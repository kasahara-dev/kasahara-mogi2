data:
	docker compose exec php php artisan migrate:fresh
	docker compose exec php php artisan db:seed

test:
	-docker compose exec php php artisan test
	docker compose exec php php artisan dusk