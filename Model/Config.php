<?php
/**
 *

 */

namespace MagentoFarsi\Mellat\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const CGI_URL = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';
    //protected $_cgiUrl = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';

    protected $_scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    public function getCgiUrl()
    {
        //return $this->_cgiUrl;
        return self::CGI_URL;
    }
}