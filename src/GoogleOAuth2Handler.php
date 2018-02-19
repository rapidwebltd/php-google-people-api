<?php

namespace RapidWeb\GooglePeopleAPI;

use GuzzleHttp\Psr7\Request;

class GoogleOAuth2Handler
{
    private $clientId;
    private $clientSecret;
    private $scopes;
    private $refreshToken;
    private $client;
    
    public $authUrl;

    public function __construct($clientId, $clientSecret, $scopes, $refreshToken = '')
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
        $this->refreshToken = $refreshToken;

        $this->setupClient();
    }

    private function setupClient()
    {
        $this->client = new \Google_Client();

        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');

        foreach($this->scopes as $scope)  {
            $this->client->addScope($scope);
        }

        if ($this->refreshToken) {
            $this->client->refreshToken($this->refreshToken);
        } else {
            $this->authUrl = $this->client->createAuthUrl();
        }
    }

    public function getRefreshToken($authCode)
    {
        $this->client->authenticate($authCode);
        $accessToken = $this->client->getAccessToken();
        return $accessToken['refresh_token'];
    }

    public function performRequest($method, $url, $body = null)
    {
        $httpClient = $this->client->authorize();
        $request = new Request('GET', $url, [], $body);
        $response = $httpClient->send($request);
        $body = $response->getBody();
        return $body;
    }

}