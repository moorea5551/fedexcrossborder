<?php

use FedExCrossBorder\Adapter\GuzzleHttpAdapter;
use FedExCrossBorder\Auth\Credentials;
use FedExCrossBorder\Tracking\Entity\CustomerInfo;
use FedExCrossBorder\Tracking\Entity\MerchantCredential;
use FedExCrossBorder\Tracking\Entity\TrackingParam;
use FedExCrossBorder\Tracking\TrackingClient;
use PHPUnit\Framework\TestCase;

class TrackingClientTest extends TestCase
{
    /**
     * @group tracking
     * @group tracking-get-widget
     */
    public function testGetWidget()
    {
        $credentials = new Credentials($_SERVER['FCB_CLIENT_ID'], $_SERVER['FCB_CLIENT_SECRET'], $_SERVER['FCB_PARTNER_KEY']);
        $guzzleAdapter = new GuzzleHttpAdapter();

        $trackingClient = new TrackingClient($credentials, $guzzleAdapter, 'https://purplepay.crossborder.fedex.com');

        $trackingParam = new TrackingParam();
        $merchantCredential = new MerchantCredential();
        $merchantCredential->setPartnerKey($_SERVER['FCB_PARTNER_KEY']);
        $customerInfo = new CustomerInfo();
        $customerInfo->setEmail('david.l.vergara@gmail.com');
        $trackingParam->setMerchantCredential($merchantCredential);
        $trackingParam->setCustomerInfo($customerInfo);
        $widget = $trackingClient->getWidget($trackingParam);

        $this->assertNotEmpty($widget);
        $this->assertContains('link', $widget);
        $this->assertContains('div', $widget);
        $this->assertContains('script', $widget);
    }

    /**
     * @group tracking
     * @group todo
     */
    public function testGetOrders()
    {
        $credentials = new Credentials($_SERVER['FCB_CLIENT_ID'], $_SERVER['FCB_CLIENT_SECRET'], $_SERVER['FCB_PARTNER_KEY']);
        $guzzleAdapter = new GuzzleHttpAdapter();

        $trackingClient = new TrackingClient($credentials, $guzzleAdapter, 'https://toolz.bongous.com');

        $tracking = $trackingClient->getOrders('sandra.sirasch@gmail.com');

        $this->assertNotEmpty($tracking);
        $this->assertJson($tracking);
    }

    /**
     * @group tracking
     * @group todo
     */
    public function testGetOrderActivities()
    {
        $credentials = new Credentials($_SERVER['FCB_CLIENT_ID'], $_SERVER['FCB_CLIENT_SECRET'], $_SERVER['FCB_PARTNER_KEY']);
        $guzzleAdapter = new GuzzleHttpAdapter();

        $trackingClient = new TrackingClient($credentials, $guzzleAdapter, 'https://toolz.bongous.com');

        $tracking = $trackingClient->getOrderActivities('338213');

        $this->assertNotEmpty($tracking);
        $this->assertJson($tracking);
    }
    /**
     * @group tracking
     * @group todo
     */
    public function testGetBoxItems()
    {
        $credentials = new Credentials($_SERVER['FCB_CLIENT_ID'], $_SERVER['FCB_CLIENT_SECRET'], $_SERVER['FCB_PARTNER_KEY']);
        $guzzleAdapter = new GuzzleHttpAdapter();

        $trackingClient = new TrackingClient($credentials, $guzzleAdapter, 'https://toolz.bongous.com');

        $tracking = $trackingClient->getBoxItems('420337169261297937924231545950');

        $this->assertNotEmpty($tracking);
        $this->assertJson($tracking);
    }
}
