up: 
	docker-compose up -d

migration: 
	export HOST=127.0.0.1 && php bin/console make:migration

doctrine-migrate: 
	export HOST=127.0.0.1 && php bin/console doctrine:migrations:migrate --no-interaction

init: doctrine-migrate

down: 
	docker-compose down

build: 
	docker-compose build