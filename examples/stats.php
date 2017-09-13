<?php

include_once __DIR__ . '/../src/APIClient.php';

$client = new \StackCDN\APIClient();

$email      = 'YOUR_EMAIL';
$password   = 'YOUR_PASSWORD';

$client->login($email, $password);
$token = $client->getAuthorizationToken();

# More statistics types are in API Docs: http://portal.stackcdn.io/api/doc/#get--api-stats.{_format}
$statisticsType = 'traffic-for-account';
#$statisticsType = 'traffic-for-site';
#$statisticsType = 'traffic-for-user';

$statsParameters = [
    'period'    => 'last-24-hours',
    'account'   => 'YOUR_ACCOUNT_ID'
];

if ($statisticsType == 'traffic-for-user') {
    $statsParameters['user'] = 'USER_ID_FOR_STATISTICS';
}
if ($statisticsType == 'traffic-for-site') {
    $statsParameters['user'] = 'USER_ID_FOR_STATISTICS';
    $statsParameters['site'] = 'SITE_ID_FOR_STATISTICS';
}

try {
    $getParams = 'type=' . $statisticsType;
    foreach ($statsParameters as $param => $value) {
        $getParams .= '&' . $param . '=' . $value;
    }

    $response = $client->getHttpClient()->get('/stats?' . $getParams, $token);
    print_r($response);
} catch (\StackCDN\APIClient\Exception $x) {
    echo $x->getFile() . ':' . $x->getLine() . ' [' . $x->getServerErrorMessage()  . ']' . PHP_EOL;
} catch (\Exception $x) {
    echo $x->getFile() . ':' . $x->getLine() . ' [' . $x->getMessage()  . ']' . PHP_EOL;
}
