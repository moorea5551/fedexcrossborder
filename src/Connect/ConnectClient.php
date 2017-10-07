<?php

namespace FedExCrossBorder\Connect;

use GuzzleHttp\Client;
use Meng\AsyncSoap\Guzzle\Factory;

class ConnectClient
{
    protected $client;

    public function __construct($apiUrl = null, $config = array())
    {
        $factory = new Factory();
        $this->client = $factory->create(
            new Client(
                [
                    'curl' => [
                        CURLOPT_TIMEOUT => 120,
                        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36',
                        CURLOPT_SSL_CIPHER_LIST => 'ecdhe_rsa_aes_128_gcm_sha_256',
                        CURLOPT_SSLVERSION => 6,
                        CURLOPT_SSL_VERIFYHOST => 2,
                        CURLOPT_SSL_VERIFYPEER => 2,
                    ]
                ]
            ),
            $apiUrl
        );
    }

    public function ConnectProductInfo($request)
    {
        $response = $this
            ->client
            ->call(
                'ConnectProductInfo',
                [
                    'request' => $request,
                ]
            )
        ;

        return $response;
    }

    public function ConnectSkuStatus($request)
    {
        $response = $this
            ->client
            ->call(
                'ConnectSkuStatus',
                [
                    'request' => $request,
                ]
            )
        ;

        return $response;
    }
}
