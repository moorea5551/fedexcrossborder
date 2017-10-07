<?php

use FedExCrossBorder\Adapter\GuzzleHttpAdapter;
use FedExCrossBorder\Checkout\CheckoutClient;
use FedExCrossBorder\Checkout\Entity\Address;
use FedExCrossBorder\Checkout\Entity\Cart;
use FedExCrossBorder\Checkout\Entity\CustomerAttributes;
use FedExCrossBorder\Checkout\Entity\Product;
use FedExCrossBorder\Checkout\Entity\ShippingMethodOption;
use FedExCrossBorder\Checkout\Entity\MerchantShippingMethod;
use FedExCrossBorder\Auth\OAuthClient;
use FedExCrossBorder\Auth\Credentials;
use FedExCrossBorder\Exception\HttpException;
use PHPUnit\Framework\TestCase;

class CheckoutTest extends TestCase
{
    /**
     * @var CheckoutClient $checkoutClientWithCredentials
     */
    protected $checkoutClientWithCredentials;

    /**
     * @var CheckoutClient $checkoutClientWithBearer
     */
    protected $checkoutClientWithBearer;

    /**
     * @var OAuthClient $oauthClient
     */
    protected $oauthClient;

    public function setUp()
    {
        parent::setUp();

        // Building a Client with Basic Auth - Credentials are sent on each request
        $credentials = new Credentials($_SERVER['FCB_CLIENT_ID'], $_SERVER['FCB_CLIENT_SECRET'], $_SERVER['FCB_PARTNER_KEY']);
        $this->checkoutClientWithCredentials = new CheckoutClient(CheckoutClient::BASE_URL, new GuzzleHttpAdapter(), $credentials);

        // Building an Oauth Client
        $credentials = new Credentials($_SERVER['FCB_CLIENT_ID'], $_SERVER['FCB_CLIENT_SECRET'], $_SERVER['FCB_PARTNER_KEY']);
        $this->oauthClient = new OAuthClient($credentials);

        // Bearer Token
        try{
            $access_token = $this->oauthClient->clientCredentials();
            $bearer_token = $access_token->getAccessToken();
        }catch (HttpException $e) {
            echo 'Unable to get Oauth Client Credentials, please check your API credentials.' . PHP_EOL;
            $bearer_token = '';
        }

        // Building a Client with Bearer Token - Credentials are used only once, and the Bearer Token is sent on each request
        $this->checkoutClientWithBearer = new CheckoutClient();
        $this->checkoutClientWithBearer->setAccessToken($bearer_token);
        $this->checkoutClientWithBearer->setPartnerKey($_SERVER['FCB_PARTNER_KEY']);
    }

    /**
     * @group checkout
     * @group create-cart
     */
    public function testCreateCheckoutWithEmptyCart()
    {
        $cart = new Cart();
        $checkout = $this->checkoutClientWithCredentials->createCheckout($cart);
        $this->assertNotEmpty($checkout->getCheckoutId());
    }

    /**
     * @group checkout
     * @group create-cart
     */
    public function testCreateCheckoutWithEmptyCartBearer()
    {
        $cart = new Cart();
        $checkout = $this->checkoutClientWithBearer->createCheckout($cart);
        $this->assertNotEmpty($checkout->getCheckoutId());
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group create-cart
     */
    public function testCreateCart($cartData)
    {
        $cart = new Cart($cartData);
        $checkout = $this->checkoutClientWithCredentials->createCheckout($cart);

        $this->assertNotEmpty($checkout->getCheckoutId());
    }

    /**
     * @group checkout
     * @group create-cart
     */
    public function testCreateCartStepByStep()
    {
        $cart = new Cart();

        // Setting some Shipping Address
        $cart->setShippingAddress(
            new Address([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'company' => 'My Biz',
                'address_1' => '10-123 1/2 MAIN STREET NW',
                'address_2' => 'MONTREAL QC H3Z 2Y7',
                'city' => 'Montreal',
                'country' => 'CA',
                'state' => 'Quebec',
                'zip' => 'H3Z 2Y7',
                'phone' => '1-514-888-9999',
                'email' => 'john.doe@mybiz.com',
                'custom_1' => 'Custom Value 1',
                'custom_2' => 'Custom Value 2',
                'custom_3' => 'Custom Value 3',
                'tax_id' => '123456789',
                ]
            )
        );
        
        // Billing and Shipping Address can be the same
        $cart->setBillingAddress($cart->getShippingAddress());

        // Set the Merchant currency to USD
        $cart->setMerchantCurrency('USD');

        // Adding a single product
        $cart->addProduct(
            new Product([
                'id' => '1766738',
                'name' => '576 #2 Pencils',
                'quantity' => 1,
                'price' => '37.25',
                'shipping' => '0.00',
                'country_of_origin' => 'US',
                'distribution_country' => 'US',
                'distribution_zip' => '',
                'product_transit' => 3,
                'custom_1' => '',
                'custom_2' => '',
                'custom_3' => '',
                ]
            )
        );

        // International Standard Shipping Method
        $cart->setShippingMethod(new ShippingMethodOption(ShippingMethodOption::INTERNATIONAL_STANDARD));

        $checkout = $this->checkoutClientWithCredentials->createCheckout($cart);

        $this->assertNotEmpty($checkout->getCheckoutId());
        $this->assertNotEmpty($checkout->getTotals()->getTotal());
        $this->assertNotEmpty($checkout->getTotals()->getCurrency());
        $this->assertEquals($checkout->getCart()->getShippingMethod()->getCode(), ShippingMethodOption::INTERNATIONAL_STANDARD);
    }

    /**
     * @group checkout
     * @group create-cart
     * @group todo
     */
    public function testCreateCartMerchantDirectStepByStep()
    {
        $cart = new Cart();

        // Order is Merchant Direct
        $cart->setMerchantDirect(true);

        // Setting some Shipping Address
        $cart->setShippingAddress(
            new Address([
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'company' => 'My Biz',
                    'address_1' => '10-123 1/2 MAIN STREET NW',
                    'address_2' => 'MONTREAL QC H3Z 2Y7',
                    'city' => 'Montreal',
                    'country' => 'CA',
                    'state' => 'Quebec',
                    'zip' => 'H3Z 2Y7',
                    'phone' => '1-514-888-9999',
                    'email' => 'john.doe@mybiz.com',
                    'custom_1' => 'Custom Value 1',
                    'custom_2' => 'Custom Value 2',
                    'custom_3' => 'Custom Value 3',
                    'tax_id' => '123456789',
                ]
            )
        );

        // Billing and Shipping Address can be the same
        $cart->setBillingAddress($cart->getShippingAddress());

        // Set the Merchant currency to USD
        $cart->setMerchantCurrency('USD');

        // Adding a single product
        $cart->addProduct(
            new Product([
                    'id' => '1766738',
                    'name' => '576 #2 Pencils',
                    'quantity' => 1,
                    'price' => '37.25',
                    'shipping' => '0.00',
                    'country_of_origin' => 'US',
                    'distribution_country' => 'US',
                    'distribution_zip' => '',
                    'product_transit' => 3,
                    'custom_1' => '',
                    'custom_2' => '',
                    'custom_3' => '',
                ]
            )
        );

        // Set Merchant Direct Shipping Methods
        $cart->setMerchantShippingMethods([
            new MerchantShippingMethod([
                "code" => 100,
                "name" => "Merchant Economy",
                "transit_time" => 9,
                "dutiable_transit_cost" => 50,
                "charged_transit_cost" => 75,
                "service_level_category" => 7,
                "sort_index" => 1
                ]
            ),
            new MerchantShippingMethod([
                "code" => 101,
                "name" => "Merchant Standard",
                "transit_time" => 6,
                "delivery_date" => "2017-03-17",
                "dutiable_transit_cost" => 100,
                "service_level_category" => 8,
                "sort_index" => 2
                ]
            ),
            new MerchantShippingMethod([
                "code" => 102,
                "name" => "Merchant Express",
                "transit_time" => 3,
                "dutiable_transit_cost" => 100,
                "charged_transit_cost" => 200,
                "charged_transit_cost_message" => "10 Discount` Message",
                "service_level_category" => 7,
                "sort_index" => 3
                ]
            ),
            new MerchantShippingMethod([
                "code" => 103,
                "name" => "Merchant Super Express",
                "transit_time" => 3,
                "dutiable_transit_cost" => 200,
                "charged_transit_cost" => 400,
                "charged_transit_cost_message" => "10 Discount` Message",
                "service_level_category" => 5,
                "sort_index" => 4
                ]
            )
        ]);

        // Set a custom Merchant Shipping Method
        $cart->setShippingMethod(new ShippingMethodOption(102));

        $checkout = $this->checkoutClientWithCredentials->createCheckout($cart);

        $this->assertNotEmpty($checkout->getCheckoutId());
        $this->assertNotEmpty($checkout->getTotals()->getTotal());
        $this->assertNotEmpty($checkout->getTotals()->getCurrency());
        $this->assertTrue($checkout->getCart()->isMerchantDirect());
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group get-checkout
     */
    public function testGetCheckout($cartData)
    {
        $cart = new Cart($cartData);
        $checkout = $this->checkoutClientWithCredentials->createCheckout($cart);
        $checkout = $this->checkoutClientWithCredentials->getCheckout($checkout->getCheckoutId());

        $this->assertNotEmpty($checkout->getCheckoutId());
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group add-update-delete-item
     */
    public function testAddUpdateDeleteItem($cartData)
    {
        $cart = new Cart($cartData);

        $checkout = $this->checkoutClientWithCredentials->createCheckout($cart);

        $product = new Product();
        $product->setId("1766739");
        $product->setName("576 #2 Pencils");
        $product->setPrice("37.25");
        $product->setQuantity(1);
        $product->setShipping("10.00");

        $checkout = $this->checkoutClientWithCredentials->addItem($checkout->getCheckoutId(), $product);

        $this->assertEquals(2, count($checkout->getCart()->getProducts()));

        $product2  = $checkout->getCart()->getProducts()[1];
        $product2->setQuantity(2);
        $checkout = $this->checkoutClientWithCredentials->updateItem($checkout->getCheckoutId(), $product2);

        $this->assertEquals(2, $checkout->getCart()->getProducts()[1]->getQuantity());

        $product2  = $checkout->getCart()->getProducts()[1];
        $this->checkoutClientWithCredentials->deleteItem($checkout->getCheckoutId(), $product2->getId());
        $checkout = $this->checkoutClientWithCredentials->getCheckout($checkout->getCheckoutId());

        $this->assertEquals(1, count($checkout->getCart()->getProducts()));
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group update-shipping-address
     */
    public function testUpdateShippingAddress($cartData)
    {
        $cart = new Cart($cartData);
        $firstCheckout = $this->checkoutClientWithCredentials->createCheckout($cart);
        $shippingAddress = new Address();
        $shippingAddress
            ->setFirstName("Juan Carlos")
            ->setLastName("Fernandez")
            ->setCompany("otra")
            ->setAddress1("loas alphes")
            ->setAddress2("los aplhes 2")
            ->setCity("New York")
            ->setCountry("US")
            ->setState("Alaska")
            ->setZip("99812")
            ->setPhone("2345678")
            ->setEmail("jludena@idigital.pe")
            ->setCustom1("custom1")
            ->setCustom2("custom2")
            ->setCustom3("custom3")
            ->setTaxId("taxid")
        ;
        $secondCheckout = $this->checkoutClientWithCredentials->updateShippingAddress($firstCheckout->getCheckoutId(), $shippingAddress);

        $this->assertEquals('Ludeña', $firstCheckout->getCart()->getShippingAddress()->getLastName());
        $this->assertEquals('Fernandez', $secondCheckout->getCart()->getShippingAddress()->getLastName());
        $this->assertEquals('otra', $secondCheckout->getCart()->getShippingAddress()->getCompany());
        $this->assertEquals('loas alphes', $secondCheckout->getCart()->getShippingAddress()->getAddress1());
        $this->assertEquals('los aplhes 2', $secondCheckout->getCart()->getShippingAddress()->getAddress2());
        $this->assertEquals('New York', $secondCheckout->getCart()->getShippingAddress()->getCity());
        $this->assertEquals('US', $secondCheckout->getCart()->getShippingAddress()->getCountry());
        $this->assertEquals('Alaska', $secondCheckout->getCart()->getShippingAddress()->getState());
        $this->assertEquals('99812', $secondCheckout->getCart()->getShippingAddress()->getZip());
        $this->assertEquals('2345678', $secondCheckout->getCart()->getShippingAddress()->getPhone());
        $this->assertEquals('jludena@idigital.pe', $secondCheckout->getCart()->getShippingAddress()->getEmail());
        $this->assertEquals('custom1', $secondCheckout->getCart()->getShippingAddress()->getCustom1());
        $this->assertEquals('custom2', $secondCheckout->getCart()->getShippingAddress()->getCustom2());
        $this->assertEquals('custom3', $secondCheckout->getCart()->getShippingAddress()->getCustom3());
        $this->assertEquals('taxid', $secondCheckout->getCart()->getShippingAddress()->getTaxId());
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group update-billing-address
     */
    public function testUpdateBillingAddress($cartData)
    {
        $cart = new Cart($cartData);
        $firstCheckout = $this->checkoutClientWithCredentials->createCheckout($cart);
        $billingAddress = new Address();
        $billingAddress
            ->setFirstName("Juan Carlos")
            ->setLastName("Fernandez")
            ->setCompany("otra")
            ->setAddress1("loas alphes")
            ->setAddress2("los aplhes 2")
            ->setCity("New York")
            ->setCountry("US")
            ->setState("Alaska")
            ->setZip("99812")
            ->setPhone("2345678")
            ->setEmail("jludena@idigital.pe")
            ->setCustom1("custom1")
            ->setCustom2("custom2")
            ->setCustom3("custom3")
            ->setTaxId("taxid")
        ;
        $secondCheckout = $this->checkoutClientWithCredentials->updateBillingAddress($firstCheckout->getCheckoutId(), $billingAddress);

        $this->assertEquals('Ludeña', $firstCheckout->getCart()->getBillingAddress()->getLastName());
        $this->assertEquals('Fernandez', $secondCheckout->getCart()->getBillingAddress()->getLastName());
        $this->assertEquals('otra', $secondCheckout->getCart()->getBillingAddress()->getCompany());
        $this->assertEquals('loas alphes', $secondCheckout->getCart()->getBillingAddress()->getAddress1());
        $this->assertEquals('los aplhes 2', $secondCheckout->getCart()->getBillingAddress()->getAddress2());
        $this->assertEquals('New York', $secondCheckout->getCart()->getBillingAddress()->getCity());
        $this->assertEquals('US', $secondCheckout->getCart()->getBillingAddress()->getCountry());
        $this->assertEquals('Alaska', $secondCheckout->getCart()->getBillingAddress()->getState());
        $this->assertEquals('99812', $secondCheckout->getCart()->getBillingAddress()->getZip());
        $this->assertEquals('2345678', $secondCheckout->getCart()->getBillingAddress()->getPhone());
        $this->assertEquals('jludena@idigital.pe', $secondCheckout->getCart()->getBillingAddress()->getEmail());
        $this->assertEquals('custom1', $secondCheckout->getCart()->getBillingAddress()->getCustom1());
        $this->assertEquals('custom2', $secondCheckout->getCart()->getBillingAddress()->getCustom2());
        $this->assertEquals('custom3', $secondCheckout->getCart()->getBillingAddress()->getCustom3());
        $this->assertEquals('taxid', $secondCheckout->getCart()->getBillingAddress()->getTaxId());
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group update-shipping-method
     */
    public function testUpdateShippingMethod($cartData)
    {
        $cart = new Cart($cartData);
        $initialCheckout = $this->checkoutClientWithCredentials->createCheckout($cart);
        $shippingMethodOption = new ShippingMethodOption();
        $shippingMethodOption
            ->setCode(0)
        ;
        $lastCheckout = $this->checkoutClientWithCredentials->updateShippingMethod($initialCheckout->getCheckoutId(), $shippingMethodOption);

        $this->assertEquals(0, $lastCheckout->getCart()->getShippingMethod()->getCode());
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group pay
     * @group todo
     */
    public function testPayCart($cartData)
    {
        $cart = new Cart($cartData);
        $checkout = $this->checkoutClientWithCredentials->createCheckout($cart);

        $success = $this->checkoutClientWithCredentials->pay($checkout->getCheckoutId(), $this->payWithAmexProvider()[0][0]);

        $this->assertEquals('OK', $success['status']);
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group update-customer-attributes
     */
    public function testUpdateCustomerAttributes($cartData)
    {
        $cart = new Cart($cartData);
        $initialCheckout = $this->checkoutClientWithCredentials->createCheckout($cart);
        $customerAttributes = new CustomerAttributes();
        $customerAttributes
            ->setLocale("en_US")
            ->setIp("190.233.97.200")
            ->setTimezone("America/New_York")
        ;
        $lastCheckout = $this->checkoutClientWithCredentials->updateCustomerAttributes($initialCheckout->getCheckoutId(), $customerAttributes);

        $this->assertEquals('en_US', $lastCheckout->getCart()->getCustomerAttributes()->getLocale());
        $this->assertEquals('190.233.97.200', $lastCheckout->getCart()->getCustomerAttributes()->getIp());
        $this->assertEquals('America/New_York', $lastCheckout->getCart()->getCustomerAttributes()->getTimezone());
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group get-available-shipping-methods
     */
    public function testGetAvailableShippingMethods($cartData)
    {
        $cart = new Cart($cartData);
        $checkout = $this->checkoutClientWithCredentials->createCheckout($cart);

        $availableShippingMethods = $this->checkoutClientWithCredentials->getAvailableShippingMethods($checkout->getCheckoutId());

        $this->assertGreaterThan(0, count($availableShippingMethods->getAvailableShippingMethods()));
        $this->assertTrue(is_int($availableShippingMethods->getAvailableShippingMethods()[0]->getCode()));
        $this->assertNotEmpty($availableShippingMethods->getAvailableShippingMethods()[0]->getName());
        $this->assertNotEmpty($availableShippingMethods->getAvailableShippingMethods()[0]->getAmount());
        $this->assertNotEmpty($availableShippingMethods->getAvailableShippingMethods()[0]->getDeliveryDate());
    }

    /**
     * @dataProvider fullCartProvider
     * @group checkout
     * @group get-available-payment-methods
     */
    public function testGetAvailablePaymentMethods($cartData)
    {
        $cart = new Cart($cartData);
        $checkout = $this->checkoutClientWithCredentials->createCheckout($cart);

        $availablePaymentMethods = $this->checkoutClientWithCredentials->getAvailablePaymentMethods($checkout->getCheckoutId());

        $this->assertGreaterThan(0, count($availablePaymentMethods->getAvailablePaymentMethods()));
        $this->assertNotEmpty($availablePaymentMethods->getAvailablePaymentMethods()[0]->getCode());
        $this->assertNotEmpty($availablePaymentMethods->getAvailablePaymentMethods()[0]->getName());
        $this->assertNotEmpty($availablePaymentMethods->getAvailablePaymentMethods()[0]->getType());
    }

    /**
     * @group checkout
     * @group get-available-countries
     */
    public function testGetAvailableCountries()
    {
        $availableCountries = $this->checkoutClientWithCredentials->getAvailableCountries();

        $this->assertNotEmpty($availableCountries->getAvailableBillingCountries());
        $this->assertNotEmpty($availableCountries->getAvailableShippingCountries());
    }

    /**
     * @group checkout
     * @group get-available-languages
     */
    public function testGetAvailableLanguages()
    {
        $availableLanguages = $this->checkoutClientWithCredentials->getAvailableLanguages();

        $this->assertNotEmpty($availableLanguages->getAvailableLanguages());
    }

    public function fullCartProvider()
    {
        return [
            [
                [
                    'shipping_address' => [
                        'first_name' => 'Juan Carlos',
                        'last_name' => 'Ludeña',
                        'company' => 'otra',
                        'address_1' => 'loas alphes',
                        'address_2' => 'los aplhes 2',
                        'city' => 'New York',
                        'country' => 'PE',
                        'state' => 'Alaska',
                        'zip' => '99812',
                        'phone' => '2345678',
                        'email' => 'jludena@idigital.pe',
                        'custom_1' => '',
                        'custom_2' => '',
                        'custom_3' => '',
                        'tax_id' => ''
                    ],
                    'billing_address' => [
                        'first_name' => 'Juan Carlos',
                        'last_name' => 'Ludeña',
                        'company' => 'otra',
                        'address_1' => 'loas alphes',
                        'address_2' => 'los aplhes 2',
                        'city' => 'New York',
                        'country' => 'EC',
                        'state' => 'Alaska',
                        'zip' => '99812',
                        'phone' => '2345678',
                        'email' => 'jludena@idigital.pe',
                        'custom_1' => '',
                        'custom_2' => '',
                        'custom_3' => '',
                        'tax_id' => ''
                    ],
                    'domestic_shipping_charge' => '1000.00',
                    'shipping_method' => [
                        "code" => 1
                    ],
                    'merchant_currency' => 'USD',
                    'charge_insurance' => true,
                    'products' => [
                        [
                            'id' => '1766738',
                            'name' => '576 #2 Pencils',
                            'price' => '37.25',
                            'quantity' => 1,
                            'shipping' => '10.00',
                            "retail_price" => 98.49
                        ]
                    ],
                    'customer_attributes' => [
                        'locale' => 'en_US',
                        'timezone' => 'America/New_York',
                        'ip' => '190.233.97.170'
                    ]
                ]
            ]
        ];
    }

    public function payWithVisaProvider()
    {
        return [
            [
                [
                    "pay_type" => "creditcard",
                    "code" => "VISA",
                    "card_number" => "4111111111111111",
                    "card_exp_month" => "4",
                    "card_exp_year" => "2018",
                    "card_ccv" => "411"
                ]
            ]
        ];
    }

    public function payWithAmexProvider()
    {
        return [
            [
                [
                    "pay_type" => "creditcard",
                    "code" => "AMEX",
                    "card_number" => "374245455400126",
                    "card_exp_month" => "4",
                    "card_exp_year" => "2018",
                    "card_ccv" => "4111"
                ]
            ]
        ];
    }
}
