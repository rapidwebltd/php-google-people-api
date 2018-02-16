<?php

if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require_once __DIR__.'/../vendor/autoload.php';
}

$client = new Google_Client();

echo PHP_EOL;
echo '*** PHP Google People API - Setup ***';
echo PHP_EOL.PHP_EOL;

echo 'Go to the following URL to setup a new or existing project.';
echo PHP_EOL;
echo 'When asked about credentials, you should setup OAuth credentials.';
echo PHP_EOL;
echo 'When done, enter the client ID and client secret below.';
echo PHP_EOL.PHP_EOL;

echo 'https://console.developers.google.com/start/api?id=people.googleapis.com&credential=client_key';
echo PHP_EOL.PHP_EOL;

$clientId = trim(readline('Google Client ID: '));
$clientSecret = trim(readline('Google Client Secret: '));
echo PHP_EOL;

$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
$client->setAccessType('offline');
$client->setApprovalPrompt('force');

$client->addScope('profile');
$client->addScope('https://www.googleapis.com/auth/contacts');

$authUrl = $client->createAuthUrl();

echo 'Now, go to the following URL, sign in to your Google Account,';
echo PHP_EOL;
echo 'and copy-paste the auth code you receive below.';
echo PHP_EOL.PHP_EOL;

echo $authUrl;
echo PHP_EOL.PHP_EOL;

$authCode = trim(readline('Auth Code: '));
echo PHP_EOL;PHP_EOL;

$client->authenticate($authCode);
$accessToken = $client->getAccessToken();
$refreshToken = $accessToken['refresh_token'];

echo 'This account\'s refresh token is: '.$refreshToken;
echo PHP_EOL.PHP_EOL;

echo 'You should store this refresh token, as it is used to mantain';
echo PHP_EOL;
echo 'access to the Google account.';
echo PHP_EOL.PHP_EOL;

/*
$client->refreshToken($accessToken);

$peopleService = new Google_Service_People($client);

$connections = $peopleService->people_connections->listPeopleConnections('people/me', array('personFields' => 'names,emailAddresses'));

var_dump($connections);
*/