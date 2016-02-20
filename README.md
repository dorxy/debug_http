# DebugHttp

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://img.shields.io/travis/dorxy/debug_http/master.svg?style=flat-square)](https://travis-ci.org/dorxy/debug_http)
[![Coverage Status](https://img.shields.io/codecov/c/github/dorxy/debug_http.svg?style=flat-square)](https://codecov.io/github/dorxy/debug_http)
[![Code Consistency](https://squizlabs.github.io/PHP_CodeSniffer/analysis/dorxy/debug_http/grade.svg)](http://squizlabs.github.io/PHP_CodeSniffer/analysis/dorxy/debug_http/)
[![Total Downloads](https://img.shields.io/packagist/dt/dorxy/debug_http.svg?style=flat-square)](https://packagist.org/packages/dorxy/debug_http)
[![Latest Stable Version](https://img.shields.io/packagist/v/dorxy/debug_http.svg?style=flat-square&label=stable)](https://packagist.org/packages/dorxy/debug_http)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/dorxy/debug_http.svg?style=flat-square&label=unstable)](https://packagist.org/packages/dorxy/debug_http)

DebugHttp gives the [CakePHP DebugKit](https://github.com/cakephp/debug_kit) plugin integration for HTTP requests using [CakePHP's client](http://book.cakephp.org/3.0/en/core-libraries/httpclient.html).

## Requirements

The `master` branch has the following requirements:

* CakePHP 3.0.0 or larger
* DebugKit 3.2 or larger

## Installation

* Install the plugin with [Composer](https://getcomposer.org/) from your CakePHP Project's ROOT directory (where the **composer.json** file is located)
```sh
php composer.phar require dorxy/debug_http "~1.0"
```
_note this is not a dev requirement_

* [Load the plugin](http://book.cakephp.org/3.0/en/plugins.html#loading-a-plugin)
```php
Plugin::load('DebugHttp');
Plugin::load('DebugKit', ['bootstrap' => true, 'routes' => true]);
```

* Add the panel to DebugKit
```php
Configure::write('DebugKit.panels', ['DebugHttp.ClientCall']);
```

* Set `'debug' => true,` in `config/app.php`.

## Usage

Whenever you wish to have a client request appear in the DebugHttp panel you must use the provided client, e.g.:
```php
$http = new \DebugHttp\Network\Http\Client();
$http->get('http://www.google.com');
```

The request and response will automatically appear in the **Client calls** panel, as well as their timing in the Timer panel.

## Screenshots

![alt text](https://github.com/dorxy/debug_http/raw/master/example-images/example-calls.png "Calls panel")

![alt text](https://github.com/dorxy/debug_http/raw/master/example-images/example-expanded.png "Call opened in panel")

![alt text](https://github.com/dorxy/debug_http/raw/master/example-images/example-timer.png "Requests incorporated in Timer panel")

## Reporting Issues

If you have a problem with DebugHttp or wish to see other features please open an issue on [GitHub](https://github.com/dorxy/debug_http/issues).
