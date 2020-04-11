#!/bin/bash

UID = 1000

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

generate-ssh-keys: ## Generate ssh keys in the container
	md config\jwt
	openssl genrsa -passout pass:sf5-expenses-api -out config/jwt/private.pem -aes256 4096
	openssl rsa -pubout -passin pass:sf5-expenses-api -in config/jwt/private.pem -out config/jwt/public.pem	