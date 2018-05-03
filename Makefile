# Makefile for Drupal 8 skeleton docker.

include .env

help:
	@echo "\n${ORANGE}usage: make ${BLUE}COMMAND${NC}"
	@echo "\n${YELLOW}Commands:${NC}"
	@echo "  ${BLUE}code-sniff            : ${LIGHT_BLUE}Check the API with PHP Code Sniffer (Drupal Standards).${NC}"
	@echo "  ${BLUE}clean                 : ${LIGHT_BLUE}Clean directories for reset.${NC}"
	@echo "  ${BLUE}c-install             : ${LIGHT_BLUE}Install PHP/Drupal dependencies with composer.${NC}"
	@echo "  ${BLUE}c-update              : ${LIGHT_BLUE}Update PHP/Drupal dependencies with composer.${NC}"
	@echo "  ${BLUE}clean-drupal-config   : ${LIGHT_BLUE}Delete exported configuration from project.${NC}"
	@echo "  ${BLUE}docker-start          : ${LIGHT_BLUE}Create and start containers.${NC}"
	@echo "  ${BLUE}docker-stop           : ${LIGHT_BLUE}Stop and clear all services.${NC}"
	@echo "  ${BLUE}gen-certs             : ${LIGHT_BLUE}Generate SSL certificates.${NC}"
	@echo "  ${BLUE}logs                  : ${LIGHT_BLUE}Follow log output.${NC}"
	@echo "  ${BLUE}mysql-dump            : ${LIGHT_BLUE}Create backup of all databases.${NC}"
	@echo "  ${BLUE}mysql-restore         : ${LIGHT_BLUE}Restore backup of all databases.${NC}"
	@echo "\n${YELLOW}Tests commands:${NC}"
	@echo "  ${BLUE}test                  : ${LIGHT_BLUE}Test all application (custom and contribution modules).${NC}"
	@echo "  ${BLUE}test-contrib          : ${LIGHT_BLUE}Test Drupal contributor modules.${NC}"
	@echo "  ${BLUE}test-custom-modules   : ${LIGHT_BLUE}Test Drupal custom modules.${NC}"
	@echo "\n${YELLOW}Drupal specific commands:${NC}"
	@echo "${RED}Important: All ${BLUE}COMMAND${NC} import automatically your \`sync\` exported configuration. You can specify another configuration to install/update/export ${YELLOW}\`eg: make ${BLUE}COMMAND${NC} my_config_name\`.${NC}"
	@echo "  ${BLUE}drupal-si             : ${LIGHT_BLUE}Install new Drupal instance and drop database.${NC}"
	@echo "  ${BLUE}drupal-update         : ${LIGHT_BLUE}Update your current Drupal instance and (re)import your \`/config\` exported configuration.${NC}"
	@echo "  ${BLUE}drupal-config-export  : ${LIGHT_BLUE}Export your current Drupal instance from \`/config\` by default. That can be in sub-folder depend your custom changes.${NC}"

init:
	@echo "${BLUE}Project configuration initialization:${NC}"
	@$(shell cp -n $(shell pwd)/docker-compose.yml.dist $(shell pwd)/docker-compose.yml 2> /dev/null)
	@$(shell cp -n $(shell pwd)/composer.json.dist $(shell pwd)/composer.json 2> /dev/null)
	@$(shell cp -n $(shell pwd)/composer.required.json.dist $(shell pwd)/composer.required.json 2> /dev/null)
	@$(shell cp -n $(shell pwd)/composer.suggested.json.dist $(shell pwd)/composer.suggested.json 2> /dev/null)
	@$(shell cp -n $(shell pwd)/settings/settings.local.php.dist $(shell pwd)/settings/settings.local.php 2> /dev/null)
	@$(shell cp -n $(shell pwd)/settings/development.services.yml.dist $(shell pwd)/settings/development.services.yml 2> /dev/null)
	@$(shell cp -n $(shell pwd)/settings/phpunit.xml.dist $(shell pwd)/settings/phpunit.xml 2> /dev/null)

clean:
	@echo "${BLUE}Clean directories:${NC}"
	@rm -Rf data/db/mysql/*
	@rm -Rf $(MYSQL_DUMPS_DIR)/*
	@rm -Rf vendor/
	@rm -Rf composer.lock
	@rm -Rf settings/settings.local.php
	@rm -Rf settings/development.services.yml
	@rm -Rf settings/phpunit.xml
	@rm -Rf report
	@rm -Rf web/
	@rm -Rf etc/ssl/*
	@rm -Rf bin/
	@rm -Rf app/Drupal/parameters.yml
	@rm -Rf composer.required.json
	@rm -Rf composer.suggested.json
	@rm -Rf composer.json
	@rm -Rf docker-compose.yml

clean-drupal-config:
	@echo "${RED}Clean exported config directories:${NC}"
	@rm -Rf config/*

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
	@docker-compose exec -T php composer site-install

drupal-update:
	@echo "${BLUE}Updating your Drupal Application:${NC}"
	@docker-compose exec -T php composer site-update

drupal-config-export:
	@echo "${BLUE}Export of your Drupal configuration:${NC}"
	@docker-compose exec -T php composer export-conf

docker-start: init gen-certs
	@echo "${BLUE}Starting all containers:${NC}"
	@docker-compose up -d
	@make c-install

docker-stop:
	@echo "${BLUE}Stopping all containers:${NC}"
	@docker-compose down -v
	@make clean

gen-certs:
	@echo "${BLUE}SSL certificate generation:${NC}"
	@docker run --rm -v $(shell pwd)/etc/ssl:/certificates -e "SERVER=$(NGINX_HOST)" jacoelho/generate-certificate

logs:
	@docker-compose logs -f

mysql-dump:
	@echo "${BLUE}Dump of all database:${NC}"
	@mkdir -p $(MYSQL_DUMPS_DIR)
	@docker exec $(shell docker-compose ps -q mysqldb) mysqldump --all-databases -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" > $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null
	@make resetOwner

mysql-restore:
	@echo "${BLUE}Restore all database:${NC}"
	@docker exec -i $(shell docker-compose ps -q mysqldb) mysql -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" < $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null

test: test-contrib test-custom-modules
	@make resetOwner

test-contrib:
	@echo "${LIGHT_BLUE}Lets go to test installed contributor modules."
	@docker-compose exec -T php bin/phpunit -c ./web/core ./web/modules/contrib/ --colors=always

test-custom-modules:
	@echo "${LIGHT_BLUE}Lets go to test custom modules."
	@docker-compose exec -T php bin/phpunit -c ./web/core ./web/modules/custom/ --colors=always

resetOwner:
	@$(shell chown -Rf $(SUDO_USER):$(shell id -g -n $(SUDO_USER)) $(MYSQL_DUMPS_DIR) "$(shell pwd)/etc/ssl" "$(shell pwd)/web" 2> /dev/null)

.PHONY: clean test code-sniff init clean-drupal-config