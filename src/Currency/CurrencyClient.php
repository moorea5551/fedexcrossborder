<?php

namespace FedExCrossBorder\Currency;

use FedExCrossBorder\AbstractFedExCrossBorder;
use FedExCrossBorder\Adapter\AdapterInterface;
use FedExCrossBorder\Auth\Credentials;

class CurrencyClient extends AbstractFedExCrossBorder
{
    public function __construct(Credentials $credentials, AdapterInterface $adapter = null, $apiUrl = null, $config = array())
    {
        parent::__construct($credentials, $adapter, $apiUrl, $config);
    }

    public function getExchangeRate($partnerKey)
    {
        $content = $this->adapter
            ->get(
                sprintf("%s/currency/exchanges.json?key=%s", $this->apiUrl, $partnerKey)
            );

        return $content;
    }
}
