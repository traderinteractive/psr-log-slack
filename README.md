# PSR Log Slack

[![Build Status](https://travis-ci.org/traderinteractive/psr-log-slack.svg?branch=master)](https://travis-ci.org/traderinteractive/psr-log-slack)
[![Code Quality](https://scrutinizer-ci.com/g/traderinteractive/psr-log-slack/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/traderinteractive/psr-log-slack/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/traderinteractive/psr-log-slack/v/stable)](https://packagist.org/packages/traderinteractive/psr-log-slack)
[![Latest Unstable Version](https://poser.pugx.org/traderinteractive/psr-log-slack/v/unstable)](https://packagist.org/packages/traderinteractive/psr-log-slack)
[![License](https://poser.pugx.org/traderinteractive/psr-log-slack/license)](https://packagist.org/packages/traderinteractive/psr-log-slack)

[![Total Downloads](https://poser.pugx.org/traderinteractive/psr-log-slack/downloads)](https://packagist.org/packages/traderinteractive/psr-log-slack)
[![Monthly Downloads](https://poser.pugx.org/traderinteractive/psr-log-slack/d/monthly)](https://packagist.org/packages/traderinteractive/psr-log-slack)
[![Daily Downloads](https://poser.pugx.org/traderinteractive/psr-log-slack/d/daily)](https://packagist.org/packages/traderinteractive/psr-log-slack)

This is an implementation of [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) reporting to [Slack](https://api.slack.com/) using [incoming webhooks](https://api.slack.com/incoming-webhooks).

## Requirements

PSR Log Slack requires PHP 7.0 (or later).

## Composer
To add the library as a local, per-project dependency use [Composer](http://getcomposer.org)! Simply add a dependency on `traderinteractive/psr-log-slack` to your project's `composer.json`.
```sh
composer require traderinteractive/psr-log-slack
```

## Contact
Developers may be contacted at:

 * [Pull Requests](https://github.com/traderinteractive/psr-log-slack/pulls)
 * [Issues](https://github.com/traderinteractive/psr-log-slack/issues)

## Run Unit Tests
With a checkout of the code get [Composer](http://getcomposer.org) in your PATH and run:

```sh
composer install
./vendor/bin/phpunit
