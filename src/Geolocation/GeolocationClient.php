<?php

namespace FedExCrossBorder\Geolocation;

use FedExCrossBorder\AbstractFedExCrossBorder;
use FedExCrossBorder\Adapter\AdapterInterface;
use FedExCrossBorder\Auth\Credentials;
use FedExCrossBorder\Geolocation\Entity\Geolocation;

class GeolocationClient extends AbstractFedExCrossBorder
{
    public function __construct(Credentials $credentials, AdapterInterface $adapter = null, $apiUrl = null, $config = array())
    {
        parent::__construct($credentials, $adapter, $apiUrl, $config);
    }

    public function getCountry($ipAddress)
    {
        $ipAddress = urlencode($ipAddress);
        $content = $this->adapter
            ->get(
                sprintf("%s/v2/checkout/geolocation/%s", $this->apiUrl, $ipAddress)
            );


        return new Geolocation($content);
    }
}
