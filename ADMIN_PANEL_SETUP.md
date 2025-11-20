# Admin Panel Setup Guide

## Option 1: EasyAdmin 4 (Recommended) ⭐

**Best for:** Traditional admin panel with CRUD operations

### Features
- ✅ Auto-discovers all your entities
- ✅ Full CRUD (Create, Read, Update, Delete)
- ✅ Search, filters, and pagination
- ✅ Batch actions
- ✅ File uploads
- ✅ Custom actions and fields
- ✅ Role-based access control
- ✅ Beautiful Bootstrap 5 UI
- ✅ Works perfectly with Symfony 7.3

### Installation

```bash
# Using Docker
docker compose exec php composer require easycorp/easyadmin-bundle --dev

# Or without Docker
composer require easycorp/easyadmin-bundle --dev
```

### Quick Start

#### 1. Create Dashboard Controller

```bash
docker compose exec php php bin/console make:admin:dashboard
```

This creates `src/Controller/Admin/DashboardController.php`

#### 2. Create CRUD Controllers

```bash
# For each entity:
docker compose exec php php bin/console make:admin:crud

# You'll be prompted to select:
# - User
# - Animal
# - PooLog
# - MedicineLog
# - VaccineLog
# - BlogPost
```

Or manually create them (see examples below).

### Manual Setup

#### Dashboard Controller

Create `src/Controller/Admin/DashboardController.php`:

```php
<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Animal;
use App\Entity\PooLog;
use App\Entity\MedicineLog;
use App\Entity\VaccineLog;
use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('PawTrack Admin')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        
        yield MenuItem::section('Animals');
        yield MenuItem::linkToCrud('Animals', 'fa fa-paw', Animal::class);
        
        yield MenuItem::section('Health Logs');
        yield MenuItem::linkToCrud('Poo Logs', 'fa fa-toilet', PooLog::class);
        yield MenuItem::linkToCrud('Medicine Logs', 'fa fa-pills', MedicineLog::class);
        yield MenuItem::linkToCrud('Vaccine Logs', 'fa fa-syringe', VaccineLog::class);
        
        yield MenuItem::section('Content');
        yield MenuItem::linkToCrud('Blog Posts', 'fa fa-newspaper', BlogPost::class);
    }
}
```

#### CRUD Controllers

**Animal CRUD** - `src/Controller/Admin/AnimalCrudController.php`:

```php
<?php

namespace App\Controller\Admin;

use App\Entity\Animal;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use App\Enum\Species;

class AnimalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Animal::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('owner'),
            TextField::new('name'),
            ChoiceField::new('species')
                ->setChoices([
                    'Dog' => Species::DOG,
                    'Cat' => Species::CAT,
                    'Other' => Species::OTHER,
                ]),
            TextField::new('breed'),
            DateField::new('dob', 'Date of Birth'),
            NumberField::new('weight')->setNumDecimals(2),
            TextField::new('avatar')->hideOnIndex(),
        ];
    }
}
```

**PooLog CRUD** - `src/Controller/Admin/PooLogCrudController.php`:

```php
<?php

namespace App\Controller\Admin;

use App\Entity\PooLog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use App\Enum\PooColor;

class PooLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PooLog::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('animal'),
            DateTimeField::new('recordedAt'),
            IntegerField::new('bristolScore')
                ->setHelp('1-2: Constipation, 3-4: Healthy, 5-7: Diarrhea'),
            ChoiceField::new('color')
                ->setChoices([
                    'Brown' => PooColor::BROWN,
                    'Black' => PooColor::BLACK,
                    'Red' => PooColor::RED,
                    'Yellow' => PooColor::YELLOW,
                    'Green' => PooColor::GREEN,
                    'White' => PooColor::WHITE,
                ]),
            TextField::new('photoUrl')->hideOnIndex(),
            TextareaField::new('notes')->hideOnIndex(),
        ];
    }
}
```

**BlogPost CRUD** - `src/Controller/Admin/BlogPostCrudController.php`:

```php
<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use App\Enum\Species;

class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            SlugField::new('slug')->setTargetFieldName('title'),
            TextEditorField::new('content')->hideOnIndex(),
            ArrayField::new('tags'),
            ChoiceField::new('targetSpecies')
                ->setChoices([
                    'All' => null,
                    'Dog' => Species::DOG,
                    'Cat' => Species::CAT,
                    'Other' => Species::OTHER,
                ]),
            AssociationField::new('author'),
            BooleanField::new('published'),
            DateTimeField::new('publishedAt')->hideOnForm(),
            TextField::new('featuredImage')->hideOnIndex(),
            TextareaField::new('excerpt')->hideOnIndex(),
        ];
    }
}
```

#### Dashboard Template

Create `templates/admin/dashboard.html.twig`:

```twig
{% extends '@EasyAdmin/page/content.html.twig' %}

{% block content_title %}
    <h1>🐾 PawTrack Admin Dashboard</h1>
{% endblock %}

{% block main %}
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Welcome to PawTrack Admin</h5>
                    <p class="card-text">Manage users, animals, health logs, and blog posts.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Quick Stats</h5>
                    <p class="card-text">
                        <i class="fa fa-paw"></i> Animals: {{ ea.crud.countEntities('App\\Entity\\Animal') }}<br>
                        <i class="fa fa-user"></i> Users: {{ ea.crud.countEntities('App\\Entity\\User') }}<br>
                        <i class="fa fa-newspaper"></i> Blog Posts: {{ ea.crud.countEntities('App\\Entity\\BlogPost') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

### Security Configuration

Update `config/packages/security.yaml` to allow admin access:

```yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: ^/api/v1/login, roles: PUBLIC_ACCESS }
    - { path: ^/api/v1/register, roles: PUBLIC_ACCESS }
    - { path: ^/api/docs, roles: PUBLIC_ACCESS }
    - { path: ^/api/v1, roles: IS_AUTHENTICATED_FULLY }
```

### Access the Admin Panel

1. **Create an admin user** (via command or database):

```bash
docker compose exec php php bin/console
```

Then in PHP console:
```php
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

$em = $this->getContainer()->get('doctrine')->getManager();
$hasher = $this->getContainer()->get(UserPasswordHasherInterface::class);

$admin = new User();
$admin->setEmail('admin@pawtrack.com');
$admin->setPassword($hasher->hashPassword($admin, 'admin123'));
$admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

$em->persist($admin);
$em->flush();
```

2. **Login at:** http://localhost:8080/admin

---

## Option 2: API Platform Admin 🔷

**Best for:** If you want a React-based admin that auto-generates from your API

### Features
- ✅ Auto-generates from API Platform metadata
- ✅ React-based SPA
- ✅ Separate from Symfony (can deploy independently)
- ✅ Consumes your REST API
- ✅ Material-UI design

### Setup

This is a **separate React application**:

```bash
npm install -g @api-platform/admin
api-platform-admin http://localhost:8080/api
```

Or use Create React App:

```bash
npx create-react-app my-admin
cd my-admin
npm install @api-platform/admin
```

Then configure it to point to your API:

```javascript
// src/App.js
import { HydraAdmin } from "@api-platform/admin";

export default () => (
  <HydraAdmin entrypoint="http://localhost:8080/api/v1" />
);
```

---

## Comparison

| Feature | EasyAdmin 4 | API Platform Admin |
|---------|-------------|-------------------|
| **Setup Time** | 5 minutes | 30 minutes |
| **Technology** | PHP/Twig | React/TypeScript |
| **Integration** | Native Symfony | External SPA |
| **Customization** | Very flexible | React components |
| **Learning Curve** | Easy | Moderate |
| **Best For** | Backend admins | Frontend devs |
| **Deployment** | Same as Symfony | Separate deployment |
| **Access Control** | Symfony Security | JWT tokens |

---

## Recommendation for PawTrack

**Use EasyAdmin 4** because:
1. ✅ Faster setup (5 minutes vs 30 minutes)
2. ✅ No separate deployment needed
3. ✅ Uses existing Symfony security
4. ✅ Better for internal admin panel
5. ✅ No need to maintain separate React app
6. ✅ Perfect for managing users, animals, and blog posts

**Use API Platform Admin if:**
- You want to give non-technical users access (prettier UI)
- You want to deploy admin panel separately
- Your team prefers React
- You need offline-first capabilities

---

## Installation Steps for EasyAdmin

```bash
# 1. Install bundle
docker compose exec php composer require easycorp/easyadmin-bundle --dev

# 2. Create dashboard
docker compose exec php php bin/console make:admin:dashboard

# 3. Create CRUD controllers
docker compose exec php php bin/console make:admin:crud
# Select: Animal, PooLog, MedicineLog, VaccineLog, BlogPost, User

# 4. Create an admin user (see above)

# 5. Access admin panel
open http://localhost:8080/admin
```

---

## Resources

- **EasyAdmin Docs:** https://symfony.com/bundles/EasyAdminBundle/current/index.html
- **API Platform Admin:** https://api-platform.com/docs/admin/
- **Sonata Admin** (older alternative): https://sonata-project.org/

---

## Next Steps

1. Install EasyAdmin: `docker compose exec php composer require easycorp/easyadmin-bundle --dev`
2. Generate controllers: `php bin/console make:admin:dashboard`
3. Create admin user with ROLE_ADMIN
4. Login at http://localhost:8080/admin
5. Start managing your data! 🎉

