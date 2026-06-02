
# About Project

MetrNaMetr.

## Installation
```
npm install
composer install
```

## Docker

```
docker-compose up -d
docker-compose down
docker-compose up -d --no-deps --build
```

### Install composer packages
```
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
```

## Database

Run migration and seeders.

```
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan db:seed --class=DemoSeeder
docker-compose exec app php artisan migrate:fresh --seed
```

## Git Flow

Git Flow:

* `master` - master;
* `develop` - develop;
* `feature/*` - ветки по фичам, бранчинг от develop;
* `hotfix/*` - ветки по хотфиксам, бранчинг от master;
