<?php
/**
 * API Client wrapper object to expose API REST calls as Object methods
 *
 * Examples:
 *      /api/login will be exposed as $this->login($email, $password)
 *      /api/accounts/{AccountID}/sites will be exposed as $this->getAccountSites($accountId, $authToken)
 */
namespace StackCDN;

include_once __DIR__ . '/Exception.php';
include_once __DIR__ . '/HTTPClient.php';

use StackCDN\APIClient\HTTPClient;


class APIClient
{
    protected $httpClient;
    protected $apiKey = '';
    protected $loggedInUser = [];

    public function __construct($apiKey = '', $apiURL = 'https://portal.stackcdn.io/api')
    {
        $this->httpClient= new HTTPClient($apiURL);
        $this->apiKey = $apiKey;
    }

    /**
     * Gets URI to send request to. Appends api_key if Client contains API key
     * @param $uri
     * @return string
     */
    protected function getURI($uri)
    {
        $uri .= !empty($this->apiKey) ? '?api_key=' . $this->apiKey : '';
        return $uri;
    }


    /**
     * @return HTTPClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function getAuthorizationToken($authToken = null)
    {
        if (empty($authToken)
            && !empty($this->loggedInUser)
            && !empty($this->loggedInUser['token'])
        ) {
            return $this->loggedInUser['token'];
        }
        return $authToken;
    }

    public function login($email, $password)
    {
        $data = $this->httpClient->post('/login', [
            'email' => $email,
            'password' => $password
        ]);
        if (!empty($data['bearer_token'])) {
            $this->loggedInUser = [
                'id' => $data['id'],
                'token' => $data['bearer_token'],
                'account' => [
                    'id' => $data['user']['account']['id']
                ]
            ];
        }
        return $data;
    }

    /**
     * Creates new account
     * Parameters list is similar to Account Update action: http://portal.stackcdn.io/api/doc/#put--api-accounts-{id}.{_format}
     *
     * @param string $name Account Name (must be unique across the system)
     * @param array $parameters Account creation parameters
     * @param null $authToken
     * @return mixed|null
     */
    public function createAccount($name, $parameters = [], $authToken = null)
    {
        $parameters['name'] = $name;
        return $this->httpClient->post(
            $this->getURI('/accounts'),
            $parameters,
            $this->getAuthorizationToken($authToken)
        );
    }

    /**
     * Creates a new user for a given account
     * Users parameters list can be found at: http://portal.stackcdn.io/api/doc/#post--api-accounts-{id}-users.{_format}
     *
     * @param int $accountId
     * @param array $parameters
     * @param null $authToken
     * @return mixed|null
     */
    public function createUserForAccount($accountId, $parameters = [], $authToken = null)
    {
        return $this->httpClient->post(
            $this->getURI('/accounts/' . $accountId . '/users'),
            $parameters,
            $this->getAuthorizationToken($authToken)
        );
    }

    /**
     * Creates a new Bucket
     *
     * Bucket Parameters list:
     *
     * "credits" - amount of credits to add
     * "unit_price" - price in dollars for 1 GB of data
     * "expires" - expiration period. Possible values: "1 month", "1 week" or blank for 1 year
     * "comments" - (optional) Comments to a bucket
     * "unit_price__EU" - (optional) price for Europe. Overrides "unit_price"
     * "unit_price__NA" - (optional) price for North America. Overrides "unit_price"
     * "unit_price__AS" - (optional) price for Asia. Overrides "unit_price"
     * "unit_price__SA" - (optional) price for South America. Overrides "unit_price"
     * "unit_price__OC" - (optional) price for Oceania. Overrides "unit_price"
     * "unit_price__AF" - (optional) price for Africa. Overrides "unit_price"
     *
     * @param int $accountId Account ID to create bucket for
     * @param array $parameters bucket parameters
     * @param null $authToken
     * @return mixed|null
     */
    public function createBucket($accountId, $parameters, $authToken = null)
    {
        return $this->httpClient->post(
            $this->getURI('/accounts/' . $accountId . '/credits-transactions'),
            $parameters,
            $this->getAuthorizationToken($authToken)
        );
    }

}
