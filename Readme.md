## Comments

### Prerequisites
    docker
    docker-compose

### First time initialization

Run these commands

1. ```docker compose up -d``` 
2. ```docker compose exec app composer install``` 
3. ```docker compose exec app yarn install``` 
4. ```docker compose exec app yarn build```
5. ```docker compose exec app php bin/console doctrine:migrations:migrate```

Your app will be running on ```127.0.0.1:8000```  
Admin will be running on ```127.0.0.1:8000/admin```

### Creating user for admin

```docker compose exec app php bin/console app:create-user username password``` 

### Generating posts

```docker compose exec app php bin/console app:generate-post```