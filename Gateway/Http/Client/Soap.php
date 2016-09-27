<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoFarsi\Mellat\Gateway\Http\Client;

use Magento\Framework\Webapi\Soap\ClientFactory;
use Magento\Payment\Gateway\Http\ConverterInterface;
use MagentoFarsi\Mellat\Gateway\Http\ClientInterface;
use MagentoFarsi\Mellat\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;

/**
 * Class Soap
 * @package MagentoFarsi\Mellat\Gateway\Http\Client
 * @api
 */
class Soap implements ClientInterface
{
    const SOAP_CLIENT_URL = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ConverterInterface | null
     */
    private $converter;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @param Logger $logger
     * @param ClientFactory $clientFactory
     * @param ConverterInterface | null $converter
     */
    public function __construct(
        Logger $logger,
        ClientFactory $clientFactory,
        ConverterInterface $converter = null
    ) {
        $this->logger = $logger;
        $this->converter = $converter;
        $this->clientFactory = $clientFactory;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     * @throws \Exception
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        //$this->logger->debug(['request' => $transferObject->getBody()]);

        /*try {
            $client->__setSoapHeaders($transferObject->getHeaders());

            $response = $client->__soapCall(
                $transferObject->getMethod(),
                [$transferObject->getBody()]
            );

            $result = $this->converter
                ? $this->converter->convert(
                    $response
                )
                : [$response];

            $this->logger->debug(['response' => $result]);
        } catch (\Exception $e) {
            $this->logger->debug(['trace' => $client->__getLastRequest()]);
            throw $e;
        }

        return $result;*/
        \Zend_Debug::dump($transferObject->getClientConfig()['wsdl']);exit;
        try {
            //$client = new \SoapClient(self::SOAP_CLIENT_URL, ['trace' => true]);

            $client = $this->clientFactory->create(
                //$transferObject->getClientConfig()['wsdl'],
                self::SOAP_CLIENT_URL,
                ['trace' => true]
            );

            $client->__setSoapHeaders($transferObject->getHeaders());

            $response = $client->__soapCall(
                $transferObject->getMethod(),
                [$transferObject->getBody()]
            );

            if (!$client) {
                $this->logger->debug([$client->getError()]);
                //return false;
            }
        }
        catch (\Exception $e){
            $this->logger->debug([$e->fault]);
            \Zend_Debug::dump('1234');
            throw $e;
            //return false;
        }

        return $client;
    }
}
