[![Build Status](https://travis-ci.com/loeffel-io/netro.svg?token=diwUYjrdo8kHiwiMCFuq&branch=master)](https://travis-ci.com/loeffel-io/netro)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![codecov](https://codecov.io/gh/loeffel-io/netro/branch/master/graph/badge.svg?token=tmPeOhqvU6)](https://codecov.io/gh/loeffel-io/netro)

# Netro
Framework for WordPress Developers - Super simple & Powerful

## Installation

- [Download](https://github.com/loeffel-io/netro/archive/master.zip) the latest version
- Unzip the file to your WordPress plugins directory `wp-content/plugins`
- Run `composer install` in the `netro` plugin directory
- Activate the plugin in your WP Admin

## Roadmap

| Component     | Implementation | Documentation | Tests | Release |
|---------------|----------------|---------------|-------|---------|
| Netro Theme   | ✅              | ✅             | ✅     | v0.1.0  |
| Types         | ✅              | ✅             | ❌     | v0.1.0  |
| Types/Create  | ✅              | ✅             | ❌     | v0.1.0  |
| Types/Update  | ✅              | ✅             | ❌     | v0.1.0  |
| Types/Read    | ✅              | ✅             | ❌     | v0.1.0  |
| Types/Delete  | ✅              | ✅             | ❌     | v0.1.0  |
| Types/Author  | ✅              | ✅             | ❌     | v0.1.0  |
| Types/Image   | ✅              | ✅             | ❌     | v0.1.0  |
| Commands      | ✅              | ✅             | ❌     | v0.1.0  |
| Helper        | ✅              | ❌             | ❌     | v0.1.0  |
| Mail          | ✅              | ✅             | ✅     | v0.1.0  |
| Auth          | ❌              | ❌             | ❌     | v0.2.0  |
| User          | ❌              | ❌             | ❌     | v0.2.0  |
| Request       | ❌              | ❌             | ❌     | v0.2.0  |
| Session       | ❌              | ❌             | ❌     | v0.2.0  |

- Request
- Session
- Cache
- Validation
- Logging
- Middleware
- Commands
- Helper
- Auth
- Mail
- Theme
- Custom Post Types
  - Create
  - Read
  - Update
  - Delete
  - Events
- Hooks
  - Filter
  - Action
