<?php
/**
 *

 */
namespace MagentoFarsi\Mellat\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $serverUrl = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';

    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    public function getOrderPlaceRedirectUrl()
    {
        return $this->_urlBuilder->getUrl('mellat/payment/start');
    }
}
