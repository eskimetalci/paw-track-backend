# API Versioning Guide

## Overview

PawTrack API uses **URI Path Versioning** starting with **v1**. All API endpoints are prefixed with the version number to ensure backward compatibility and smooth transitions between versions.

## Current Version

**Version 1 (v1)** - Initial release

Base URL: `http://localhost:8080/api/v1`

## URL Structure

```
https://api.pawtrack.com/api/{version}/{resource}
```

Examples:
- `https://api.pawtrack.com/api/v1/animals`
- `https://api.pawtrack.com/api/v1/poo_logs`
- `https://api.pawtrack.com/api/v1/login_check`

## V1 Endpoints

### Authentication
- `POST /api/v1/register` - User registration
- `POST /api/v1/login_check` - Login (get JWT token)

### Resources
- `GET|POST /api/v1/animals` - Animals collection
- `GET|PUT|DELETE /api/v1/animals/{id}` - Single animal
- `GET|POST /api/v1/poo_logs` - Poo logs collection
- `GET|PUT|DELETE /api/v1/poo_logs/{id}` - Single poo log
- `GET|POST /api/v1/medicine_logs` - Medicine logs collection
- `GET|PUT|DELETE /api/v1/medicine_logs/{id}` - Single medicine log
- `GET|POST /api/v1/vaccine_logs` - Vaccine logs collection
- `GET|PUT|DELETE /api/v1/vaccine_logs/{id}` - Single vaccine log
- `GET|POST /api/v1/blog_posts` - Blog posts collection
- `GET|PUT|DELETE /api/v1/blog_posts/{id}` - Single blog post

### Documentation
- `GET /api/docs` - Interactive API documentation (Swagger UI)
- `GET /api/docs.json` - OpenAPI specification

## How to Add a New Version (v2)

When you need to introduce breaking changes, create a new version:

### Step 1: Create Version-Specific Route Configuration

Create `config/routes/api_platform_v2.yaml`:

```yaml
api_platform_v2:
    resource: .
    type: api_platform
    prefix: /api/v2
```

### Step 2: Organize Entities by Version (Optional)

You have two approaches:

**Approach A: Shared Entities with Version-Specific Operations**

Keep entities in `src/Entity/` but add version-specific operations:

```php
#[ApiResource(
    operations: [
        // V1 operations
        new Get(uriTemplate: '/v1/animals/{id}'),
        new GetCollection(uriTemplate: '/v1/animals'),
        new Post(uriTemplate: '/v1/animals'),
        
        // V2 operations with different serialization groups
        new Get(
            uriTemplate: '/v2/animals/{id}',
            normalizationContext: ['groups' => ['animal:v2:read']]
        ),
        new GetCollection(
            uriTemplate: '/v2/animals',
            normalizationContext: ['groups' => ['animal:v2:read']]
        ),
    ]
)]
class Animal
{
    // Entity properties
}
```

**Approach B: Separate Entities per Version**

Create version-specific entities:

```
src/
  Entity/
    V1/
      Animal.php
      PooLog.php
    V2/
      Animal.php
      PooLog.php
```

### Step 3: Update Security Configuration

Add v2 patterns to `config/packages/security.yaml`:

```yaml
firewalls:
    login_v2:
        pattern: ^/api/v2/login
        stateless: true
        json_login:
            check_path: /api/v2/login_check
            success_handler: lexik_jwt_authentication.handler.authentication_success
            failure_handler: lexik_jwt_authentication.handler.authentication_failure
    
    api_v2:
        pattern: ^/api/v2
        stateless: true
        jwt: ~

access_control:
    - { path: ^/api/v2/login, roles: PUBLIC_ACCESS }
    - { path: ^/api/v2/register, roles: PUBLIC_ACCESS }
    - { path: ^/api/v2, roles: IS_AUTHENTICATED_FULLY }
```

### Step 4: Update CORS Configuration

Add v2 path to `config/packages/nelmio_cors.yaml`:

```yaml
paths:
    '^/api/v2/':
        allow_origin: ['*']
        allow_headers: ['*']
        allow_methods: ['POST', 'PUT', 'GET', 'DELETE', 'PATCH', 'OPTIONS']
        max_age: 3600
```

### Step 5: Update Documentation

Update `config/packages/api_platform.yaml` to support multiple versions:

```yaml
api_platform:
    title: 'PawTrack API'
    description: 'REST API for pet health tracking'
    version: '2.0.0'  # Update version number
```

## Version Lifecycle

### Version Support Policy

- **Current version (v1)**: Fully supported, receives all new features
- **Previous version (when v2 is released)**: Supported for 12 months, security fixes only
- **Deprecated versions**: 6-month notice before removal

### Breaking Changes

A new version is required when:
- Removing fields from responses
- Changing field types or formats
- Changing authentication mechanisms
- Removing endpoints
- Changing error response structure

### Non-Breaking Changes (Can stay in same version)

- Adding new optional fields
- Adding new endpoints
- Adding new query parameters
- Bug fixes
- Performance improvements

## Migration Guide (When v2 is Released)

### For API Consumers

1. **Update Base URL**
   ```javascript
   // Old
   const BASE_URL = 'https://api.pawtrack.com/api/v1';
   
   // New
   const BASE_URL = 'https://api.pawtrack.com/api/v2';
   ```

2. **Review Changelog**
   - Check for removed fields
   - Update request/response handling
   - Test all integrations

3. **Gradual Migration**
   - Keep v1 for production
   - Test v2 in staging
   - Migrate when ready

### For Developers

1. **Communicate Changes**
   - Send migration guide to users
   - Provide 6-month deprecation notice
   - Update documentation

2. **Support Both Versions**
   - Keep v1 endpoints active
   - Monitor v1 usage metrics
   - Provide migration assistance

3. **Sunset Old Version**
   - After 12 months (or based on usage)
   - Final notification 1 month before
   - Archive documentation

## Best Practices

### Do's ✅

- Always use the version prefix in API calls
- Document all changes between versions
- Provide clear migration guides
- Keep versions simple (v1, v2, v3)
- Test thoroughly before releasing new versions

### Don'ts ❌

- Don't skip version numbers (v1 → v3)
- Don't make breaking changes within a version
- Don't remove old versions without notice
- Don't version individual endpoints differently
- Don't use query parameters or headers for versioning

## Testing Multiple Versions

### Test Script for V1

```bash
./test-api.sh
```

### Test Script for V2 (when implemented)

Update `API_VERSION` variable:

```bash
# In test-api.sh
API_VERSION="v2"
```

### Manual Testing

```bash
# V1
curl -X GET http://localhost:8080/api/v1/animals \
  -H "Authorization: Bearer $TOKEN"

# V2 (when available)
curl -X GET http://localhost:8080/api/v2/animals \
  -H "Authorization: Bearer $TOKEN"
```

## Monitoring Version Usage

Track which versions are being used:

```yaml
# config/packages/monolog.yaml (future enhancement)
monolog:
    handlers:
        api_version:
            type: stream
            path: '%kernel.logs_dir%/api_versions.log'
            level: info
            formatter: monolog.formatter.json
```

## FAQ

### Q: Do I need to maintain separate databases for each version?
**A:** No, use the same database. Versions only affect the API layer (serialization, validation, business logic).

### Q: How do I handle IRI references between versions?
**A:** IRI format stays consistent across versions. `/api/v1/animals/123` and `/api/v2/animals/123` refer to the same resource.

### Q: Should I version the authentication endpoints?
**A:** Yes, for consistency and future flexibility.

### Q: What about the Swagger docs?
**A:** API Platform can generate separate documentation per version. Access at `/api/docs` (shows all versions).

### Q: Can clients use both versions simultaneously?
**A:** Yes! Clients can call `/api/v1/animals` and `/api/v2/users` in the same application during migration.

## Example: Real-World Breaking Change

### Scenario: Changing Bristol Scale from 1-7 to 1-10

**V1 Response:**
```json
{
  "id": "123",
  "bristolScore": 4,
  "color": "BROWN"
}
```

**V2 Response:**
```json
{
  "id": "123",
  "bristolScore": 6,  // Converted from 1-7 to 1-10 scale
  "stoolColor": "BROWN",  // Renamed field
  "healthZone": "HEALTHY"  // New computed field
}
```

**Migration:**
- V1 continues to use 1-7 scale
- V2 uses 1-10 scale
- Backend converts on-the-fly based on version
- Users have 12 months to migrate

## Resources

- [API Platform Versioning](https://api-platform.com/docs/core/versioning/)
- [REST API Versioning Best Practices](https://restfulapi.net/versioning/)
- [Semantic Versioning](https://semver.org/)

## Status

- ✅ **v1** - Current (Active Development)
- 🔜 **v2** - Planned (Future)

