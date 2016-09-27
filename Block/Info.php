<?php
/**
 *

 */
namespace MagentoFarsi\Mellat\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;
use MagentoFarsi\Mellat\Gateway\Response\FraudHandler;

class Info extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_payableTo;

    /**
     * @var string
     */
    protected $_mailingAddress;

    /**
     * @var string
     */
    protected $_template = 'MagentoFarsi_Mellat::info.phtml';

    
    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('MagentoFarsi_Mellat::pdf/info.phtml');
        return $this->toHtml();
    }
}
