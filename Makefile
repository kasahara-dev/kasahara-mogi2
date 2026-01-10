data:
	docker compose exec php php artisan migrate:fresh
	docker compose exec php php artisan db:seed

test:
	-docker compose exec php php artisan test
	docker compose exec php php artisan dusk

init:
	docker-compose up -d --build
	docker compose exec php composer instal
	cp src/.env.example src/.env
	docker compose exec php php artisan key:generate
	docker compose exec mysql bash -c 'mysql -u root -p root CREATE DATABASE demo_test;'
	docker compose exec php php artisan migrate
	docker compose exec php php artisan db:seed
