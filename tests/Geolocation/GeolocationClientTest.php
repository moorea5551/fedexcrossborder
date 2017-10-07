<?php

use FedExCrossBorder\Adapter\GuzzleHttpAdapter;
use FedExCrossBorder\Auth\Credentials;
use FedExCrossBorder\Geolocation\GeolocationClient;
use PHPUnit\Framework\TestCase;

class GeolocationClientTest extends TestCase
{
    protected $credentials;
    protected $guzzleAdapter;

    /**
     * @var GeolocationClient $geolocationClient
     */
    protected $geolocationClient;

    public function setUp()
    {
        parent::setUp();

        $this->credentials = new Credentials($_SERVER['FCB_CLIENT_ID'], $_SERVER['FCB_CLIENT_SECRET'], $_SERVER['FCB_PARTNER_KEY']);

        $this->guzzleAdapter = new GuzzleHttpAdapter();

        $this->geolocationClient = new GeolocationClient($this->credentials, $this->guzzleAdapter, 'https://checkout.crossborder.fedex.com');
    }

    /**
     * @group geolocation
     */
    public function testGeolocation()
    {
        $ipAddress = '200.106.125.197';

        $countryCode = null;
        $geo = $this->geolocationClient->getCountry($ipAddress);
        if ($geo->getCountryCode() != '') {
            $countryCode = $geo->getCountryCode();
            //echo $geo->getCountryName();
            //echo $geo->getCountryCurrency();
        }


        $this->assertEquals('PE', $countryCode);
    }
}
