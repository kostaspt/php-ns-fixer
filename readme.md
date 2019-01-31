# PHP Namespace Fixer

[![Build Status](https://travis-ci.org/kostaspt/php-ns-fixer.svg?branch=master)](https://travis-ci.org/kostaspt/php-ns-fixer)
[![Code Coverage](https://scrutinizer-ci.com/g/kostaspt/php-ns-fixer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kostaspt/php-ns-fixer/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kostaspt/php-ns-fixer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kostaspt/php-ns-fixer/?branch=master)

> Automatically find (and fix) wrong namespaces in your PHP projects.

## Install
```bash
$ composer global require kostaspt/php-ns-fixer
```

## Usage

```bash
$ php-ns-fixer fix src --prefix=App
$ php-ns-fixer fix tests --dry-run --skip-empty
```
