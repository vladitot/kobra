build-images:
	cd infra/images/php8.1 && docker build --target base-xdebug -t local/kobra-docker .
	cd infra/images/php8.1 && docker build --target composer-local -t local/kobra-composer .

build-app:
	docker run --rm -it -v `pwd`/:/var/www -v ~/.ssh:/home/professional3/.ssh \
		-v ~/.composer:/home/professional3/.composer --entrypoint bash --user professional3 \
		local/kobra-composer -c "php kobra app:build --build-version='1'"

composer:
	docker run --rm -it -v `pwd`/:/var/www -v ~/.ssh:/home/professional3/.ssh \
		-v ~/.composer:/home/professional3/.composer --entrypoint bash --user professional3 \
		local/kobra-composer -c "bash"

composer-install:
	docker run --rm -it -v `pwd`/:/var/www -v ~/.ssh:/home/professional3/.ssh \
    		-v ~/.composer:/home/professional3/.composer --entrypoint bash --user professional3 \
    		local/kobra-composer -c "composer install"

build-local-kobra:
	make build-images
	make composer-install
	make build-app
	cp builds/kobra infra/images/php8.1/kobra
	cd infra/images/php8.1 && docker build --target phar -t local/kobra-dev .
	rm -rf infra/images/php8.1/kobra

push-vladitot:
	docker tag local/kobra-dev vladitot/kobra:$(VERSION)
	docker push vladitot/kobra:$(VERSION)

build-full:
	make build-local-kobra
	make push-vladitot

build-latest:
	make build-full VERSION=latest

show-alias:
	@echo "alias kobra-update=\"docker pull vladitot/kobra:latest"
	@echo "alias kobra=\"docker run --rm -it -v \`pwd\`/:/var/www:ro -v \`pwd\`/infra/:/var/www/infra -v ~/.ssh/id_rsa:/tmp/.ssh/id_rsa:ro vladitot/kobra\""
