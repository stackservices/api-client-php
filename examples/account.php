<?php

include_once __DIR__ . '/../src/APIClient.php';

$apiKey = 'YOUR_API_KEY_HERE';

$client = new \StackCDN\APIClient(
    $apiKey,
    'http://portal-test.stackcdn.io/api' # Leave this parameter empty to use CDN Portal LIVE
);

try {

    # Create account
    $accountName = 'Testing ' . rand(0, 100) . '_' . time();
    $accountData = [
        'is_active' => 1,
        'has_contract' => 1,
        'is_payg' => 1
    ];
    $response = $client->createAccount($accountName, $accountData);

    $newAccountId = $response['id'];
    echo 'New Account ID: ' . $newAccountId . PHP_EOL;

    # Create a new User
    $userData = [
        'first_name' => 'John',
        'insertion' => 'van',
        'last_name' => 'Doe',
        'passwrod' => time(),
        'role' => 'accountManager',
        'email' => 'john.doe. ' . rand(1000, 100000) . '@example.org'
    ];
    $response = $client->createUserForAccount($newAccountId, $userData);

    $newUser = $response['id'];
    echo 'New User ID: ' . $newUser . PHP_EOL;


    # Create a bucket
    $bucketData = [
        "credits" => 10000,
        "expires" => "", # empty for 1 year
        "unit_price" => 0.05,

        # Optional parameters below
        "comments" => "Bucket is created from Third Party CRM",
        "unit_price__EU" => 0.03,
        "unit_price__NA" => 0.03,
        "unit_price__AS" => 0.07,
        "unit_price__SA" => 0.07,
        "unit_price__AF" => 0.09,
        "unit_price__OC" => 0.1
    ];
    $response = $client->createBucket($newAccountId, $bucketData);

    $createdBucket = $response['bucket'];
    print_r($createdBucket);

} catch (\StackCDN\APIClient\Exception $x) {
    echo 'API Exception: [' . $x->getServerErrorMessage() . '][' . $x->getServerErrorDetails() . ']' . PHP_EOL;
}

