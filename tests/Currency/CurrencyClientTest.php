<?php

use FedExCrossBorder\Adapter\GuzzleHttpAdapter;
use FedExCrossBorder\Auth\Credentials;
use FedExCrossBorder\Currency\CurrencyClient;
use PHPUnit\Framework\TestCase;

class CurrencyClientTest extends TestCase
{
    /**
     * @group currency
     */
    public function testGetExchangeRate()
    {
        $credentials = new Credentials('', '', '');
        $guzzleAdapter = new GuzzleHttpAdapter();

        $currencyClient = new CurrencyClient($credentials, $guzzleAdapter, 'https://partnertools.crossborder.fedex.com');

        $response = $currencyClient->getExchangeRate($_SERVER['FCB_PARTNER_KEY']);

        $arrResponse = json_decode($response, true);

        $this->assertTrue(is_array($arrResponse));
        $this->assertTrue(count($arrResponse)>0);
        $this->assertArrayHasKey('AED', $arrResponse);
        $this->assertArrayHasKey('USD', $arrResponse);
        $this->assertArrayHasKey('PEN', $arrResponse);
        $this->assertArrayHasKey('BTC', $arrResponse);
    }
}
