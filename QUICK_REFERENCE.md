# PawTrack API - Quick Reference

## 🚀 Quick Start Commands

```bash
# Initial setup (one command does everything)
make setup

# Or step by step:
make build          # Build Docker containers
make up             # Start containers
make install        # Install dependencies
make jwt-generate   # Generate JWT keys
make db-create      # Create database
make migrate        # Run migrations
```

## 🐳 Docker Commands

```bash
make up             # Start all containers
make down           # Stop all containers
make restart        # Restart containers
make logs           # View logs (follow mode)
make bash           # Enter PHP container
docker compose ps   # Check container status
```

## 🗄️ Database Commands

```bash
make db-create      # Create database
make db-drop        # Drop database
make db-reset       # Drop, create, migrate (fresh start)
make migrate        # Run migrations
make console CMD="doctrine:schema:validate"  # Validate schema
```

## 🔧 Development Commands

```bash
make cache-clear    # Clear Symfony cache
make entity         # Create new entity (interactive)
make console CMD="debug:router"             # List all routes
make console CMD="debug:container"          # List services
make composer CMD="require package/name"    # Install package
```

## 🧪 Testing

```bash
./test-api.sh       # Run automated API test
```

## 🌐 URLs

- **API Base (v1)**: http://localhost:8080/api/v1
- **API Docs**: http://localhost:8080/api/docs
- **OpenAPI**: http://localhost:8080/api/docs.json

## 📡 API Endpoints Quick Reference

### Authentication (No token required)

```bash
# Register
POST /api/v1/register
{
  "email": "user@example.com",
  "password": "password123"
}

# Login (get JWT token)
POST /api/v1/login_check
{
  "username": "user@example.com",
  "password": "password123"
}
```

### Animals (Token required: Bearer {token})

```bash
# List user's animals
GET /api/v1/animals

# Create animal
POST /api/v1/animals
{
  "name": "Buddy",
  "species": "DOG",
  "breed": "Golden Retriever",
  "dob": "2020-05-15",
  "weight": 30.5
}

# Get single animal
GET /api/v1/animals/{id}

# Update animal
PUT /api/v1/animals/{id}

# Delete animal
DELETE /api/v1/animals/{id}
```

### Poo Logs (Token required)

```bash
# Create poo log
POST /api/v1/poo_logs
{
  "animal": "/api/v1/animals/{animal-id}",
  "recordedAt": "2024-01-15T10:30:00",
  "bristolScore": 4,
  "color": "BROWN",
  "contents": ["NORMAL"],
  "notes": "Healthy stool"
}

# List poo logs
GET /api/v1/poo_logs

# Get single log
GET /api/v1/poo_logs/{id}
```

### Medicine Logs (Token required)

```bash
# Create medicine log
POST /api/v1/medicine_logs
{
  "animal": "/api/v1/animals/{animal-id}",
  "medicineName": "Heartgard Plus",
  "dosage": "25mg",
  "frequency": "Once monthly",
  "startDate": "2024-01-01",
  "endDate": null,
  "prescribedBy": "Dr. Smith"
}

# List medicine logs
GET /api/v1/medicine_logs
```

### Vaccine Logs (Token required)

```bash
# Create vaccine log
POST /api/v1/vaccine_logs
{
  "animal": "/api/v1/animals/{animal-id}",
  "vaccineName": "Rabies",
  "batchNumber": "RB12345",
  "administeredAt": "2024-01-15",
  "nextDueDate": "2025-01-15",
  "clinicName": "Happy Paws Clinic",
  "veterinarianName": "Dr. Johnson"
}

# List vaccine logs
GET /api/v1/vaccine_logs
```

### Blog Posts (Read: Public, Write: Admin only)

```bash
# List published posts
GET /api/v1/blog_posts

# Get single post
GET /api/v1/blog_posts/{id}

# Create post (admin only)
POST /api/v1/blog_posts
{
  "title": "10 Tips for Puppy Training",
  "content": "Lorem ipsum...",
  "tags": ["PUPPY", "TRAINING"],
  "targetSpecies": "DOG",
  "author": "/api/v1/users/{admin-id}",
  "published": true
}
```

## 🔑 Environment Variables

```env
APP_ENV=dev
APP_SECRET=ChangeThisInProduction
DATABASE_URL=postgresql://pawtrack:pawtrack_secret@db:5432/pawtrack
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=ChangeThisInProduction
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

## 📊 Enums Reference

### Species
- `DOG`
- `CAT`
- `OTHER`

### PooColor
- `BROWN`
- `BLACK`
- `RED`
- `YELLOW`
- `GREEN`
- `WHITE`

### PooContent (array field)
- `NORMAL`
- `MUCUS`
- `BLOOD`
- `WORMS`
- `FOREIGN_OBJECT`

### Bristol Scale (1-7)
- **1-2**: Constipation (Yellow zone)
- **3-4**: Healthy/Normal (Green zone)
- **5-7**: Diarrhea (Red zone)

## 🔐 User Roles

- `ROLE_USER` - Default role for all registered users
- `ROLE_ADMIN` - Required for blog post management

## 🐛 Troubleshooting

### Port already in use
Edit `docker-compose.yml` and change:
- `8080:80` to `8081:80` (nginx)
- `5432:5432` to `5433:5432` (postgres)

### Permission denied on JWT keys
```bash
docker compose exec php chmod 644 config/jwt/*.pem
```

### Database connection refused
```bash
docker compose ps          # Check if db container is running
docker compose logs db     # Check database logs
make db-create             # Recreate database
```

### Token expired
Login again to get a new token (tokens expire after 1 hour)

### Clear cache after changes
```bash
make cache-clear
```

## 📞 Support

- **API Docs**: http://localhost:8080/api/docs
- **Symfony Docs**: https://symfony.com/doc/current/
- **API Platform Docs**: https://api-platform.com/docs/

## 🎓 Curl Examples

### Complete workflow

```bash
# 1. Register
curl -X POST http://localhost:8080/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"pass123"}'

# 2. Login
TOKEN=$(curl -s -X POST http://localhost:8080/api/v1/login_check \
  -H "Content-Type: application/json" \
  -d '{"username":"test@example.com","password":"pass123"}' \
  | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

# 3. Create animal
curl -X POST http://localhost:8080/api/v1/animals \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"name":"Buddy","species":"DOG","breed":"Golden Retriever","dob":"2020-05-15","weight":30.5}'

# 4. List animals
curl -X GET http://localhost:8080/api/v1/animals \
  -H "Authorization: Bearer $TOKEN"
```

## 📁 Project Structure

```
animal-care-symfony/
├── config/                 # Configuration files
│   ├── packages/          # Bundle configs
│   ├── routes/            # Route definitions
│   └── services.yaml      # Service container
├── docker/                # Docker configuration
│   ├── nginx/            # Nginx config
│   └── php/              # PHP Dockerfile
├── migrations/           # Database migrations
├── public/               # Web root
├── src/
│   ├── Controller/      # Controllers
│   ├── Entity/          # Doctrine entities
│   ├── Enum/            # PHP enums
│   ├── Repository/      # Repositories
│   └── Security/        # Voters
├── docker-compose.yml   # Docker services
├── Makefile            # Development commands
├── test-api.sh         # API test script
├── README.md           # Project overview
├── SETUP.md            # Setup guide
├── PROJECT_SUMMARY.md  # Detailed summary
└── QUICK_REFERENCE.md  # This file
```

## 💡 Tips

- Use `make bash` to explore the PHP container
- Check logs with `make logs` if something fails
- JWT tokens are in `Authorization: Bearer {token}` header
- All IDs are UUIDs (format: `550e8400-e29b-41d4-a716-446655440000`)
- API Platform auto-generates documentation - just add entities!
- Use the Swagger UI at `/api/docs` for interactive testing

