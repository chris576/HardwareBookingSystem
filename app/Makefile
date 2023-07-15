up: 
	docker-compose up -d

doctrine-migrate: 
	HOST=127.0.0.1 && php bin/console doctrine:migrations:migrate --no-interaction

init: doctrine-migrate

down: 
	docker-compose down

build: 
	docker-compose build