Getting started
===============

The PHP StatHat bindings library provides a simple, object oriented way
to interact with the StatHat APIs.


Installation
------------

### Step 1: Download via Composer

You can install this bundle using composer:

    php composer.phar require pradodigital/php-stathat-bindings

or add the package to your composer.json file directly:

    {
        "require": {
            "pradodigital/php-stathat-bindings": "~2.0"
        }
    }

Run composer to download the bundle:

    php composer.phar update pradodigital/php-stathat-bindings


Usage
-----

The easiest way to get started is by using the StatHatEz API to send stats to
StatHat.

First create an HTTP client to use for API requests. A simple asynchronous one
is provided in the package.

    $client = new AsyncHttpClient();

### EZ API

Next, create an instance of type StatHatEz and provide to it your HTTP client
and your EZ Key.

    $ezKey = 'YOUR_EMAIL_OR_CUSTOM_EZ_KEY';
    $statHat = new StatHatEz($client, $ezKey);

Finally, send stats over by using the ezCount or ezValue methods.

    $statHat->ezCount('messages sent');         // Default count increases by 1.
    $statHat->ezCount('messages sent', 5);      // Alternatively provide a number.
    $statHat->ezValue('ws0 load average', 0.5); // Value stats always need a value.

    // You can also provide an optional timestamp.
    $statHat->ezValue('ws0 load average', 0.2, 1363118126);

The stats will be batched together and sent to StatHat upon shutdown.

### Classic API

You can also use the Classic API by switching to the StatHatClassic class.

    $userKey = 'UNIQUE_USER_KEY_IN_STATHAT';
    $statHat = new StatHatClassic($client, $userKey);

    // Notice the use of the stat key instead of the name. Timestamp is again
    // optional and count is optional in the count() method.
    $statHat->count('i2oer9xisFGsHDNVYzJTM2cy9Xj', 5, 1362772440);
    $statHat->value('i2oer9xisFGsHDNVYzJTM2cy9Xj', 13.947, 1362772440);

### JSON API

Finally there's a simple implementation of the JSON API to get stats out of
StatHat. This time we need to use a Guzzle client and your Access Token which
is different from your EZ or User keys. You can also use the EZ API in
conjuction by specifying an ezKey to use.

    $client = new GuzzleHttp\Client();
    $statHat = new StatHat($client, $accessToken, $ezKey);

You can access stats via the following methods:

    // Get a array of stats available to your Access Token.
    $list = $statHat->getStatList();

    // Get a specific stat's details by name as an array.
    $stat = $statHat->getStat($name);

    // Get a dataset for a specific stat ID. To get stat IDs you can look at
    // the URL used in the web interface or see the results of getStatList()
    // and getStat() calls.
    $dataset = $statHat->getDatasets($statId, '1w3h');

See the StatHat API documentation and the class docs for more details.
