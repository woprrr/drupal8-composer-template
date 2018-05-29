# Makefile for Drupal 8 skeleton docker.

include .env

help:
	@echo "\n${ORANGE}usage: make ${BLUE}COMMAND${NC}"
	@echo "\n${YELLOW}Commands:${NC}"
	@echo "  ${BLUE}drupal-si             : ${LIGHT_BLUE}Install new Drupal instance and drop database.${NC}"
	@echo "  ${BLUE}drupal-update         : ${LIGHT_BLUE}Update your current Drupal instance and (re)import your \`/config\` exported configuration.${NC}"
	@echo "  ${BLUE}drupal-config-export  : ${LIGHT_BLUE}Export your current Drupal instance from \`/config\` by default. That can be in sub-folder depend your custom changes.${NC}"

init:
	@echo "${BLUE}Project configuration initialization:${NC}"
	@$(shell cp -n $(shell pwd)/app/drupal8/composer.json.dist $(shell pwd)/app/drupal8/composer.json 2> /dev/null)
	@$(shell cp -n $(shell pwd)/app/drupal8/composer.required.json.dist $(shell pwd)/app/drupal8/composer.required.json 2> /dev/null)
	@$(shell cp -n $(shell pwd)/app/drupal8/composer.suggested.json.dist $(shell pwd)/app/drupal8/composer.suggested.json 2> /dev/null)
	@$(shell cp -n $(shell pwd)/app/drupal8/settings/settings.local.php.dist $(shell pwd)/app/drupal8/settings/settings.local.php 2> /dev/null)
	@$(shell cp -n $(shell pwd)/app/drupal8/settings/development.services.yml.dist $(shell pwd)/app/drupal8/settings/development.services.yml 2> /dev/null)
	@$(shell cp -n $(shell pwd)/app/drupal8/settings/phpunit.xml.dist $(shell pwd)/app/drupal8/settings/phpunit.xml 2> /dev/null)

clean:
	@echo "${BLUE}Clean directories:${NC}"
	@rm -Rf ./vendor/
	@rm -Rf ./composer.lock
	@rm -Rf ./settings/settings.local.php
	@rm -Rf ./settings/development.services.yml
	@rm -Rf ./settings/phpunit.xml
	@rm -Rf ./web/
	@rm -Rf ./bin/
	@rm -Rf app/Drupal/parameters.yml
	@rm -Rf composer.required.json
	@rm -Rf composer.suggested.json
	@rm -Rf composer.json

clean-drupal-config:
	@echo "${RED}Clean exported config directories:${NC}"
	@rm -Rf ./config/*

code-sniff:
	@echo "${BLUE}Check your Drupal project with PHP Code Sniffer:${NC}"
	@docker-compose exec -T php composer phpcs

c-update:
	@echo "${BLUE}Updating your application dependencies:${NC}"
	@docker-compose exec -T php composer update

c-install:
	@echo "${BLUE}Installing your application dependencies:${NC}"
	@docker-compose exec -T php composer install
	@echo "\n${BLUE}Initialize phpunit Drupal Core file:${NC}"
	@$(shell cp -n $(shell pwd)/settings/phpunit.xml $(shell pwd)/web/core/phpunit.xml 2> /dev/null)

drupal-si:
	@echo "${BLUE}Installing your Drupal Application:${NC}"
	# Restart PHP-FPM to avoid caches of container if you change ENV variables.
	@docker-compose up -d php
	@docker-compose exec -T php composer site-install

drupal-update:
	@echo "${BLUE}Updating your Drupal Application:${NC}"
	# Restart PHP-FPM to avoid caches of container if you change ENV variables.
	@docker-compose up -d php
	@docker-compose exec -T php composer site-update

drupal-config-export:
	@echo "${BLUE}Export of your Drupal configuration:${NC}"
	@docker-compose exec -T php composer export-conf

docker-start: init
	@echo "${BLUE}Starting all containers:${NC}"
	@docker-compose up -d
	@make c-install

docker-stop:
	@echo "${BLUE}Stopping all containers:${NC}"
	@docker-compose down -v
	@make clean

.PHONY: clean test code-sniff init clean-drupal-config