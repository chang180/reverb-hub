.PHONY: up down logs artisan migrate seed restart-reverb

up:
	docker compose up -d --build

down:
	docker compose down

logs:
	docker compose logs -f --tail=100

artisan:
	docker compose exec app php artisan $(CMD)

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed

restart-reverb:
	docker compose restart reverb
