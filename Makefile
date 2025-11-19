data:
	docker compose exec php php artisan migrate:fresh
	docker compose exec php php artisan db:seed