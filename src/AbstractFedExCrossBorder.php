<?php
namespace FedExCrossBorder;

use FedExCrossBorder\Adapter\AdapterInterface;
use FedExCrossBorder\Adapter\GuzzleHttpAdapter;
use FedExCrossBorder\Auth\Credentials;

abstract class AbstractFedExCrossBorder
{
    /**
     * The url of the API endpoint.
     *
     * @var string
     */
    const ENDPOINT_URL = 'https://checkout.crossborder.fedex.com';

    /**
     * The credentials.
     *
     * @var array
     */
    protected $credentials;

    /**
     * The Bearer Access Token.
     *
     * @var string
     */
    protected $access_token;

    /**
     * The Partner Key.
     *
     * @var string
     */
    protected $partner_key;

    /**
     * The Id of the element to work with.
     *
     * @var integer
     */
    protected $id;

    /**
     * The API url.
     *
     * @var string
     */
    protected $apiUrl;

    /**
     * The OAuth url.
     *
     * @var string
     */
    protected $oauthUrl;

    /**
     * The adapter to use.
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor.
     *
     * @param Credentials $credentials The credentials to use.
     * @param AdapterInterface $adapter The HttpAdapter to use.
     * @param string $apiUrl The url for the service
     * @param array $config Additional parameters (Headers, ...)
     */
    public function __construct(Credentials $credentials = null, AdapterInterface $adapter = null, $apiUrl = null, $config = array())
    {
        $this->setAdapter($adapter);

        $this->adapter->setHeaders(
            array(
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            )
        );

        $this->setCredentials($credentials);

        $additional_headers = (array_key_exists('headers', $config)) ? $config['headers'] : array();
        foreach ($additional_headers as $header_key => $header_value) {
            $this->adapter->addHeader($header_key, $header_value);
        }
        $this->apiUrl  = ($apiUrl) ?: self::ENDPOINT_URL;
    }
    /**
     * Builds the API url according to the parameters.
     *
     * @param integer $id         The Id of the element to work with (optional).
     * @param string  $action     The action to perform (optional).
     * @param array   $parameters An array of parameters (optional).
     *
     * @return string The built API url.
     */
    protected function buildQuery($id = null, $action = null, array $parameters = array())
    {
        $parameters = http_build_query(array_merge($parameters, $this->credentials));
        $query = $id ? sprintf("%s/%s", $this->apiUrl, $id) : $this->apiUrl;
        $query = $action ? sprintf("%s/%s/?%s", $query, $action, $parameters) : sprintf("%s/?%s", $query, $parameters);
        return $query;
    }
    /**
     * Processes the query.
     *
     * @param string $query The query to process.
     *
     * @return \StdClass
     *
     * @throws \RuntimeException
     */
    protected function processQuery($query)
    {
        if (null === $processed = json_decode($this->adapter->getContent($query))) {
            throw new \RuntimeException(sprintf("Impossible to process this query: %s", $query));
        }
        if ('ERROR' === $processed->status) {
            // it looks that the API does still have the old error object structure.
            if (isset($processed->error_message)) {
                $errorMessage = $processed->error_message;
            }
            if (isset($processed->message)) {
                $errorMessage = $processed->message;
            }
            throw new \RuntimeException(sprintf("%s: %s", $errorMessage, $query));
        }
        return $processed;
    }

    /**
     * Set a custom Http Client
     *
     * @param AdapterInterface|null $adapter
     */
    public function setAdapter(AdapterInterface $adapter = null)
    {
        $this->adapter = $adapter ?: new GuzzleHttpAdapter();
    }

    public function setCredentials(Credentials $credentials = null)
    {
        if($credentials != null) {
            $partnerKey = $credentials->getPartnerKey();

            $this->credentials = array(
                'client_id' => $credentials->getClientId(),
                'client_secret' => $credentials->getClientSecret(),
                'partner_key' => $partnerKey,
            );

            $this->adapter->addHeader('Authorization', sprintf("Basic %s", base64_encode($credentials->getClientId() . ':' . $credentials->getClientSecret())));

            if($partnerKey) {
                $this->setPartnerKey($partnerKey);
            }
        }
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @param string $access_token
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
        $this->adapter->addHeader('Authorization', 'Bearer ' . $access_token);
    }

    /**
     * @return string
     */
    public function getPartnerKey()
    {
        return $this->partner_key;
    }

    /**
     * @param string $partner_key
     */
    public function setPartnerKey($partner_key)
    {
        $this->partner_key = $partner_key;
        $this->adapter->addHeader('X-FCB-Partner-Key', $partner_key);
    }
}
