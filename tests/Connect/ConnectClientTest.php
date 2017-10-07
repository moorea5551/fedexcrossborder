<?php

use FedExCrossBorder\Connect\ConnectClient;
use PHPUnit\Framework\TestCase;

class ConnectClientTest extends TestCase
{

    /**
     * @var ConnectClient $connectClient
     */
    protected $connectClient;

    public function setUp()
    {
        parent::setUp();

        $this->connectClient = new ConnectClient('https://api.crossborder.fedex.com/services/v43?wsdl');
    }

    /**
     * @group connect
     * @group connect-product-info
     */
    public function testConnectProductInfo()
    {
        $params =
            [
                'partnerKey' => $_SERVER['FCB_PARTNER_KEY'],
                'language' => 'en',
                'items' =>
                    [
                        [
                            'productID' => 'gs1139530101',
                            'description' => 'Jeanneret Women\'s Austen White Leather Stainless Steel Austrian Crystal Accented Dial Watch',
                            'url' => 'http://dev.magento4.com/index.php/jeanneret-women-s-austen-white-leather-stainless-steel-austrian-crystal-accented-dial-watch.html',
                            'imageUrl' => 'http://dev.magento4.com/media/catalog/product/cache/0/image/265x/9df78eab33525d08d6e5fb8d27136e95/b/9/b9f87f04869ee3cc09de3ff4b36a7fdf.jpg',
                            'price' => 300.0000,
                            'countryOfOrigin' => 'US',
                            'hsCode' => '',
                            'eccn' => '',
                            'hazFlag' => '',
                            'licenseFlag' => '',
                            'importFlag' => '',
                            'productType' => '',
                            'itemInformation' =>
                                [
                                    [
                                        'l' => '',
                                        'w' => '',
                                        'h' => '',
                                        'wt' => 0.5000
                                    ]
                                ]
                        ]
                    ]
            ];

        $response = $this->connectClient->connectProductInfo($params);

        $this->assertEquals(0, $response->error);
        $this->assertEquals('', $response->errorMessage);
        $this->assertEquals('', $response->errorMessageDetail);
    }

    /**
     * @group connect
     * @group connect-sku-status
     */
    public function testConnectSkuStatus()
    {
        $skus = [
            ['productID' => 'gs1139530101'],
            ['productID' => 'gs11395301056'],
        ];

        $params = (object) array(
            'partnerKey' => $_SERVER['FCB_PARTNER_KEY'],
            'language' => 'en',
            'items' => $skus
        );

        $response = $this->connectClient->ConnectSkuStatus($params);

        $this->assertEquals(0, $response->error);
        $this->assertEquals('', $response->errorMessage);
        $this->assertEquals(2, count($response->items));
        $this->assertTrue( is_int($response->items[0]->skuHsCode));
        $this->assertTrue( is_int($response->items[0]->productStatus));
    }
}
