# laravel-teams-logging

Laravel handler to sending messages to Microsoft Teams using the Incoming Webhook connector.

## Installation

Require this package with composer.

```bash
$ composer require kroyeeg/laravel-teams-logging
```

## Integration

Package tested and worked with Laravel and Lumen framework (5.7+).

**Laravel**: After installation using composer finishes up, you'll have to add the following line to your `config/app.php`:

```php
Kroyeeg\LaravelTeamsLogging\LoggerServiceProvider::class
```

**Lumen**: For Lumen, you'll have to add the following line to your `bootstrap/app.php`:

```php
$app->register(Kroyeeg\LaravelTeamsLogging\LoggerServiceProvider::class);
```

**Laravel**: Then copy `teams` config file from laravel-teams-logging to your config folder:

```bash
$ php artisan vendor:publish --provider="Kroyeeg\LaravelTeamsLogging\LoggerServiceProvider"
```

**Lumen**: For Lumen, you need to copy file manually to your config folder and enable it in `bootstrap/app.php`:

```php
$app->configure('teams');
```

Create a [custom channel](https://laravel.com/docs/master/logging#creating-custom-channels) using laravel logging file or create new logging config file for lumen.

Add this configuration to `config/logging.php` file

```php
'teams' => [
    'driver'    => 'custom',
    'via'       => \Kroyeeg\LaravelTeamsLogging\LoggerChannel::class,
    'level'     => 'debug',
    'url'       => env('INCOMING_WEBHOOK_URL'),
    'style'     => 'simple',    // Available style is 'simple' and 'card', default is 'simple'
],
```

or simply add name to specify different project name for each connector.

```php
'teams' => [
    'driver'    => 'custom',
    'via'       => \Kroyeeg\LaravelTeamsLogging\LoggerChannel::class,
    'level'     => 'debug',
    'url'       => env('INCOMING_WEBHOOK_URL'),
    'style'     => 'simple',    // Available style is 'simple' and 'card', default is 'simple'
    'name'      => 'Dummy Project'
],
```

There are 2 available styles for microsoft teams message, using simple and card. You can see card style in results style which is difference from simple style.

After added configs to your `config/logging.php` file, add `INCOMING_WEBHOOK_URL` variable to your `.env` file with connector url from your microsoft teams connector. Please read [microsoft teams](https://docs.microsoft.com/en-us/microsoftteams/platform/concepts/connectors/connectors-using) document to find your connector url.

## Usage

To send a simple error message to teams channel, you kindly use script below:

```php
Log::channel('teams')->error('Error message');
```

Or you can include additional info to card message using log context.

```php
Log::channel('teams')->error('Error message', [
    'Assigned to' => 'Unassigned',
    'stack trace' => $e->getTraceAsString(), // could not set getTrace(), because may be upper limit fact value's section
    'nested array' => [ // output json_encode string
        'key1' => 'value1',
        'key2' => 'value2',
    ],
]);
```

When using simple style, log context will ignore from message.

You can also add `teams` to the default `stack` channel so all errors are automatically send to the `teams` channel.

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'teams'],
    ],
],
```

## Results

Here are some results of notifications sent to microsoft teams channel using card style.

https://github.com/margatampu/laravel-teams-logging#results

## License

This laravel-teams-logging package is available under the MIT license. See the LICENSE file for more info.
