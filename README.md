# PHPLint - the fastest way to check your syntax

[![PHP-Tests](https://github.com/wundii/PHPLint/actions/workflows/code_quality.yml/badge.svg)](https://github.com/wundii/PHPLint/actions/workflows/code_quality.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://phpstan.org/)
[![Downloads](https://img.shields.io/packagist/dt/wundii/phplint.svg?style=flat)](https://packagist.org/packages/wundii/phplint)

This program is one of the fastest tools for static code analysis and error detection in PHP source code.
The native php lint checking is used here.

## Installation and Usage

```shell
composer require wundii/phplint --dev
```

```shell
php vendor/bin/phplint
php vendor/bin/phplint --config=phplint.php
php vendor/bin/phplint init
php vendor/bin/phplint list
php vendor/bin/phplint --help
php vendor/bin/phplint --no-config
php vendor/bin/phplint --no-config --paths=src --paths=tests --skip=vendor
```

### Functionality over the config file (phplint.php)
+ php cgi executable (default: php)
+ php extension (default: php)
+ paths (default: src)
+ skip
+ memory limit (default: 512M)
+ async processes (default: 10)
+ async processes timeout (default: 60)
+ console warnings (default: true)
+ console notice (default: true)
+ cache class (default: Symfony\Component\Cache\Adapter\FilesystemAdapter)
+ no exit code (default: false)
+ no process bar (default: false)

## Development for PHPLint

### composer scripts

```shell
composer rector-dry
composer rector-apply
composer phpstan
composer ecs-dry
composer ecs-apply
composer phpunit
composer cache-clear
```

### complete checks before merge

```shell
composer complete-check
```

### To-do list for version 1.0.0
+ [x] add symfony cache-system
+ [x] refactor LintConsoleOutput class
+ [x] refactor LintConfig class
+ [ ] skip classes
+ [x] run without config file
+ [x] test BootstrapInputResolver
+ [x] test createLintConfigFromInput
+ [x] test no-config with config options

## Feedback and Contributions
I welcome feedback, bug reports and contributions from the community! 
If you found a bug or have a suggestion for improvement, please create an issue. 

Every contribution is welcome!