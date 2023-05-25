#!make

.SILENT:

## Colors
COLOR_RESET		?= \033[0m
COLOR_INFO      ?= \033[32m
COLOR_COMMENT   ?= \033[33m
NOW				:= $(shell date +%Y-%m-%d-%Hh%M)

MAKEFILE_PATH 	:= $(abspath $(lastword $(MAKEFILE_LIST)))
PROJECT_PATH 	:= $(dir $(MAKEFILE_PATH))
ROOT_PATH 		:= $(shell cd $(PROJECT_PATH)/../. && pwd)

ENV_FILE           	?= $(PROJECT_PATH)/docker/.env
LOCAL_ENV_FILE     	?= $(PROJECT_PATH)/docker/.env.local

DOCKER_COMPOSE      ?= @docker compose

define setup_env
	$(eval include $(1))
	$(eval export $(1))
endef

define show
	@printf "\n$(COLOR_COMMENT)%s$(COLOR_RESET)\n" $(1)
endef

define inform
	@printf "==> $(COLOR_INFO)%s$(COLOR_RESET)\n" $(1)
endef

define retrieve
	@printf "	%-50s : %s\n" $(1) $(2)
endef

load_env: $(ENV_FILE) $(LOCAL_ENV_FILE)
	$(call setup_env, $(ENV_FILE))
	$(call setup_env, $(LOCAL_ENV_FILE))

cmd-exists-%:
	@hash $(*) > /dev/null 2>&1 || \
		(echo "ERROR: '$(*)' must be installed and available on your PATH."; exit 1)

## Render Help
help:
	$(call show "Usage:")
	printf " make [COMMANDS]\n"
	$(call show "Commands:")
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-40s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)