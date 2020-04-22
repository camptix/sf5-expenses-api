#!/bin/bash

UID = 1000

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

prepare:
	composer-install
	migrations

composer-install:
	composer install --no-scripts --no-interaction --optimize-autoloader

migrations:
	symfony console doctrine:migrations:migrate -n

code-style: ## Runs php-cs to fix code styling following Symfony rules
	php-cs-fixer fix src --rules=@Symfony
	php-cs-fixer fix tests --rules=@Symfony

generate-ssh-keys: ## Generate ssh keys in the container
	md config\jwt
	openssl genrsa -passout pass:sf5-expenses-api -out config/jwt/private.pem -aes256 4096
	openssl rsa -pubout -passin pass:sf5-expenses-api -in config/jwt/private.pem -out config/jwt/public.pem