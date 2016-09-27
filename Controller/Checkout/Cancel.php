<?php 

namespace MagentoFarsi\Mellat\Controller\Checkout;

use Magento\Framework\App\Action\Action;
#use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
#use Magento\Customer\Model\Session;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Checkout\Model\Session;


class Cancel extends Action
{
    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    #protected $resultPageFactory;
    protected $orderRepository;
    protected $checkoutSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param Session|\Magento\Customer\Model\Session $customerSession
     * @param OrderRepositoryInterface $orderRepository
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        OrderRepositoryInterface $orderRepository,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->orderRepository = $orderRepository;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $this->messageManager->addError(__('Payment has been cancelled.'));
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        //change order status to cancel
        $order = $this->orderRepository->get($this->checkoutSession->getLastOrderId());
        if ($order) {
            $order->cancel();
            $order->addStatusToHistory(\Magento\Sales\Model\Order::STATE_CANCELED, __('Canceled by customer'));
            $order->save();
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('checkout/cart');
        return $resultRedirect;
    }
}


