#!/bin/bash

set -e
./vendor/bin/phpstan analyse -c phpstan.neon -l 5 src
./vendor/bin/phpstan analyse -c phpstan.neon -l 5 tests
./vendor/bin/phpcs --standard=PSR2 --extensions=php src/ tests/
./vendor/bin/phpunit