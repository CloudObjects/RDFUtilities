.PHONY: all

all: vendor build

composer.lock: composer.json
	# Updating Dependencies with Composer
	composer update -o

vendor: composer.lock
	# Installing Dependencies with Composer
	composer install -o

sami.phar:
	# Get a copy of sami (only on PHP 7)
	@if [ `php -v | awk '{ if ($$1 == "PHP") { print substr($$2,0,1) }}'` = "7" ]; then\
		wget http://get.sensiolabs.org/sami.phar;\
	fi

build: sami.phar
	# Building documentation with sami.phar (only on PHP 7)
	@if [ `php -v | awk '{ if ($$1 == "PHP") { print substr($$2,0,1) }}'` = "7" ]; then\
		php sami.phar update sami-config.php --force;\
    fi