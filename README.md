<p align="center">
	The easiest and fastest way to get up and running with Snipcart's api.
</p>


<p align="center">
<img src="https://i.imgur.com/FgLDOaa.png" alt="Snipcart API - mtownsend/snipcart-api" title="Snipcart API - mtownsend/snipcart-api">
</p>

## First steps

* [Register an account and obtain your api key](https://app.snipcart.com/register)
* [Snipcart Developer Documentation](https://docs.snipcart.com/api-reference/introduction)

## Installation

Install via composer:

```
composer require mtownsend/snipcart-api
```

*This package can be used with any PHP 7.0+ application but has special support for Laravel.*

### Registering the service provider (Laravel users)

For Laravel 5.4 and lower, add the following line to your ``config/app.php``:

```php
/*
 * Package Service Providers...
 */
Mtownsend\SnipcartApi\Providers\SnipcartApiServiceProvider::class,
```

For Laravel 5.5 and greater, the package will auto register the provider for you.

### Using Lumen

To register the service provider, add the following line to ``app/bootstrap/app.php``:

```php
$app->register(Mtownsend\SnipcartApi\Providers\SnipcartApiServiceProvider::class);
```

### Publishing the config file (Laravel users)

````
php artisan vendor:publish --provider="Mtownsend\SnipcartApi\Providers\SnipcartApiServiceProvider"
````

Once your ``snipcart.php`` has been published your to your config folder, add the api key you obtained from [Snipcart](https://app.snipcart.com/dashboard/account/credentials). If you are using Laravel and put your Snipcart api key in the config file, Laravel will automatically set your api key every time you instantiate the class through the helper or facade.

## Quick start

### Using the class

```php
use Mtownsend\SnipcartApi\SnipcartApi;

$orders = (new SnipcartApi($apiKey))->get()->from('/orders')->send();
```

### HTTP methods

This package supports RESTful HTTP methods including ``GET`` (default), ``POST``, ``PUT``, ``PATCH`` and ``DELETE``.

#### GET example
```php
// Get a list of orders with a query string of ?limit=10&offset=5&status=Processed
$orders = (new SnipcartApi($apiKey))->get()->from('/orders')->payload([
	'limit' => 10,
	'offset' => 5,
	'status' => 'Processed'
])->send();
```

#### POST example
```php
// Post a refund
$refund = (new SnipcartApi($apiKey))->post()->payload([
	'token' => '6dc8e374-7e30-4dc5-9b68-2d605819e7f0',
	'amount' => 5.00,
	'comment' => "We're refunding $5 for your order."
])->to('/orders/6dc8e374-7e30-4dc5-9b68-2d605819e7f0/refunds')->send();
```

#### PUT example
```php
// Update a product
$product = (new SnipcartApi($apiKey))->put()->payload([
	'stock' => 200,
	'allowOutOfStockPurchases' => false
])->to('/products/3932ecd1-6508-4209-a7c6-8da4cc75590d')->send();
```

#### DELETE example
```php
// Delete an allowed domain from your Snipcart account
$product = (new SnipcartApi($apiKey))->delete()->payload([
	[
		[
			'domain' => 'subdomain.my-website.com',
			'protocol' => 'https'
		],
	]
])->from('/settings/alloweddomains')->send();
```

### Class methods

#### ->to(string '/url') or ->from(string '/url')

The ``to`` or ``from`` methods are identical and only exist to make your syntax make more semantic sense (``get()->from()`` or ``post()->to()``). These methods expect to receive a relative url path for the Snipcart endpoint you're attempting to communicate with. For example, if you want to get a list of orders from ``https://app.snipcart.com/api/orders``, you would utilize your method of choice and pass it an argument of ``/orders``.

Note: It does not matter if you prepend a slash, append a slash, both, or exclude both. The package gracefully handles your usage of prepended/appended slashes of the relative url. Any of these examples would be acceptable arguments: ``/orders/``, ``/orders``, ``orders/``, or ``orders``.

#### ->payload(array ['key' => 'value']) or ->payload('key', 'value')

The ``payload`` method allows you to pass data through with your request.

If the request is a ``GET`` operation the payload will be converted to a valid query string. E.g. ``['from' => '2018-05-05', 'to' => '2019-05-05']`` will produce ``?from=2018-05-05&to=2019-05-05`` and be automatically appended to your request url.

Alternatively, if your preference is to manually include your query string for ``GET`` requests you may omit the ``payload`` method entirely and append your query string to the ``to``/``from`` method. E.g. ``->get()->from('/orders?limit=10&offset=5')->send()``.

If the request is not a ``POST``, ``PUT``, ``PATCH`` or ``DELETE`` operation the payload will automatically be converted to json and sent in the request's body.

#### ->send()

The ``send`` method triggers the api call to be sent and returns the response.

#### ->addHeader(string 'key', string 'value')

The ``addHeader`` method accepts two arguments. The first is the header key and the second is the header value. By default this package sets the ``Accept`` and ``Content-Type`` for you.

#### ->addHeaders(array ['key' => 'value'])

The ``addHeaders`` method accepts an associative array of key/values to set as headers for the api request.

#### ->responseCode()

The ``responseCode`` method returns the http code received from the server for the request. Note: this method should only be used if you are breaking up your api call into multiple variables.

#### ->successful()

The ``successful`` method parses the http code received from the server and checks for any 2XX code and returns a boolean. Note: this method should only be used if you are breaking up your api call into multiple variables.

### Checking for failed api calls

Snipcart's api provides graceful failure in many circumstances. If you were to make an api call to the endpoint ``/does-not-exist``, the response would be ``null``. You could easily check the value of your request object with a simple ``if`` statement before attempting to perform any logic.

```php
$response = (new SnipcartApi($apiKey))->get()->from('/does-not-exist')->send();
if (!$response) {
	// api call failed
}
// else: do something with the response data...
```

Alternatively, if you prefer to split up your api call into multiple variables, the ``SnipcartApi`` class comes with a ``successful`` method.

```php
$call = (new SnipcartApi($apiKey))->get()->from('/does-not-exist');
$response = $call->send();
if (!$call->successful()) {
	// api call failed
}
// else: do something with the response data...
```

### Using the global helper (Laravel users)

If you are using Laravel, this package provides a convenient helper function which is globally accessible. The package will automatically set your api key from your ``config/snipcart.php`` file.

```php
snipcart()->get()->from('/orders')->send();
```

### Using the facade (Laravel users)

If you are using Laravel, this package provides a facade. To register the facade add the following line to your ``config/app.php`` under the ``aliases`` key. The package will automatically set your api key from your ``config/snipcart.php`` file.

````php
'Snipcart' => Mtownsend\SnipcartApi\Facades\SnipcartApi::class,
````

```php
use Snipcart;

Snipcart::get()->from('/orders')->send();
```

## Credits

- Mark Townsend
- [All Contributors](../../contributors)

## Testing

*Tests coming soon...*

You can run the tests with:

```bash
./vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.