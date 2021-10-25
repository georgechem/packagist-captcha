## Generates Captcha using GD library
<hr>

Install with composer:
```
composer require georgechem/captcha
```

Usage: (Captcha generation)
```php
use Georgechem\Captcha\Captcha\Captcha;

require __DIR__ . '/vendor/autoload.php';
// Init session storage for Captcha - expire time can be edited (default 5 minutes)
$captcha = Captcha::getInstance();
// create end echo image with captcha
$captcha->create();
```

Usage: (Captcha verification)
```php
use Georgechem\Captcha\Captcha\Captcha;

require __DIR__ . '/vendor/autoload.php';

$captcha = Captcha::getInstance();
// @Returns bool, true on success, false on failure
$captcha->verify($text);
```

