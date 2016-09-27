<?php 

namespace MagentoFarsi\Mellat\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
#use Magento\Framework\View\Result\PageFactory;
#use MagentoFarsi\Mellat\Model\PaymentMethod;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use MagentoFarsi\Mellat\Gateway\Config\Config;
#use Magento\Framework\App\ObjectManager;
#use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;
use MagentoFarsi\Mellat\Gateway\Http\Client\Soap;
use MagentoFarsi\Mellat\Gateway\Http\TransferInterface;

class GetNonce extends Action
{
    const SOAP_CLIENT_URL = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';
    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    //private $resultPageFactory;
    //private $_paymentMethod;
    private $_checkoutSession;
    private $_config;
    //protected $_objectManager;
    protected $logger;
    protected $soapClient;
    //protected $transfer;
    //private $logger;

    /**
     * @param Context $context
     * @param Config $config
     * @param LoggerInterface $logger
     * @param Soap $soapClient
     * @param Session $checkoutSession
     * @internal param Soap $soap
     */
    public function __construct(
        Context $context,
        //LoggerInterface $logger,
        //PageFactory $resultPageFactory,
        //PaymentMethod $paymentMethod,
        Config $config,
        LoggerInterface $logger,
        Soap $soapClient,
        //TransferInterface $transfer,
        //ObjectManager $objectManager,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->soapClient = $soapClient;
        //$this->resultPageFactory = $resultPageFactory;
        //$this->_paymentMethod = $paymentMethod;
        $this->_config = $config;
        //$this->transfer = $transfer;
        //$this->_objectManager = $objectManager;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        //$this->_initCheckout();
        //$this->getCheckout()->place($this->getRequest()->getParam('token'));
        /*$orderId = $this->cartManagement->placeOrder($this->_checkoutSession->getQuote()->getId());
        $order = $this->orderRepository->get($orderId);
        if ($order){
            $order->setState($this->_scopeConfig->getValue('payment/mellat/order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $order->setStatus($this->_scopeConfig->getValue('payment/mellat/order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $order->save();
        }*/
        
        /*
        // prepare session to success or cancellation page
        $this->_checkoutSession->clearHelperData();

        // "last successful quote"
        $quoteId = $this->_checkoutSession->getQuote()->getId();
        $this->_checkoutSession->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

        // an order may be created
        $order = $this->getCheckout()->getOrder();
        if ($order) {
            $this->_checkoutSession->setLastOrderId($order->getId())
                ->setLastRealOrderId($order->getIncrementId())
                ->setLastOrderStatus($order->getStatus());
        }
        */
        
        /*$url = $this->_paymentMethod->getMellatCheckoutRedirection();
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($url);
        return $resultRedirect;*/
        /*try {
            $client = new \SoapClient(self::SOAP_CLIENT_URL, ['trace' => true]);
            //\Zend_Debug::dump($client);

            if (!$client) {
                $this->logger->critical($error);
                return false;
            }
            return $client;
        }
        catch (\Exception $e){
            $this->logger->critical($e);
            \Zend_Debug::dump('1234');
            throw $e;
            //return false;
        }*/
        $client = new \SoapClient(self::SOAP_CLIENT_URL, ['trace' => true]);
        if (!$client) {
            \Zend_Debug::dump('1234');
            return false;
        }

        $order = $this->getOrder();
        $grandTotal = $order->getBaseGrandTotal();
        if ($this->_config->isToomanCurrency()) {
            $grandTotal *= 10;
        }

        $params = [
            'terminalId'     => (string) $this->_config->getTerminalId(),
            'userName'       => (string) $this->_config->getUsername(),
            'userPassword'   => (string) $this->_config->getPassword(),
            'orderId'        => (int) $order->getIncrementId(),
            'amount'         => (int) number_format($grandTotal, 0, '', ''),
            'localDate'      => (string) date('Ymd'),
            'localTime'      => (string) date('His'),
            'additionalData' => '',
            'callBackUrl'    => (string) $this->_url->getUrl('mellat/checkout/return'),
            'payerId'        => 0
        ];
        $resultObj = $client->bpPayRequest($params);
        //\Zend_Debug::dump($resultObj);


        /*$response = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $model = $this->_objectManager->create('MagentoFarsi\Mellat\Model\PaymentMethod');
        $payRequest = $model->payRequest($this->getOrder());*/
        //$this->logger->info($response);

        /*$this->getResponse()->setRedirect(
            $this->paymentMethod->payRequest($this->getOrder())
        );*/
        //return $response;
    }

    protected function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
    
    
    /*public function dispatch(RequestInterface $request)
    {
        $url = $this->_paymentMethod->getMellatCheckoutRedirection();
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($url);
        return $resultRedirect;
    }*/
}


