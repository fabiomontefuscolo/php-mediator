# php-mediator [![Build Status](https://travis-ci.org/fabiomontefuscolo/php-mediator.svg?branch=master)](https://travis-ci.org/fabiomontefuscolo/php-mediator)

It is just another Hook system. It is useful to integrate very distinct components of software without making them too coupled to each other.


## Installing

```shell
composer require montefuscolo/php-mediator
```


## Using it

### Actions

```php
<?php
use montefuscolo/BaseMediator;

$mediator = new BaseMediator();

// Add callbacks to be called later
$mediator->add_action('my-channel', function() {
    echo 'Hello World' . PHP_EOL;
});
$mediator->add_action('my-channel', function() {
    echo 'Foo Bar' . PHP_EOL;
});

// .... 

$mediator->run_actions('my-channel');
```


### Filters

```php
<?php
use montefuscolo/BaseMediator;

$mediator = new BaseMediator();

// Add callbacks to be called later
$mediator->add_filter('my-channel', function($n) {
    return $n * 2;
});
$mediator->add_filter('my-channel', function($n) {
    return $n * 3;
});
$mediator->add_filter('my-channel', function($n) {
    return $n - 6;
});

// .... 

echo $mediator->run_filters('my-channel', 1);
// >>> 0
```
