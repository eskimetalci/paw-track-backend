# Admin Panel - Quick Start

## ⚡ 5-Minute Setup with EasyAdmin

### Step 1: Install EasyAdmin

```bash
docker compose exec php composer require easycorp/easyadmin-bundle --dev
```

### Step 2: Generate Admin Panel

```bash
# Create dashboard
docker compose exec php php bin/console make:admin:dashboard

# Create CRUD controllers for each entity
docker compose exec php php bin/console make:admin:crud
```

When prompted, select these entities one by one:
- Animal
- PooLog  
- MedicineLog
- VaccineLog
- BlogPost
- User

### Step 3: Create Admin User

```bash
# Easy way (using custom command)
make create-admin

# Or manually
docker compose exec php php bin/console app:create-admin
```

Enter:
- Email: `admin@pawtrack.com`
- Password: (your secure password)

### Step 4: Access Admin Panel

Open: **http://localhost:8080/admin**

Login with the credentials you just created.

---

## 🎯 What You Get

### Features
- ✅ Full CRUD for all entities (Animals, Logs, Users, Blog Posts)
- ✅ Search and filters
- ✅ Pagination
- ✅ Export to CSV
- ✅ Batch actions
- ✅ Beautiful Bootstrap 5 UI
- ✅ Mobile responsive

### Admin Panel URLs
- Dashboard: http://localhost:8080/admin
- Animals: http://localhost:8080/admin?crudController=AnimalCrudController
- Users: http://localhost:8080/admin?crudController=UserCrudController
- Blog Posts: http://localhost:8080/admin?crudController=BlogPostCrudController

---

## 🔐 Security

Admin panel requires `ROLE_ADMIN`:
- Already configured in `security.yaml`
- Only users with `ROLE_ADMIN` can access `/admin`
- Regular users (ROLE_USER) cannot access

---

## 📝 Customization Examples

### Add Custom Field to Animal CRUD

Edit `src/Controller/Admin/AnimalCrudController.php`:

```php
public function configureFields(string $pageName): iterable
{
    return [
        IdField::new('id')->hideOnForm(),
        TextField::new('name'),
        TextField::new('age', 'Age')->hideOnForm(), // Computed field
        // ... more fields
    ];
}
```

### Customize Dashboard

Edit `src/Controller/Admin/DashboardController.php`:

```php
public function configureMenuItems(): iterable
{
    yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
    yield MenuItem::linkToCrud('Animals', 'fa fa-paw', Animal::class)
        ->setBadge(10, 'info'); // Show count badge
    
    // Add external links
    yield MenuItem::linkToUrl('API Docs', 'fa fa-book', '/api/docs');
}
```

### Add Custom Actions

```php
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

public function configureActions(Actions $actions): Actions
{
    $exportAction = Action::new('export', 'Export')
        ->linkToRoute('admin_export_animals')
        ->createAsGlobalAction();

    return $actions
        ->add(Crud::PAGE_INDEX, $exportAction);
}
```

---

## 🎨 Theming

EasyAdmin uses Bootstrap 5. You can customize:

### Change Colors

Create `templates/bundles/EasyAdminBundle/layout.html.twig`:

```twig
{% extends '@!EasyAdmin/layout.html.twig' %}

{% block head_stylesheets %}
    {{ parent() }}
    <style>
        :root {
            --color-primary: #6366f1;
        }
    </style>
{% endblock %}
```

---

## 📊 Dashboard Widgets

Create custom dashboard widgets in `templates/admin/dashboard.html.twig`:

```twig
{% extends '@EasyAdmin/page/content.html.twig' %}

{% block main %}
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Total Animals</h5>
                    <h2>{{ animals_count }}</h2>
                </div>
            </div>
        </div>
        <!-- More widgets -->
    </div>
{% endblock %}
```

---

## 🚀 Advanced Features

### File Uploads

```php
ImageField::new('avatar')
    ->setBasePath('uploads/avatars')
    ->setUploadDir('public/uploads/avatars')
    ->setUploadedFileNamePattern('[randomhash].[extension]');
```

### Filters

```php
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

public function configureFilters(Filters $filters): Filters
{
    return $filters
        ->add('species')
        ->add('breed');
}
```

### Custom Queries

```php
public function createIndexQueryBuilder(...): QueryBuilder
{
    $qb = parent::createIndexQueryBuilder(...);
    
    // Only show user's own animals
    if (!$this->isGranted('ROLE_ADMIN')) {
        $qb->andWhere('entity.owner = :user')
           ->setParameter('user', $this->getUser());
    }
    
    return $qb;
}
```

---

## 📚 Resources

- **Full Documentation:** https://symfony.com/bundles/EasyAdminBundle/current/index.html
- **Examples:** https://github.com/EasyCorp/EasyAdminBundle/tree/4.x/doc
- **Demo:** https://demo.easyadmin.org/admin

---

## 🆘 Troubleshooting

### Can't access /admin (404)
```bash
# Clear cache
docker compose exec php php bin/console cache:clear

# Check routes
docker compose exec php php bin/console debug:router | grep admin
```

### "Access Denied"
- Make sure your user has `ROLE_ADMIN`
- Check `config/packages/security.yaml` has admin route configured

### CRUD controller not showing
- Make sure it's in `src/Controller/Admin/` directory
- Must extend `AbstractCrudController`
- Check namespace: `App\Controller\Admin`

---

## ⚙️ Configuration Files

Everything you need:
- ✅ `composer.json` - EasyAdmin already added
- ✅ `config/packages/security.yaml` - Admin route protected
- ✅ `src/Command/CreateAdminUserCommand.php` - Helper command
- ✅ `Makefile` - `make create-admin` command added

---

## 🎉 You're Done!

Your admin panel is ready. Now you can:
1. Manage all your data through a beautiful UI
2. Create/edit/delete animals, logs, and blog posts
3. Search and filter records
4. Export data
5. Give admin access to team members

**Access it at:** http://localhost:8080/admin 🚀

