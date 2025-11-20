# PawTrack API - Setup Guide

This guide will help you set up and run the PawTrack API locally.

## Prerequisites

- Docker & Docker Compose
- Git
- Make (optional, but recommended)

## Initial Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd animal-care-symfony
```

### 2. Environment Configuration

The project includes environment variables pre-configured in `docker-compose.yml`. For production, you'll need to create a `.env` file:

```bash
# Create .env file (for local development without Docker)
cat > .env << 'EOF'
APP_ENV=dev
APP_SECRET=YourRandomSecretKeyHere
DATABASE_URL="postgresql://pawtrack:pawtrack_secret@db:5432/pawtrack?serverVersion=16&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=YourJWTPassphraseHere
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
EOF
```

### 3. Build and Start Docker Containers

```bash
# Using Make (recommended)
make build
make up

# Or manually
docker compose build
docker compose up -d
```

### 4. Install Dependencies

```bash
# Using Make
make install

# Or manually
docker compose exec php composer install
```

### 5. Generate JWT Keys

JWT keys are required for authentication:

```bash
# Using Make
make jwt-generate

# Or manually
docker compose exec php sh -c "mkdir -p config/jwt && \
  openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096 && \
  openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem && \
  chmod 644 config/jwt/private.pem config/jwt/public.pem"
```

### 6. Create Database and Run Migrations

```bash
# Using Make
make db-create
make migrate

# Or manually
docker compose exec php php bin/console doctrine:database:create --if-not-exists
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

### 7. (Optional) Load Fixtures

If you want to add test data:

```bash
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

## Quick Setup (All-in-One)

If you have Make installed, you can run all setup steps at once:

```bash
make setup
```

This will: build containers, start them, install dependencies, generate JWT keys, create the database, and run migrations.

## Accessing the Application

Once setup is complete, you can access:

- **API Endpoints**: http://localhost:8080/api
- **API Documentation**: http://localhost:8080/api/docs
- **OpenAPI JSON**: http://localhost:8080/api/docs.json

## API Usage

### 1. Register a User

```bash
curl -X POST http://localhost:8080/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "SecurePassword123"
  }'
```

### 2. Login to Get JWT Token

```bash
curl -X POST http://localhost:8080/api/login_check \
  -H "Content-Type: application/json" \
  -d '{
    "username": "user@example.com",
    "password": "SecurePassword123"
  }'
```

Response:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

### 3. Use the Token for Authenticated Requests

Save the token and use it in the `Authorization` header:

```bash
TOKEN="your-jwt-token-here"

# Create an animal
curl -X POST http://localhost:8080/api/animals \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "name": "Buddy",
    "species": "DOG",
    "breed": "Golden Retriever",
    "dob": "2020-05-15",
    "weight": 30.5
  }'
```

### 4. Log a Poo Entry (Bristol Scale)

```bash
curl -X POST http://localhost:8080/api/poo_logs \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "animal": "/api/animals/{animal-id}",
    "recordedAt": "2024-01-15T10:30:00",
    "bristolScore": 4,
    "color": "BROWN",
    "contents": ["NORMAL"],
    "notes": "Healthy stool, no concerns"
  }'
```

## Development Commands

### Container Management

```bash
make up          # Start containers
make down        # Stop containers
make restart     # Restart containers
make logs        # View logs
make bash        # Enter PHP container shell
```

### Database Management

```bash
make db-create   # Create database
make db-drop     # Drop database
make db-reset    # Reset database (drop, create, migrate)
make migrate     # Run migrations
```

### Symfony Commands

```bash
make cache-clear                           # Clear cache
make console CMD="doctrine:schema:validate" # Validate schema
make console CMD="debug:router"            # List routes
make entity                                # Create new entity (interactive)
```

### Composer Commands

```bash
make composer CMD="require package/name"   # Install package
make composer CMD="update"                 # Update dependencies
```

## Database Schema Overview

The application includes the following entities:

- **User** - Pet owners/guardians (with JWT authentication)
- **Animal** - Pets (dogs, cats, other)
  - Properties: name, species, breed, dob, weight, avatar
  - Computed: age (calculated from dob)
- **PooLog** - Stool quality tracking
  - Bristol Scale: 1-7 (1-2: Constipation, 3-4: Healthy, 5-7: Diarrhea)
  - Additional: color, contents, photo URL, notes
- **MedicineLog** - Medication tracking
  - Properties: medicine name, dosage, frequency, start/end dates
  - Computed: isActive()
- **VaccineLog** - Vaccination records
  - Properties: vaccine name, batch number, administered date, next due date
  - Computed: isDue(), daysUntilDue()
- **BlogPost** - Educational content
  - Properties: title, slug, content, tags, target species
  - SEO-friendly URLs with auto-generated slugs

## Security

### Authentication

- JWT token-based authentication using `lexik/jwt-authentication-bundle`
- Tokens expire after 1 hour (configurable in `config/packages/lexik_jwt_authentication.yaml`)

### Authorization

- Security voters ensure users can only access their own animals and logs
- Blog posts: Public read access, Admin-only write access
- All API endpoints (except login/register/docs) require authentication

### Voters

- **AnimalVoter**: Ensures users can only VIEW/EDIT/DELETE their own animals
- **HealthLogVoter**: Ensures users can only VIEW/EDIT/DELETE logs for their own animals

## Troubleshooting

### Port Already in Use

If port 8080 or 5432 is already in use, edit `docker-compose.yml`:

```yaml
services:
  nginx:
    ports:
      - "8081:80"  # Change 8080 to 8081
  
  db:
    ports:
      - "5433:5432"  # Change 5432 to 5433
```

### Permission Issues with JWT Keys

```bash
docker compose exec php chmod 644 config/jwt/*.pem
```

### Database Connection Issues

Check if the database container is running:

```bash
docker compose ps
docker compose logs db
```

### Clear Cache

If you encounter strange behavior:

```bash
make cache-clear
# Or
docker compose exec php php bin/console cache:clear
```

## Testing

### Manual API Testing

Use the interactive API documentation at http://localhost:8080/api/docs to test endpoints.

### Command Line Testing

Examples are provided in the "API Usage" section above.

## Next Steps

1. ✅ Backend API is ready
2. 🔜 Create Nuxt 3 frontend
3. 🔜 Implement "Poo Chart" visualization
4. 🔜 Add file upload for pet avatars and log photos
5. 🔜 Implement notifications for vaccine reminders
6. 🔜 Add unit and integration tests

## Project Structure

```
├── config/              # Symfony configuration
│   ├── packages/       # Bundle configurations
│   │   ├── api_platform.yaml
│   │   ├── doctrine.yaml
│   │   ├── security.yaml
│   │   ├── lexik_jwt_authentication.yaml
│   │   └── nelmio_cors.yaml
│   ├── routes.yaml     # Route definitions
│   └── services.yaml   # Service container config
├── docker/              # Docker configuration
│   ├── nginx/          # Nginx config
│   └── php/            # PHP Dockerfile
├── migrations/          # Database migrations
├── public/              # Web root
│   └── index.php       # Entry point
├── src/
│   ├── Controller/     # API Controllers
│   │   └── RegistrationController.php
│   ├── Entity/         # Doctrine entities
│   │   ├── User.php
│   │   ├── Animal.php
│   │   ├── PooLog.php
│   │   ├── MedicineLog.php
│   │   ├── VaccineLog.php
│   │   └── BlogPost.php
│   ├── Enum/           # PHP 8.1+ Enums
│   │   ├── Species.php
│   │   ├── PooColor.php
│   │   └── PooContent.php
│   ├── Repository/     # Doctrine repositories
│   ├── Security/       # Security voters
│   │   └── Voter/
│   │       ├── AnimalVoter.php
│   │       └── HealthLogVoter.php
│   └── Kernel.php
├── docker-compose.yml   # Docker services
├── Makefile            # Development commands
├── README.md           # Project overview
└── SETUP.md            # This file
```

## Support

For issues or questions, please check:
- API Documentation: http://localhost:8080/api/docs
- Symfony Documentation: https://symfony.com/doc/current/index.html
- API Platform Documentation: https://api-platform.com/docs/

## License

Proprietary

