<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoFarsi\Mellat\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        //$method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);//\Zend_Debug::dump($data); exit('hello!!');

        //$data = $this->authorization;
        //\Zend_Debug::dump($data); exit('hello!!');
        //$paymentInfo = $method->getInfoInstance();
        $paymentInfo = $this->readPaymentModelArgument($observer);

        if ($data['RefId'] !== null) {
            $paymentInfo->setAdditionalInformation(
                'RefId',
                $data['RefId']
            );
        }

        /*if ($data->getDataByKey('refId') !== null) {
            $paymentInfo->setAdditionalInformation(
                'refId',
                $data->getDataByKey('refId')
            );
        }*/
    }
}
