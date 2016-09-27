<?php 

namespace MagentoFarsi\Mellat\Controller\Payment;


use Magento\Framework\Controller\ResultFactory;

class Ipn extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    protected $resultPageFactory;
    protected $_scopeConfig;
    protected $_orderFactory;
    private $invoiceService;
    protected $orderSender;
    
    const PAYMENT_STATUS_PAID = 0;
    const PAYMENT_STATUS_REFUND = 4;
    const PAYMENT_STATUS_CANCEL = 2;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
        $this->_orderFactory = $orderFactory;
        $this->invoiceService = $invoiceService;
        $this->creditmemoSender = $creditmemoSender;
        $this->orderSender = $orderSender;
    }

    
    protected function _createInvoice($order)
    {
        if (!$order->canInvoice()) {
            return;
        }
        
        $invoice = $order->prepareInvoice();
        if (!$invoice->getTotalQty()) {
            throw new \RuntimeException("Cannot create an invoice without products.");
        }

        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
        $invoice->register();
        $order->addRelatedObject($invoice);
    }
    
    public function execute()
    {
        $headers = array();
        foreach ($_SERVER as $name => $value){
            if(substr($name, 0, 5) == 'HTTP_'){
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            } else if($name == "CONTENT_TYPE") {
                $headers["Content-Type"] = $value;
            } else if($name == "CONTENT_LENGTH") {
                $headers["Content-Length"] = $value;
            } else{
                $headers[$name]=$value;
            }
        }
        $headers = array_change_key_case($headers, CASE_UPPER);
        if(!isset($headers['PAYPLUG-SIGNATURE'])){
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Signature not provided', true, 403);
            die;
        }

        $signature = base64_decode($headers['PAYPLUG-SIGNATURE']);
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $status = $data['status'];
        if($status == self::PAYMENT_STATUS_PAID || $status == self::PAYMENT_STATUS_REFUND || $status == self::PAYMENT_STATUS_CANCEL){
            // Check signature
            $publicKey = $this->_scopeConfig->getValue('payment/mellat/public_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $data['custom_data']);
            $checkSignature = openssl_verify($body , $signature, $publicKey, OPENSSL_ALGO_SHA1);
            if($checkSignature == 1){
                $bool_sign = true;
            } else if($checkSignature == 0){
                echo __('Invalid signature');
                header($_SERVER['SERVER_PROTOCOL'] . ' 403 Invalid signature', true, 403);
                die;
            } else{
                echo __('Error while checking signature');
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Error while checking signature', true, 500);
                die;
            }

            if($data && $bool_sign){
                $order = $this->_orderFactory->create()->loadByIncrementId($data['order']);
                if($orderId = $order->getId()){
                    // If status paid
                    if($status == self::PAYMENT_STATUS_PAID) {
                        // If order state is already paid by payplug
                        
                        if($order->getState() == $this->_scopeConfig->getValue('payment/mellat/complete_order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $data['custom_data'])){
                            // Order is already marked as paid - return http 200 OK
                        }
                        // If order state is payment in progress by payplug
                        elseif($order->getState() == $this->_scopeConfig->getValue('payment/mellat/new_order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $data['custom_data'])){
                            
                            //$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true, __('Payment has been captured by Payment Gateway. Transaction id: %1', $data['id_transaction']));
                            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                                ->setStatus($order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_PROCESSING));
                            $order->addStatusToHistory(\Magento\Sales\Model\Order::STATE_PROCESSING, __('Payment has been captured by Payment Gateway. Transaction id: %1', $data['id_transaction']));
                            // save transaction ID
                            $order->getPayment()->setLastTransId($data['id_transaction']);
                            // send new order email
                            //$order->sendNewOrderEmail();
                            $this->orderSender->send($order);
                            //$order->setEmailSent(true);

                            if ($this->_scopeConfig->getValue('payment/mellat/invoice', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $data['custom_data'])){
                                $this->_createInvoice($order);
                            }

                            $order->save();

                        }
                    } // If status refund
                    else if($status == self::PAYMENT_STATUS_CANCEL){
                        $order->cancel();
                        $order->addStatusToHistory(\Magento\Sales\Model\Order::STATE_CANCELED, __('Payment canceled by Payment Gateway. Transaction id: %1', $data['id_transaction']));
                        $order->save();
                    }
                    else if($status == self::PAYMENT_STATUS_REFUND){
                        $payment = $order->getPayment()->setPreparedMessage(
                            __('Payment refunded by Payment Gateway.')
                        )->setTransactionId(
                            $data['id_transaction']
                        )->setParentTransactionId(
                            $order->getPayment()->getLastTransId()
                        )->setIsTransactionClosed(
                            true
                        )->registerRefundNotification(
                            -1 * $order->getBaseGrandTotal()
                        );
                        $order->save();

                        // TODO: there is no way to close a capture right now
                        $creditMemo = $payment->getCreatedCreditmemo();
                        if ($creditMemo) {
                            $this->creditmemoSender->send($creditMemo);
                            $order->addStatusHistoryComment(
                                __('You notified customer about creditmemo #%1.', $creditMemo->getIncrementId())
                            )->setIsCustomerNotified(
                                true
                            )->save();
                        }
                        
                        $order->setState($this->_scopeConfig->getValue('payment/mellat/cancel_order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $data['custom_data']));
                        $order->setStatus($this->_scopeConfig->getValue('payment/mellat/cancel_order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $data['custom_data']));
                        $order->addStatusToHistory(\Magento\Sales\Model\Order::STATE_CLOSED, __('Payment refunded by Payment Gateway. Transaction id: %1', $data['id_transaction']));
                        $order->save();
                        
                        /*
                        $invoices = [];
                        foreach ($order->getInvoiceCollection() as $invoice) {
                            if ($invoice->canRefund()) {
                                $invoices[] = $invoice;
                            }
                        }
                        //$service = $this->invoiceService->prepareInvoice($order);
                        $service = Mage::getModel('sales/service_order', $order);
                        foreach ($invoices as $invoice) {
                            $creditmemo = $service->prepareInvoiceCreditmemo($invoice);
                            $creditmemo->refund();
                            $creditmemo->getInvoice()->save();
                            $creditmemo->save();
                        }
                        $order->setState($this->_scopeConfig->getValue('payment/mellat/cancel_order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $data['custom_data']));
                        $order->setStatus($this->_scopeConfig->getValue('payment/mellat/cancel_order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $data['custom_data']));
                        $order->addStatusToHistory(\Magento\Sales\Model\Order::STATE_CLOSED, Mage::helper('mellat')->__('Payment refunded by Payment Gateway. Transaction id: %s', $data['id_transaction']));
                        $order->save();
                        */
                    }
                }
            } else{
                echo __('Error: missing or wrong parameters.');
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Missing or wrong parameters', true, 400);
                die;
            }
        }
    }
}


