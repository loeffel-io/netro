<img src="https://github.com/loeffel-io/netro-docs/blob/master/images/netro.png?raw=true" width="200">

Plugin Framework for WordPress Developers - Super simple, beautiful & powerful

[![Build Status](https://travis-ci.com/loeffel-io/netro.svg?token=diwUYjrdo8kHiwiMCFuq&branch=master)](https://travis-ci.com/loeffel-io/netro)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

- [Documentation](https://docs.wp-netro.io)
- [API Documentation](https://api.wp-netro.io)

```php
# header.php

use Netro\Facade\Type\Post;

foreach (Post::latest(3) as $post) {
    $post->getTitle();
    $post->getContent();
    $post->getCreatedAt()->diffForHumans();
    $post->getImage()->getPath();
    $post->getAuthor()->getLastName();
}
```

## Installation

- [Download](https://github.com/loeffel-io/netro/archive/master.zip) the latest version
- Unzip the files to your WordPress plugins directory `wp-content/plugins/netro`
- Run `composer install` in the `wp-content/plugins/netro` directory
- Activate the plugin in your WPAdmin

## Bug Reports

If you discover a bug in Netro, please open an issue on the [issues](https://github.com/loeffel-io/netro) section
