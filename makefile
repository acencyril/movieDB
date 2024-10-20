.PHONY: help
.DEFAULT_GOAL = help

DOCKER_COMPOSE=@docker-compose
DOCKER_COMPOSE_EXEC=$(DOCKER_COMPOSE) exec
PHP_DOCKER_COMPOSE_EXEC=$(DOCKER_COMPOSE_EXEC) php
COMPOSER=$(PHP_DOCKER_COMPOSE_EXEC) php -d memory_limit=-1 /usr/local/bin/composer
SYMFONY_CONSOLE=$(PHP_DOCKER_COMPOSE_EXEC) bin/console

## —— Docker 🐳  ———————————————————————————————————————————————————————————————

start: ## Start containers
	$(DOCKER_COMPOSE) up -d

stop: ## Stop containers
	docker-compose stop

build: ## build containers
	docker-compose build

down: ## Down containers
	docker-compose down

rm:	stop ## Supprimer les containers docker
	$(DOCKER_COMPOSE) rm -f

php_sh: ## Connect to themoviedb_php container
	docker exec -it themoviedb_php /bin/bash

logs: ## tails docker logs
	docker-compose logs -f

## —— Symfony 🎶 ———————————————————————————————————————————————————————————————
## necessary to add path of bdw

vendor-install:	## Installation des vendors
	$(COMPOSER) install

vendor-update:	## Mise à jour des vendors
	$(COMPOSER) update

clean-vendor: cc-hard ## Suppression du répertoire vendor puis un réinstall
	$(PHP_DOCKER_COMPOSE_EXEC) rm -Rf vendor
	$(PHP_DOCKER_COMPOSE_EXEC) rm composer.lock
	$(COMPOSER) install

cc:	## Vider le cache
	$(SYMFONY_CONSOLE) c:c

cc-hard: ## Supprimer le répertoire cache
	$(PHP_DOCKER_COMPOSE_EXEC) rm -fR var/cache/*

## —— Others 🛠️️ ———————————————————————————————————————————————————————————————
help: ## Liste des commandes
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
