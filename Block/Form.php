<?php
/**
 *

 */
namespace MagentoFarsi\Mellat\Block;

class Form extends \Magento\Payment\Block\Form
{
    /**
     * Checkmo template
     *
     * @var string
     */
    protected $_supportedInfoLocales = array('en');
    protected $_defaultInfoLocale = 'fa';
    
    protected $_template = 'MagentoFarsi_Mellat::form.phtml';
}
