# wp-kit/wp-login-auth

An authentication class for WordPress.

## Installation

Install via [```Composer```](https://getcomposer.org/) in a composer driven folder:

```php
composer require "wp-kit/wp-login-auth"
```

## Usage

Anywhere in your theme or plugin, use the following code:

```php
WPKit\WpLoginAuth::boot([
  'allow' => array(
    '/some/page'
  ),
  'disallow' => array(),
  'logout_redirect' => '/page/login',
  'login_redirect' => home_url(),
  'mask_wp_login' => false
]);
```
## License

wp-kit/wp-login-auth is open-sourced software licensed under the MIT License.
