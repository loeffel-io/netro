#!/bin/bash
set -e

./vendor/bin/phpstan analyse -c phpstan.neon -l 5 src
./vendor/bin/phpstan analyse -c phpstan.neon -l 5 tests
./vendor/bin/phpcs --standard=PSR2 --extensions=php src/ tests/
./vendor/bin/phpmd ./src/ text cleancode, codesize, controversial, design, naming, unusedcode
./vendor/bin/phpunit