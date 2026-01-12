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
	docker compose exec php php artisan migrate:fresh
	docker compose exec php php artisan db:seed
	echo CREATE DATABASE demo_test$;|docker compose exec -T mysql bash -c 'mysql -u root -proot'
	cp src/.env.testing.example src/.env.testing
	docker compose exec php php artisan key:generate --env=testing
	cp src/.env.testing src/.env.dusk.local