<?php
/**
 *

 */

namespace MagentoFarsi\Mellat\Model\Config;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Gateway\Config\ConfigFactory;

class Factory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $className
     * @param array $data
     * @return object
     */
    public function create($className, array $data = [])
    {
        return $this->_objectManager->create($className, $data);
    }
}