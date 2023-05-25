#!make

include init.mk

COMPOSE_FILE       	 ?= ./docker/docker-compose.yml
ENV_FILE           	 ?= ./docker/.env
LOCAL_ENV_FILE     	 ?= ./docker/.env.local
PROJECT_NAME     	 ?= htc-intranet
BIN_CONSOLE          ?= @php bin/console
COMPOSE_EXEC_CONSOLE ?= ${DOCKER_COMPOSE} --file ${COMPOSE_FILE} --env-file ${LOCAL_ENV_FILE} -p ${PROJECT_NAME} exec app bin/console

build-and-start: load_env
	$(call show,"Start Intranet HTC & Build ...")
	${DOCKER_COMPOSE} --file ${COMPOSE_FILE} --env-file ${LOCAL_ENV_FILE} down
	#${DOCKER_COMPOSE} --file ${COMPOSE_FILE} --env-file ${LOCAL_ENV_FILE} config --quiet
	${DOCKER_COMPOSE} --file ${COMPOSE_FILE} --env-file ${LOCAL_ENV_FILE} build
	${DOCKER_COMPOSE} --file ${COMPOSE_FILE} --env-file ${LOCAL_ENV_FILE} -p ${PROJECT_NAME} up -d

start: load_env
	$(call show,"Start Intranet HTC ...")
	${DOCKER_COMPOSE} --file ${COMPOSE_FILE} --env-file ${LOCAL_ENV_FILE} down
	#${DOCKER_COMPOSE} --file ${COMPOSE_FILE} --env-file ${LOCAL_ENV_FILE} config --quiet
	${DOCKER_COMPOSE} --file ${COMPOSE_FILE} --env-file ${LOCAL_ENV_FILE} -p ${PROJECT_NAME} up -d

# database
db-migrate: load_env
	${COMPOSE_EXEC_CONSOLE} doctrine:migrations:migrate --no-interaction

db-generate-su: load_env
	${COMPOSE_EXEC_CONSOLE} intranet_htc:generate-super-admin

db-load-fixtures: load_env
	${COMPOSE_EXEC_CONSOLE} doctrine:fixtures:load --purge-exclusions=job_sector --purge-exclusions=profile --purge-exclusions=profile_job_sector --purge-exclusions=interview --purge-exclusions=interview_category --purge-exclusions=experiences
	${COMPOSE_EXEC_CONSOLE} intranet_htc:generate-super-admin

db-inject-received-profile: load_env
	${COMPOSE_EXEC_CONSOLE} intranet_htc:data_injector --received_profile

# meilisearch
meili-create: load_env
	${COMPOSE_EXEC_CONSOLE} meili:create

# global install
install: build-and-start db-migrate db-load-fixtures meili-create db-inject-received-profile

# miscellaneous, example of use : make sf-command CMD="make:entity"
sf-command: load_env
	${COMPOSE_EXEC_CONSOLE} ${CMD}
