DOCKER:=docker

build-images:
	cd infra/images/php8.1 && $(DOCKER) build --target base-xdebug -t local/kobra-docker .
	cd infra/images/php8.1 && $(DOCKER) build --target composer-local -t local/kobra-composer .

build-app:
	$(DOCKER) run --rm -it -v `pwd`/:/var/www -v ~/.ssh:/home/professional3/.ssh \
		-v ~/.composer:/home/professional3/.composer --entrypoint bash --user professional3 \
		local/kobra-composer -c "php kobra app:build --build-version='1'"

composer:
	$(DOCKER) run --rm -it -v `pwd`/:/var/www -v ~/.ssh:/home/professional3/.ssh \
		-v ~/.composer:/home/professional3/.composer --entrypoint bash --user professional3 \
		local/kobra-composer -c "bash"

composer-install:
	$(DOCKER) run --rm -it -v `pwd`/:/var/www -v ~/.ssh:/home/professional3/.ssh \
    		-v ~/.composer:/home/professional3/.composer --entrypoint bash --user professional3 \
    		local/kobra-composer -c "composer install"

build-local-kobra:
	make build-images
	make composer-install
	make build-app
	cp builds/kobra infra/images/php8.1/kobra
	cd infra/images/php8.1 && $(DOCKER) build --target phar -t local/kobra-dev .
	rm -rf infra/images/php8.1/kobra

push-vladitot:
	$(DOCKER) tag local/kobra-dev vladitot/kobra:$(VERSION)
	$(DOCKER) push vladitot/kobra:$(VERSION)

build-full:
	make build-local-kobra
	make push-vladitot

build-latest:
	make build-full VERSION=latest

show-alias:
	@echo "alias kobra-update=\"$(DOCKER) pull vladitot/kobra:latest"
	@echo "alias kobra=\"$(DOCKER) run --rm -it -v \`pwd\`/:/var/www:ro -v \`pwd\`/infra/:/var/www/infra -v ~/.ssh/id_rsa:/tmp/.ssh/id_rsa:ro vladitot/kobra\""
