<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoFarsi\Mellat\Gateway\Http\Client;

use MagentoFarsi\Mellat\Gateway\Request\CaptureDataBuilder;
use MagentoFarsi\Mellat\Gateway\Request\PaymentDataBuilder;

/**
 * Class TransactionSettlement
 */
class TransactionSubmitForSettlement extends AbstractTransaction
{
    /**
     * @inheritdoc
     */
    protected function process(array $data)
    {
        return  $this->adapter->submitForSettlement(
            $data[CaptureDataBuilder::TRANSACTION_ID],
            $data[PaymentDataBuilder::AMOUNT]
        );
    }
}
