BEHAT:=$(shell command -v behat 2> /dev/null)
PHPCS:=$(shell command -v phpcs 2> /dev/null)
COMPOSER_CMD:=$(shell command -v composer 2> /dev/null)

.DEFAULT_GOAL=all

.PHONY: all
all: test phpcs

.PHONY: clean
clean:
	rm -rf vendor
	rm -f composer.lock

.PHONY: test
test: phpunit behat

.PHONY: phpunit
phpunit: composer.lock
	vendor/bin/phpunit

.PHONY: behat
behat: composer.lock
ifndef BEHAT
    $(error "behat is not available, please install to continue")
endif
	behat --stop-on-failure

.PHONY: phpcs
phpcs: composer.lock
ifndef PHPCS
    $(error "phpcs is not available, please install to continue")
endif
	phpcs src --standard=PSR2
	phpcs tests --standard=PSR2

composer.lock: composer.json
ifndef COMPOSER_CMD
    $(error "composer is not available, please install to continue")
endif
	composer install
