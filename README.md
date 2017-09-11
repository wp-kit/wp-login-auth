# wp-kit/wp-login-auth

An authentication class for WordPress.

# Usage

Anywhere in your theme or plugin, use the following code:

```php
WpLoginAuth::boot([
  'allow' => array(
    '/some/page'
  ),
  'disallow' => array(),
  'logout_redirect' => '/page/login',
  'login_redirect' => home_url(),
  'mask_wp_login' => false
]);
```
