## StackCDN API Client (PHP)

### You can clone from GitHub
```
$ git clone git@github.com:StackCDN/api-client-php.git
```

Include necessary files in your php script
``` php
include_once __DIR__ . '/src/Exception.php';
include_once __DIR__ . '/src/HTTPClient.php';
```

### You can install it via [Composer](https://getcomposer.org)

Create composer.json like following
```
{
    "require": {
        "StackCDN/api-client-php": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:StackCDN/api-client-php.git"
        }
    ]
}
```
Run composer to install
```
$ php composer.phar install
```

Include autoloader in your php script
``` php
include_once __DIR__ . '/vendor/autoload.php';
```

### Usage example

``` php
# Create a client
$client = new \StackCDN\APIClient();

# Try to log in
try {

    # Try to log in
    $response = $client->login('my-mail@example.org', 'My Secret Password');

    # Getting authorization token from response
    $authToken = $client->getAuthorizationToken();

    # Request Account's sites list using HTTPClient object
    $sites = $client->getHttpClient()->get(
        '/accounts/' . $response['user']['account']['id'] . '/sites',
        $authToken
    );

    # Output
    echo sizeof($sites) . ' sites found' . PHP_EOL;

} catch (\StackCDN\APIClient\Exception $x) {
  echo 'Error!' . PHP_EOL;
  echo ' > Status: ' . $x->getApiResponseStatus() . PHP_EOL;
  echo ' > Text: ' . $x->getServerErrorMessage() . PHP_EOL;
  echo ' > Details: ' . $x->getServerErrorDetails() . PHP_EOL;
  echo ' > Raw Response: ' . $x->getApiResponse() . PHP_EOL;
}

```