<?php

include_once __DIR__ . '/../src/APIClient.php';

$apiKey = 'YOUR_API_KEY_HERE';

$client = new \StackCDN\APIClient();

$email      = 'YOUR_EMAIL';
$password   = 'YOUR_PASSWORD';

$client->login($email, $password);
$token = $client->getAuthorizationToken();

$userRandom = rand(1, 200);
$userData = [
    'email' => 'aa-' . $userRandom . '@example.org',
    'password' => '12345',
    'first_name' => 'John-' . $userRandom,
    'last_name' => 'Doe',
    'is_active' => 0
];

try {
    # Create User
    echo 'POST user ...' . PHP_EOL;
    $userResponse = $client->getHttpClient()->post('/accounts/1/users', $userData, $token);
    $userId = $userResponse['id'];
    echo 'User created [' . $userId . ']' . PHP_EOL;

    echo 'GET user ...' . PHP_EOL;
    $userFromServer = $client->getHttpClient()->get('/users/' . $userId, $token);
    #print_r($userFromServer);
    if (empty($userFromServer)) {
        throw new \Exception('User was not created?');
    }
    # Activate User
    echo 'PUT user ...' . PHP_EOL;
    $client->getHttpClient()->put('/users/' . $userId, ['is_active' => 1], $token);
    # Check User Data From Server
    echo 'DELETE user ...' . PHP_EOL;
    $userFromServer = $client->getHttpClient()->delete('/users/' . $userId, $token);
    echo 'GET user again (Expecting NOT FOUND Exception)' . PHP_EOL;
    $userFromServer = $client->getHttpClient()->get('/users/' . $userId, $token);
    if (!empty($userFromServer)) {
        echo 'ERROR: User is NOT deleted' . PHP_EOL;
    }
} catch (\StackCDN\APIClient\Exception $x) {
    echo $x->getFile() . ':' . $x->getLine() . ' [' . $x->getServerErrorMessage()  . ']' . PHP_EOL;
} catch (\Exception $x) {
    echo $x->getFile() . ':' . $x->getLine() . ' [' . $x->getMessage()  . ']' . PHP_EOL;
}


