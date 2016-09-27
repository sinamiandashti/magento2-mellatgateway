<?php
/**
 *

 */
namespace MagentoFarsi\Mellat\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use MagentoFarsi\Mellat\Gateway\Config\Config;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use MagentoFarsi\Mellat\Model\Adapter\MellatAdapter;

#use MagentoFarsi\Mellat\Model\PaymentMethod;
#use Magento\Framework\Locale\ResolverInterface;
#use Magento\Customer\Helper\Session\CurrentCustomer;
#use Magento\Paypal\Model\ConfigFactory;
#use MagentoFarsi\Mellat\Model\Config\Factory;
#use Magento\Checkout\Model\Session;
#use MagentoFarsi\Mellat\Gateway\Http\Client\ClientMock;

final class MellatConfigProvider implements ConfigProviderInterface
{
    const CODE = 'mellat';

    //protected $localeResolver;
    private $config;
    private $paymentHelper;
    private $_storeManager;
    private $adapter;
    //private $paymentMethod;
    //protected $redirectUrl;
    //protected $currentCustomer;
    //protected $methods = [];
    //protected $checkoutSession;

    public function __construct(
        Config $config,
        PaymentHelper $paymentHelper,
        StoreManagerInterface $storeManager,
        MellatAdapter $adapter
        //PaymentMethod $paymentMethod
        //ResolverInterface $localeResolver,
        //CurrentCustomer $currentCustomer,
        //Session $checkoutSession,
        //Config $config
    ) {
        //$this->localeResolver = $localeResolver;
        $this->config = $config;
        $this->paymentHelper = $paymentHelper;
        $this->_storeManager = $storeManager;
        $this->adapter = $adapter;
        //$this->paymentMethod = $paymentMethod;
        //$this->currentCustomer = $currentCustomer;
        //$this->checkoutSession = $checkoutSession;
        //$this->config = $config;

        //$this->methods[self::CODE] = $this->paymentHelper->getMethodInstance(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        $payment = $this->paymentHelper->getMethodInstance(self::CODE);
        $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        if ($payment->isAvailable()) {
            $config = [
                'payment' => [
                    self::CODE => [
                        'isActive'                 => $this->config->isActive(),
                        'cgiUrl'                   => $this->config->getCgiUrl(),
                        'redirectUrl'              => $this->getMethodRedirectUrl(),
                        'terminalId'               => $this->config->getTerminalId(),
                        'username'                 => $this->config->getUsername(),
                        'password'                 => $this->config->getPassword(),
                        'paymentAcceptanceMarkSrc' => $imageUrl.'magentofarsi/mellat/mellat.png'
                    ]
                ]
            ];
        }
        return $config;
    }

        /*if ($this->methods[self::CODE]->isAvailable()) {
          //  $config['payment']['mellat']['redirectUrl'][self::CODE] = $this->_getMethodRedirectUrl(self::CODE);
            $config['payment']['mellat']['redirectUrl'] = $this->_getMethodRedirectUrl(self::CODE);
            'paymentAcceptanceMarkSrc' => $this->config->getPaymentMarkImageUrl(
                $locale
            )
            'paymentAcceptanceMarkSrc' => "http://".$_SERVER['SERVER_NAME']."/pub/static/frontend/Magento/luma/fa_IR/MagentoFarsi_Mellat/image.png";
        }*/

    /**
     * Return redirect URL for method
     * @return mixed
     * @internal param string $code
     */
    /*protected function getMethodRedirectUrl()
    {
        return $this->methods[self::CODE]->getOrderPlaceRedirectUrl();
    }*/

    /**
     * Return redirect URL for method
     * @return mixed
     * @internal param string $code
     */
    /*protected function _getMethodRedirectUrl($code)
    {
        return $this->methods[$code]->getOrderPlaceRedirectUrl();
    }*/

    public function getMethodRedirectUrl()
    {
        return $this->adapter->generate();

    }
}
