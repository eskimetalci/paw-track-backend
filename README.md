# PawTrack API

A robust Symfony REST API backend for tracking pet health and well-being.

## Tech Stack

- **Backend:** Symfony 7.3 + API Platform 3
- **Database:** PostgreSQL 16
- **Authentication:** JWT (lexik/jwt-authentication-bundle)
- **Containerization:** Docker + Docker Compose

## Features

- Multi-pet management per user
- Health logging (Medication, Vaccinations, Stool Quality/Bristol Score)
- Blog/Educational content system
- Mobile-ready REST API (Nuxt frontend ready)

## Quick Start

### Prerequisites

- Docker & Docker Compose
- Make (optional, for convenience commands)

### Installation

1. **Clone and setup:**
   ```bash
   git clone <repository-url>
   cd animal-care-symfony
   ```

2. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

3. **Build and start containers:**
   ```bash
   make setup
   ```
   
   Or manually:
   ```bash
   docker-compose build
   docker-compose up -d
   docker-compose exec php composer install
   docker-compose exec php sh -c "mkdir -p config/jwt && openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096 && openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem"
   docker-compose exec php php bin/console doctrine:database:create
   docker-compose exec php php bin/console doctrine:migrations:migrate
   ```

4. **Access the API:**
   - API: http://localhost:8080/api/v1
   - API Docs: http://localhost:8080/api/docs
   - Admin Panel: http://localhost:8080/admin (optional, see `ADMIN_QUICK_START.md`)

## Development Commands

Using Make (recommended):

```bash
make help              # Show all available commands
make up                # Start containers
make down              # Stop containers
make install           # Install dependencies
make migrate           # Run migrations
make cache-clear       # Clear Symfony cache
make logs              # View logs
make bash              # Enter PHP container
```

## Database Schema

### Entities

- **User** - Pet owners/guardians
- **Animal** - Pets (dogs, cats, other)
- **PooLog** - Stool quality tracking (Bristol Scale 1-7)
- **MedicineLog** - Medication tracking
- **VaccineLog** - Vaccination records
- **BlogPost** - Educational content

## API Endpoints

After running migrations, API Platform auto-generates endpoints:

**Version 1 (v1) - Current:**
- `GET/POST /api/v1/animals` - List/Create animals
- `GET/PUT/DELETE /api/v1/animals/{id}` - View/Update/Delete animal
- `GET/POST /api/v1/poo_logs` - List/Create poo logs
- `GET/POST /api/v1/medicine_logs` - List/Create medicine logs
- `GET/POST /api/v1/vaccine_logs` - List/Create vaccine logs
- `GET/POST /api/v1/blog_posts` - List/Create blog posts

See `API_VERSIONING.md` for details on version management.

## Security

- JWT token-based authentication
- Voters ensure users can only access their own animals
- Blog posts: Public read, Admin write

## Project Structure

```
├── config/              # Symfony configuration
├── docker/              # Docker configuration
│   ├── nginx/          # Nginx config
│   └── php/            # PHP Dockerfile
├── public/              # Web root
├── src/
│   ├── Controller/     # API Controllers (if needed)
│   ├── Entity/         # Doctrine entities
│   ├── Repository/     # Doctrine repositories
│   ├── Security/       # Voters and security logic
│   └── Kernel.php
├── docker-compose.yml   # Docker services
├── Makefile            # Development commands
└── README.md
```

## Environment Variables

Key variables in `.env`:

```env
APP_ENV=dev
APP_SECRET=your-secret-key
DATABASE_URL=postgresql://pawtrack:pawtrack_secret@db:5432/pawtrack
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-jwt-passphrase
```

## Development Roadmap

- [x] Phase 1: Skeleton & Docker setup
- [x] Phase 2: Core entities (User, Animal)
- [x] Phase 3: Health logs (PooLog, VaccineLog, MedicineLog)
- [x] Phase 4: JWT Authentication
- [x] Phase 5: Security voters
- [x] Phase 6: Blog system
- [ ] Phase 7: Frontend (Nuxt 3)
- [ ] Phase 8: File upload for avatars and photos
- [ ] Phase 9: Vaccine reminder notifications
- [ ] Phase 10: Data visualization (Poo Chart)

## License

Proprietary

