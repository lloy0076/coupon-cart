# Coupon Cart

## Installation

You will need to:

* Be able to install Laravel 6.0
* Be able to use composer
* Be able to use the Yarn package manager (preferably) or NPM
* Have access to the Internet

Installation should be as simple as:

```shell script
% git clone https://github.com/lloy0076/coupon-cart.git
% cd coupon-cart
% composer install -vv -o
% yarn install
% yarn run dev
% php artisan migrate:fresh --seed --no-ansi
```

This uses Passport but does not require any setup client. That said, if you want to use Passport (the API is contactable from the outside), do:

```shell script
% php artisan passport:install
```

Apart from the normal Laravel knowledge there is nothing you need to setup to get this running. Make sure you've changed the application name, at the very least.

You'll want to take notice the second key as you most likely will want to login using a password grant.

This has been tested with SQLite3, MySQL 5.7.19 and higher, as well as the in memory SQLite3 driver.

## Tests (Unit)

There is a set of PHP unit tests; although these are "unit tests" they exercise the vast majority of functionality needed by the system.

By default they will use an in memory SQLite3 database as this reduces the 10+ minutes needed by MySQL to about 30 seconds or less.

You MAY change this in the provided "phpunit.xml" file.

## Test (Feature)

The Unit Tests do contain a lot of "feature like material" (it is hard to test a disount system without also having products and so forth).

## Test (E2E)

There are no E2E tests (e.g. using Mocha/Jest).
