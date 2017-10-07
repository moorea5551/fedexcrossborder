<?php

namespace FedExCrossBorder\Tracking;

use FedExCrossBorder\AbstractFedExCrossBorder;
use FedExCrossBorder\Adapter\AdapterInterface;
use FedExCrossBorder\Auth\Credentials;
use FedExCrossBorder\Tracking\Entity\TrackingParam;

class TrackingClient extends AbstractFedExCrossBorder
{
    const BASE_URL = 'https://purplepay.crossborder.fedex.com';

    public function __construct(Credentials $credentials, AdapterInterface $adapter = null, $apiUrl = null, $config = array())
    {
        if(empty($apiUrl)) {
            $apiUrl = self::BASE_URL;
        }

        parent::__construct($credentials, $adapter, $apiUrl, $config);
    }

    public function getOrders($email)
    {
        $email = urlencode($email);
        $content = $this->adapter
            ->get(
                sprintf("%s/v1/tracking/numbers/customer/%s", $this->apiUrl, $email)
            );

        return $content;
    }

    public function getOrderActivities($orderId)
    {
        $orderId = urlencode($orderId);
        $content = $this->adapter
            ->get(
                sprintf("%s/v1/tracking/crossborder/%s", $this->apiUrl, $orderId)
            );

        return $content;
    }

    public function getBoxItems($boxId)
    {
        $boxId = urlencode($boxId);
        $content = $this->adapter
            ->get(
                sprintf("%s/v1/tracking/items/%s", $this->apiUrl, $boxId)
            );

        return $content;
    }

    /**
     * Get monitoring widget
     *
     * @example tests/Tracking/TrackingClientTest.php 19 14 Getting a Monitoring widget
     *
     * @param TrackingParam $trackingParam
     * @return string
     */
    public function getWidget(TrackingParam $trackingParam)
    {
        $content = $this->adapter
            ->post(
                sprintf("%s/%s", $this->apiUrl, 'getMonitoring'),
                $trackingParam->toJSON(128)
            );

        return $content;
    }
}
