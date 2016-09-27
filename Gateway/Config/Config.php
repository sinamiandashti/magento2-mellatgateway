<?php
/**
 *

 */
namespace MagentoFarsi\Mellat\Gateway\Config;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    const CGI_URL = 'cgi_url';
    const TRANSACTION_URL = 'wsdl';
    const KEY_TERMINAL_ID = 'terminal_id';
    const KEY_USERNAME = 'username';
    const KEY_PASSWORD = 'password';
    const KEY_TOOMAN_CURRENCY = 'tooman_currency';
    const KEY_LOGO = 'logo';
    const KEY_ACTIVE = 'active';

    public function getCgiUrl()
    {
        return $this->getValue(self::CGI_URL);
    }

    public function getTransactionUrl()
    {
        return $this->getValue(self::TRANSACTION_URL);
    }

    public function getTerminalId()
    {
        return $this->getValue(self::KEY_TERMINAL_ID);
    }

    public function getUsername()
    {
        return $this->getValue(self::KEY_USERNAME);
    }

    public function getPassword()
    {
        return $this->getValue(self::KEY_PASSWORD);
    }

    public function isToomanCurrency()
    {
        return (bool) $this->getValue(self::KEY_TOOMAN_CURRENCY);
    }

    public function isActive()
    {
        return (bool) $this->getValue(self::KEY_ACTIVE);
    }
}