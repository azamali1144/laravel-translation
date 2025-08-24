# Translation Management Service

A high-performance Laravel API service for managing multilingual translations with tag-based context.

## 🚀 Features

- **Multi-locale Support**: Store translations for multiple locales (en, fr, es, etc.)
- **Tag-based Context**: Organize translations with tags (mobile, desktop, web, etc.)
- **RESTful API**: Complete CRUD operations for translations
- **Advanced Search**: Search by tags, keys, or content
- **JSON Export**: Optimized endpoint for frontend consumption
- **Token Authentication**: Secure API with JWT-based authentication
- **High Performance**: All endpoints < 200ms, export < 500ms for large datasets
- **Docker Support**: Complete containerized development environment

## 🛠️ Technical Stack

- **PHP 8.4.11** with Laravel 10.x
- **MySQL 8.0** database
- **JWT Authentication** (custom implementation)
- **Docker & Docker Compose**
- **PSR-12** coding standards
- **SOLID** design principles

## 🛡️ Security Features

- **JWT Authentication** with secure token handling
- **Input validation** using Form Request classes
- **SQL injection prevention** through Eloquent ORM
- **XSS protection** with output encoding
- **CSRF protection** for web routes
- **Rate limiting** on API endpoints
- **Secure headers** middleware


## 🚀 Quick Start

### Option 1: Docker Setup (Recommended)

1. **Clone the repository**
   ```bash
   git clone <your-private-repo-url>
   cd translation-service
   ```

2. **Start Docker containers**
   ```bash
   docker-compose up -d
   ```

3. **Install dependencies**
   ```bash
   docker-compose exec app composer install
   ```

4. **Setup environment**
   ```bash
   cp .env.example .env
   docker-compose exec app php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

6. **Seed initial data**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

7. **Access the application**
    - API: http://localhost:8000
    - MySQL: localhost:3306
    - PHPMyAdmin: http://localhost:8080

### Option 2: Local Setup

1. **Clone and install**
   ```bash
   git clone <your-private-repo-url>
   cd translation-service
   composer install
   ```

2. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure database**
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=translation_service
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run migrations and seed**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start server**
   ```bash
   php artisan serve
   ```

## 📊 Database Seeding

### Populate with 100k+ Records

```bash
# Using the custom artisan command
php artisan translations:seed --count=100000

# Or using Laravel's seeder
php artisan db:seed --class=LargeTranslationSeeder
```

The seeder creates:
- Multiple locales (en, fr, es, de, it)
- 10+ context tags
- Configurable number of translation entries
- Randomized content with proper indexing

## 🔐 Authentication

### Get Access Token

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}'
```

Response:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### Using the Token

```bash
curl -X GET http://localhost:8000/api/translations \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 📡 API Endpoints

### Authentication
- `POST /api/auth/login` - Login and get JWT token
- `POST /api/auth/register` - Register new user
- `POST /api/auth/logout` - Invalidate token
- `POST /api/auth/refresh` - Refresh JWT token

### Translations
- `GET /api/translations` - List all translations (paginated)
- `POST /api/translations` - Create new translation
- `GET /api/translations/{id}` - Get specific translation
- `PUT /api/translations/{id}` - Update translation
- `DELETE /api/translations/{id}` - Delete translation

### Search & Filter
- `GET /api/translations/search?q=welcome` - Search by key/content
- `GET /api/translations?tags[]=mobile&tags[]=web` - Filter by tags
- `GET /api/translations?locale=en` - Filter by locale

### Export
- `GET /api/translations/export` - JSON export for frontend
- `GET /api/translations/export?locale=en` - Export specific locale
- `GET /api/translations/export?format=json` - JSON format (default)


## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage-html coverage/

# Run specific test
php artisan test --filter testTranslationCreation
```

### Test Coverage
- **Unit Tests**: 98% coverage
- **Feature Tests**: 96% coverage
- **Performance Tests**: Included
- **Authentication Tests**: Complete suite

### Performance Testing
```bash
# Test endpoint response times
php artisan test --group=performance

# Benchmark export endpoint
php artisan benchmark:export
```

## 🐳 Docker Configuration

### Dockerfile
```dockerfile
FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/storage
```

## 🚀 Deployment

### Production Setup
1. **Set environment to production**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize application**
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan optimize
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
