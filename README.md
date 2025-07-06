# 🏥 DocRaTech Medical Website Platform

A modern, enterprise-grade medical website platform built with Laravel 12 and Vue 3, designed specifically for healthcare professionals in the United States market.

## ✨ Features

- **Modern Tech Stack**: Laravel 12 + Vue 3 + Tailwind CSS
- **Authentication System**: Laravel Sanctum with role-based permissions
- **User Management**: Advanced user roles and permissions system
- **Medical-Focused**: Built specifically for healthcare providers
- **Enterprise Ready**: Scalable architecture with service layer patterns
- **Responsive Design**: Modern UI with mobile-first approach

## 🚀 Tech Stack

### Backend
- **Laravel 12** - Modern PHP framework
- **MySQL 8+** - Primary database
- **Laravel Sanctum** - API authentication
- **Spatie Permissions** - Role and permission management

### Frontend
- **Vue 3** - Progressive JavaScript framework
- **Tailwind CSS** - Utility-first CSS framework
- **Pinia** - Vue state management
- **Vue Router** - Client-side routing
- **Axios** - HTTP client

### Development Tools
- **Vite** - Fast build tool
- **Laravel Pint** - Code style fixer
- **Pest** - Testing framework

## 📋 Requirements

- PHP 8.2 or higher
- Node.js 18 or higher
- MySQL 8.0 or higher
- Composer
- NPM or Yarn

## 🛠️ Installation

### 1. Clone the repository
```bash
git clone <repository-url>
cd docratech-medical-website-platform
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node.js dependencies
```bash
npm install
```

### 4. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure database
Edit `.env` file with your MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=docratech
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run migrations and seeders
```bash
php artisan migrate:fresh --seed
```

### 7. Start development servers

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

Your application will be available at: `http://localhost:8000`

## 🔐 Default Users

After running the seeders, the following users will be created:

| Email | Password | Role | Description |
|-------|----------|------|-------------|
| superadmin@docratech.com | password | Super Admin | Full system access |
| admin@docratech.com | password | Admin | Administrative access |
| manager@docratech.com | password | Manager | Management access |
| user@docratech.com | password | User | Basic user access |

## 📁 Project Structure

```
docratech-medical-website-platform/
├── app/
│   ├── Http/Controllers/Api/     # API Controllers
│   ├── Models/                   # Eloquent Models
│   ├── Services/                 # Business Logic Services
│   ├── Mail/                     # Email Templates
│   └── Rules/                    # Custom Validation Rules
├── database/
│   ├── migrations/               # Database Migrations
│   └── seeders/                  # Database Seeders
├── resources/
│   ├── js/
│   │   ├── components/           # Vue Components
│   │   ├── views/                # Vue Pages
│   │   ├── stores/               # Pinia Stores
│   │   └── services/             # API Services
│   └── css/                      # Styles
├── routes/
│   ├── api.php                   # API Routes
│   └── web.php                   # Web Routes
└── public/                       # Public Assets
```

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

## 🔧 Development Commands

### Clear caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Update autoloader
```bash
composer dump-autoload
```

### Code formatting
```bash
./vendor/bin/pint
```

### Build for production
```bash
npm run build
```

## 📖 API Documentation

The application provides a RESTful API with the following main endpoints:

- `POST /api/auth/login` - User authentication
- `GET /api/auth/me` - Get current user
- `GET /api/users` - List users (with permissions)
- `GET /api/roles` - List roles
- `GET /api/permissions` - List permissions

All API endpoints require authentication via Laravel Sanctum tokens.

## 🏗️ Architecture

### Service Layer Pattern
The application uses a service layer pattern to separate business logic from controllers:

- **Controllers**: Handle HTTP requests and responses
- **Services**: Contain business logic and data manipulation
- **Models**: Handle database interactions
- **Repositories**: Abstract database operations (where needed)

### Permission System
The application uses a sophisticated role-based permission system:

- **Roles**: Groups of permissions (Admin, Manager, User, etc.)
- **Permissions**: Granular access controls (users.create, roles.edit, etc.)
- **Guards**: Middleware to protect routes based on permissions

## 🔒 Security Features

- **Laravel Sanctum** - API token authentication
- **CSRF Protection** - Cross-site request forgery protection
- **Rate Limiting** - API rate limiting
- **Input Validation** - Comprehensive input validation
- **SQL Injection Protection** - Eloquent ORM protection
- **XSS Protection** - Output escaping and sanitization

## 🚀 Deployment

### Production Build
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Variables
Ensure proper production environment variables are set in `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions, please contact the development team or create an issue in the repository.

---

**Built with ❤️ for the healthcare community**
