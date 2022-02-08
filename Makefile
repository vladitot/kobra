build-kobra:
	php kobra app:build --build-version='1'

load:
	make build-kobra
	mv builds/kobra /usr/local/bin/kobra
