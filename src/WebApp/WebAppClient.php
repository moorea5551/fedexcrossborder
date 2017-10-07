<?php

namespace FedExCrossBorder\WebApp;

use FedExCrossBorder\AbstractFedExCrossBorder;
use FedExCrossBorder\Adapter\AdapterInterface;
use FedExCrossBorder\Auth\Credentials;
use FedExCrossBorder\WebApp\Entity\Cart;

class WebAppClient extends AbstractFedExCrossBorder
{
    const BASE_URL = 'https://purplepay.crossborder.fedex.com';

    /**
     * WebAppClient constructor.
     *
     * @inheritdoc
     */
    public function __construct($config = array(), $apiUrl = null, AdapterInterface $adapter = null, Credentials $credentials = null)
    {
        if(empty($apiUrl)) {
            $apiUrl = self::BASE_URL;
        }

        parent::__construct($credentials, $adapter, $apiUrl, $config);
    }

    /**
     * Get purplepay widget using a defined cart object
     *
     * @example tests/WebApp/WebAppClientTest.php 50 36 Getting a Purplepay from a basic cart object
     *
     * @example tests/WebApp/WebAppClientTest.php 25 12 Getting a Purplepay from a predefined and complex cart object
     *
     * @param Cart $cart
     * @return string
     */
    public function getWidget(Cart $cart)
    {
        $content = $this->adapter
            ->post(
                sprintf("%s/%s", $this->apiUrl, 'getFastboxWidget'),
                $cart->toJSON(128)
            );

        return $content;
    }
}
