<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoFarsi\Mellat\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
//use MagentoFarsi\Mellat\Gateway\Config\Config;

class AuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        //ConfigInterface $config
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $payment */
        $payment = $buildSubject['payment'];
        $order = $payment->getOrder();
        //$address = $order->getShippingAddress();

        $soapUrl = "https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl";
        $client = new \SoapClient($soapUrl, ['trace' => 1]);
        /*if (!$client) {
            return false;
        }*/

        $grandTotal = $order->getGrandTotalAmount();
        if ($this->config->getValue('tooman_currency')) {
            $grandTotal *= 10;
        }

        $params = [
            'terminalId'     => (string) $this->config->getValue('terminal_id'),
            'userName'       => (string) $this->config->getValue('username'),
            'userPassword'   => (string) $this->config->getValue('password'),
            'orderId'        => (int) $order->getOrderIncrementId(),
            'amount'         => (int) number_format($grandTotal, 0, '', ''),
            'localDate'      => (string) date('Ymd'),
            'localTime'      => (string) date('His'),
            'additionalData' => '',
            'callBackUrl'    => (string) '',
            'payerId'        => 0
        ];

        $resultObj = $client->bpPayRequest($params);
        //\Zend_Debug::dump($resultObj); exit('Hello!');
        $resultStr = $resultObj->return;

        if (is_numeric($resultStr) && (strlen($resultStr) <= 3)) {
            return $resultStr;
        }

        $res = explode (',',$resultStr);
        $result = [
            'ResCode' => (int)$res[0],
            'RefId'   => $res[1]
        ];
        //\Zend_Debug::dump($result);exit;

        return $result;

        /*return [
            'TXN_TYPE' => 'A',
            'INVOICE' => $order->getOrderIncrementId(),
            'AMOUNT' => $order->getGrandTotalAmount(),
            'CURRENCY' => $order->getCurrencyCode(),
            'EMAIL' => $address->getEmail(),
            'MERCHANT_KEY' => $this->config->getValue(
                'merchant_gateway_key',
                $order->getStoreId()
            )
        ];*/
    }
}
