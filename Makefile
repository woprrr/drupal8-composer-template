# Makefile for Drupal 8 skeleton docker.

include drupal8/.env

# Shell colors.
RED=\033[0;31m
LIGHT_RED=\033[1;31m
GREEN=\033[0;32m
LIGHT_GREEN=\033[1;32m
ORANGE=\033[0;33m
YELLOW=\033[1;33m
BLUE=\033[0;34m
LIGHT_BLUE=\033[1;34m
PURPLE=\033[0;35m
LIGHT_PURPLE=\033[1;35m
CYAN=\033[0;36m
LIGHT_CYAN=\033[1;36m
NC=\033[0m

help:
	@echo "\n${ORANGE}usage: make ${BLUE}COMMAND${NC}"
	@echo "\n${YELLOW}Commands:${NC}"
	@echo "  ${BLUE}drupal8-si            : ${LIGHT_BLUE}Install new Drupal instance and drop database.${NC}"
	@echo "  ${BLUE}drupal8-su            : ${LIGHT_BLUE}Update your current Drupal instance and (re)import your \`/config\` exported configuration.${NC}"
	@echo "  ${BLUE}drupal8-cex  		  : ${LIGHT_BLUE}Export your current Drupal instance from \`/config\` by default. That can be in sub-folder depend your custom changes.${NC}"
	@echo "  ${BLUE}clean                 : ${LIGHT_BLUE}Clean directories for reset.${NC}"
	@echo "  ${BLUE}drupal8-c-install     : ${LIGHT_BLUE}Install PHP/Drupal dependencies with composer.${NC}"
	@echo "  ${BLUE}drupal8-c-update      : ${LIGHT_BLUE}Update PHP/Drupal dependencies with composer.${NC}"
	@echo "  ${BLUE}docker-start          : ${LIGHT_BLUE}Create and start containers.${NC}"
	@echo "  ${BLUE}docker-stop           : ${LIGHT_BLUE}Stop and clear all services.${NC}"
	@echo "  ${BLUE}logs                  : ${LIGHT_BLUE}Follow log output.${NC}"

init:
	@echo "${BLUE}Project configuration initialization:${NC}"
	@$(shell cp -n $(shell pwd)/docker-compose.yml.dist $(shell pwd)/docker-compose.yml 2> /dev/null)
	@$(shell cp -n $(shell pwd)/drupal8/composer.json.dist $(shell pwd)/drupal8/composer.json 2> /dev/null)
	@$(shell cp -n $(shell pwd)/drupal8/composer.required.json.dist $(shell pwd)/drupal8/composer.json 2> /dev/null)
	@$(shell cp -n $(shell pwd)/drupal8/composer.suggested.json.dist $(shell pwd)/drupal8/composer.json 2> /dev/null)
	@$(shell cp -n $(shell pwd)/drupal8/settings/settings.local.php.dist $(shell pwd)/drupal8/settings/settings.local.php 2> /dev/null)
	@$(shell cp -n $(shell pwd)/drupal8/settings/development.services.yml.dist $(shell pwd)/drupal8/settings/development.services.yml 2> /dev/null)
	@$(shell cp -n $(shell pwd)/drupal8/settings/phpunit.xml.dist $(shell pwd)/drupal8/settings/phpunit.xml 2> /dev/null)

clean:
	@echo "${BLUE}Clean directories:${NC}"
	@rm -Rf drupal8//vendor/
	@rm -Rf drupal8//composer.lock
	@rm -Rf drupal8//settings/settings.local.php
	@rm -Rf drupal8//settings/development.services.yml
	@rm -Rf drupal8//settings/phpunit.xml
	@rm -Rf drupal8//web/
	@rm -Rf drupal8//bin/
	@rm -Rf drupal8/app/Drupal/parameters.yml
	@rm -Rf drupal8/composer.required.json
	@rm -Rf drupal8/composer.suggested.json
	@rm -Rf drupal8/composer.json

drupal8-clean-drupal-config:
	@echo "${RED}Clean exported config directories:${NC}"
	@rm -Rf ./config/*

drupal8-code-sniff:
	@echo "${BLUE}Check your Drupal project with PHP Code Sniffer:${NC}"
	@docker-compose exec -T php composer phpcs

drupal8-c-update:
	@echo "${BLUE}Updating your application dependencies:${NC}"
	@docker-compose exec -T php composer update

drupal8-c-install:
	@echo "${BLUE}Installing your application dependencies:${NC}"
	@docker-compose exec -T php composer install

drupal8-si:
	@echo "${BLUE}Installing your Drupal Application:${NC}"
	# Restart PHP-FPM to avoid caches of container if you change ENV variables.
	@docker-compose up -d php
	@docker-compose exec -T php composer site-install

drupal8-su:
	@echo "${BLUE}Updating your Drupal Application:${NC}"
	# Restart PHP-FPM to avoid caches of container if you change ENV variables.
	@docker-compose up -d php
	@docker-compose exec -T php composer site-update

drupal8-cex:
	@echo "${BLUE}Export of your Drupal configuration:${NC}"
	@docker-compose exec -T php composer export-conf

docker-start: init
	@echo "${BLUE}Starting all containers:${NC}"
	@docker-compose up -d

docker-stop:
	@echo "${BLUE}Stopping all containers:${NC}"
	@docker-compose down -v

logs:
	@docker-compose logs -f

.PHONY: clean drupal8-code-sniff init logs