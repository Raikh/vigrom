compose-migrate:
	docker-compose exec app bash -c "php artisan migrate:fresh"

compose-seed:
	docker-compose exec app bash -c "php artisan db:seed --class='DatabaseSeeder'"

compose-env:
	cp ./.env.example ./.env

compose-up:
	docker-compose up --build -d app mysql nginx

compose-init:
	docker-compose exec app bash -c "composer install && php artisan migrate:fresh && php artisan optimize"

compose-seed:
	docker-compose exec app bash -c "php artisan db:seed --class=DatabaseSeeder"

compose-down:
	docker-compose down

compose-ps:
	docker-compose ps

compose-test:
	docker-compose exec app bash -c "./vendor/bin/phpunit tests"

compose-refresh-rates:
	docker-compose exec app bash -c "php artisan refresh_rates"
