# 🌊 Floodguard Network - Comprehensive Flood Relief Management System

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-11-red?style=for-the-badge&logo=laravel" alt="Laravel 11">
    <img src="https://img.shields.io/badge/PHP-8.2+-blue?style=for-the-badge&logo=php" alt="PHP 8.2+">
    <img src="https://img.shields.io/badge/MySQL-8.0+-orange?style=for-the-badge&logo=mysql" alt="MySQL">
    <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="MIT License">
</p>

## 📖 About Floodguard Network

**Floodguard Network** is a comprehensive flood relief management system designed to streamline disaster response coordination. Built with Laravel 11, it provides a centralized platform for managing relief operations, coordinating volunteers, tracking donations, and assisting flood victims efficiently.

### 🎯 Project Purpose

In times of natural disasters, particularly floods, coordinated response efforts can save lives and reduce suffering. This system addresses the critical need for:

- **Centralized victim registration** and needs assessment
- **Efficient volunteer coordination** and task management
- **Transparent donation tracking** and fund management
- **Inventory management** for relief supplies
- **Real-time communication** between all stakeholders
- **Administrative oversight** and reporting capabilities

## ✨ Key Features

### 🔐 Multi-Role Authentication System
- **Admin Panel**: Complete system oversight and user management
- **Donor Dashboard**: Donation tracking and contribution history
- **Volunteer Portal**: Task assignment and availability management
- **Victim Registry**: Support request and status tracking

### 🏗️ Core Functionality

#### For Administrators
- **Complete User Management**: Create, edit, delete users across all roles
- **Dashboard Analytics**: Real-time statistics and system overview
- **Donation Oversight**: Approve/reject donations and track funds
- **Victim Management**: Register victims and assign priority levels
- **Volunteer Coordination**: Assign tasks and monitor availability
- **Inventory Control**: Manage relief supplies and distribution
- **System Security**: Role-based access control and audit trails

#### For Donors
- **Donation Portal**: Submit monetary and supply donations
- **Contribution Tracking**: View donation history and status
- **Impact Reports**: See how donations are being utilized
- **Profile Management**: Update personal information and preferences
- **Distribution Tracking**: Monitor relief supply distribution

#### For Volunteers
- **Task Dashboard**: View assigned tasks and responsibilities
- **Availability Management**: Set availability status and schedule
- **Location Assignment**: Manage deployment locations
- **Progress Tracking**: Update task completion status
- **Profile Management**: Skills, experience, and contact information

#### For Victims
- **Support Requests**: Register for assistance and relief
- **Status Updates**: Track application progress
- **Contact Information**: Emergency contacts and location details
- **Need Assessment**: Specify required assistance types

### 🎨 User Interface Features
- **Modern Design**: Clean, professional glassmorphism UI
- **Responsive Layout**: Mobile-first design for all devices
- **Intuitive Navigation**: Role-specific navigation and dashboards
- **Real-time Feedback**: Success/error messages and notifications
- **Accessibility**: Screen reader friendly and keyboard navigation

## 🚀 Getting Started

### 📋 Prerequisites

Ensure you have the following software installed:

- **PHP >= 8.2** with required extensions (mbstring, openssl, pdo, tokenizer, xml, fileinfo)
- **Composer** (latest version) - PHP dependency manager
- **Node.js >= 16** and **NPM** - For frontend asset compilation
- **MySQL >= 8.0** or **MariaDB >= 10.4** - Database server
- **Git** - Version control
- **Web Server** - Apache/Nginx (optional, Laravel has built-in server)

### ⚡ Quick Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Peru00/Floodguard-Network.git
   cd Floodguard-Network
   ```

2. **Install Dependencies**
   ```bash
   # Install PHP dependencies
   composer install

   # Install Node.js dependencies
   npm install
   ```

3. **Environment Setup**
   ```bash
   # Copy environment configuration
   cp .env.example .env

   # Generate application key
   php artisan key:generate
   ```

4. **Database Configuration**
   
   Edit your `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=floodguard_network
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

   Create the database:
   ```sql
   CREATE DATABASE floodguard_network CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. **Database Migration**
   ```bash
   # Run all migrations to create database structure
   php artisan migrate

   # Optional: Seed with sample data
   php artisan db:seed
   ```

6. **Build Frontend Assets**
   ```bash
   # Compile assets for production
   npm run build

   # For development (with file watching)
   npm run dev
   ```

7. **Start the Application**
   ```bash
   php artisan serve
   ```

   🎉 **Access the application at: http://localhost:8000**

## 🗄️ Database Architecture

The system uses a well-structured database with the following key tables:

### Core Tables
- **`users`** - Central user authentication and profile data
- **`sessions`** - Secure session management
- **`donor_profiles`** - Extended donor information and preferences
- **`volunteer_profiles`** - Volunteer skills, availability, and assignments
- **`victims`** - Comprehensive victim registry and needs assessment

### Operational Tables
- **`donations`** - All donation records with status tracking
- **`inventory`** - Relief supplies inventory and distribution
- **`distribution_tasks`** - Task assignment and completion tracking
- **`chatbox`** - Communication logs and coordination messages

### Migration Features
- Foreign key constraints for data integrity
- Indexed fields for optimal performance
- Soft deletes for audit trail maintenance
- Timestamp tracking for all operations

## 👥 User Roles & Access

### 🔑 Default Test Accounts

Create test accounts using Laravel Tinker:

```bash
php artisan tinker
```

```php
// Create Admin User
User::create([
    'user_id' => 'ADM001',
    'first_name' => 'System',
    'last_name' => 'Administrator',
    'email' => 'admin@floodguard.com',
    'phone' => '+1234567890',
    'password' => Hash::make('admin123'),
    'role' => 'admin'
]);

// Create Donor User
User::create([
    'user_id' => 'DON001',
    'first_name' => 'John',
    'last_name' => 'Donor',
    'email' => 'donor@example.com',
    'phone' => '+1234567891',
    'password' => Hash::make('donor123'),
    'role' => 'donor'
]);

// Create Volunteer User
User::create([
    'user_id' => 'VOL001',
    'first_name' => 'Jane',
    'last_name' => 'Volunteer',
    'email' => 'volunteer@example.com',
    'phone' => '+1234567892',
    'password' => Hash::make('volunteer123'),
    'role' => 'volunteer'
]);
```

### 🛡️ Security Features

- **Role-Based Access Control (RBAC)**: Strict permission enforcement
- **CSRF Protection**: All forms protected against cross-site request forgery
- **Password Hashing**: Bcrypt encryption for all passwords
- **Session Security**: Secure session handling and timeout
- **Input Validation**: Server-side validation for all user inputs
- **XSS Protection**: Output escaping and content security policies

## 📁 Project Structure

```
floodguard-network/
├── 📂 app/
│   ├── 📂 Http/Controllers/
│   │   ├── AdminController.php          # Admin panel management
│   │   ├── AuthController.php           # Authentication logic
│   │   ├── DonorController.php          # Donor dashboard & donations
│   │   ├── VolunteerController.php      # Volunteer management
│   │   └── HomeController.php           # Public pages
│   └── 📂 Models/
│       ├── User.php                     # Core user model
│       ├── Donation.php                 # Donation tracking
│       ├── DonorProfile.php             # Extended donor data
│       └── VolunteerProfile.php         # Extended volunteer data
├── 📂 database/
│   ├── 📂 migrations/                   # Database schema files
│   └── 📂 seeders/                      # Sample data generators
├── 📂 public/
│   ├── 📂 css/                         # Compiled stylesheets
│   ├── 📂 js/                          # Frontend JavaScript
│   └── 📂 assets/                      # Images and static files
├── 📂 resources/
│   └── 📂 views/
│       ├── 📂 admin/                   # Admin panel templates
│       │   ├── dashboard.blade.php     # Admin dashboard
│       │   ├── user-management.blade.php # User CRUD operations
│       │   └── edit-user.blade.php     # User editing form
│       ├── 📂 auth/                    # Authentication templates
│       ├── 📂 donor/                   # Donor portal templates
│       ├── 📂 volunteer/               # Volunteer portal templates
│       └── welcome.blade.php           # Public homepage
└── 📂 routes/
    ├── web.php                         # Web application routes
    └── api.php                         # API routes (future use)
```

## 🔧 Development Guide

### 🏃‍♂️ Running in Development Mode

```bash
# Start Laravel development server
php artisan serve

# Watch for frontend changes (separate terminal)
npm run dev

# Run background queue worker (if needed)
php artisan queue:work
```

### 🛠️ Useful Artisan Commands

```bash
# Clear all caches
php artisan optimize:clear

# Generate application key
php artisan key:generate

# Create new migration
php artisan make:migration create_example_table

# Create new controller
php artisan make:controller ExampleController

# Create new model with migration
php artisan make:model Example -m

# Run specific migration
php artisan migrate --path=database/migrations/specific_migration.php

# Check routes
php artisan route:list

# Database operations
php artisan migrate:refresh --seed
```

### 🧪 Testing Commands

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Create new test
php artisan make:test ExampleTest
```

## 🔍 API Documentation

### Authentication Endpoints

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET    | `/login` | Login form | Public |
| POST   | `/login` | Authenticate user | Public |
| GET    | `/signup` | Registration form | Public |
| POST   | `/signup` | Create new user | Public |
| POST   | `/logout` | End user session | Authenticated |

### Admin Endpoints

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET    | `/admin/dashboard` | Admin overview | Admin |
| GET    | `/admin/user-management` | User CRUD interface | Admin |
| POST   | `/admin/create-user` | Create new user | Admin |
| GET    | `/admin/users/{id}/edit` | Edit user form | Admin |
| PUT    | `/admin/users/{id}` | Update user data | Admin |
| DELETE | `/admin/users/{id}` | Delete user | Admin |

### Donor Endpoints

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET    | `/donor/dashboard` | Donor overview | Donor |
| POST   | `/donor/submit-donation` | Submit donation | Donor |
| GET    | `/donor/donations` | Donation history | Donor |

### Volunteer Endpoints

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET    | `/volunteer/dashboard` | Volunteer overview | Volunteer |
| POST   | `/volunteer/toggle-availability` | Update availability | Volunteer |
| POST   | `/volunteer/complete-task` | Mark task complete | Volunteer |

## ⚠️ Troubleshooting

### Common Issues & Solutions

#### 1. **Database Connection Failed**
```bash
# Check database service
mysql --version
sudo service mysql start  # Linux
net start mysql          # Windows

# Test connection
php artisan tinker
>>> DB::connection()->getPdo()
```

#### 2. **Migration Errors**
```bash
# Reset migrations
php artisan migrate:reset
php artisan migrate

# Check migration status
php artisan migrate:status
```

#### 3. **Permission Errors (Linux/Mac)**
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 4. **Asset Loading Issues**
```bash
# Clear browser cache
# Rebuild assets
npm run build

# Check asset files exist
ls -la public/css/
ls -la public/js/
```

#### 5. **Login Issues**
- Verify user exists in database with correct role
- Check password hashing: `Hash::make('password')`
- Clear sessions: `php artisan cache:clear`

### 📊 System Requirements

#### Minimum Requirements
- **CPU**: 1 GHz processor
- **RAM**: 1 GB minimum, 2 GB recommended
- **Storage**: 500 MB free space
- **PHP**: Version 8.2 or higher
- **MySQL**: Version 8.0 or higher

#### Production Requirements
- **CPU**: Multi-core processor (2+ cores)
- **RAM**: 4 GB or more
- **Storage**: 2 GB free space + log storage
- **HTTPS**: SSL certificate for secure connections
- **Backup**: Regular database and file backups

## 🚀 Deployment

### Production Deployment Checklist

1. **Environment Configuration**
   ```bash
   # Set production environment
   APP_ENV=production
   APP_DEBUG=false
   
   # Configure secure app key
   php artisan key:generate
   ```

2. **Database Setup**
   ```bash
   # Run migrations
   php artisan migrate --force
   
   # Optimize application
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Web Server Configuration**
   - Point document root to `/public` directory
   - Configure URL rewriting
   - Enable HTTPS with SSL certificate
   - Set appropriate file permissions

4. **Security Hardening**
   - Update all dependencies: `composer update --no-dev`
   - Configure firewall rules
   - Set up regular backups
   - Monitor logs for suspicious activity

## 🤝 Contributing

We welcome contributions from the community! Here's how to get started:

### Development Workflow

1. **Fork the Repository**
   ```bash
   git clone https://github.com/YourUsername/Floodguard-Network.git
   cd Floodguard-Network
   ```

2. **Create Feature Branch**
   ```bash
   git checkout -b feature/amazing-new-feature
   ```

3. **Make Changes**
   - Follow PSR-12 coding standards
   - Write descriptive commit messages
   - Add tests for new functionality
   - Update documentation as needed

4. **Test Changes**
   ```bash
   php artisan test
   npm run test
   ```

5. **Submit Pull Request**
   - Provide clear description of changes
   - Reference any related issues
   - Ensure all tests pass

### Code Style Guidelines

- **PHP**: Follow PSR-12 standards
- **JavaScript**: Use ES6+ features consistently
- **CSS**: Follow BEM methodology for class naming
- **Blade**: Use consistent indentation and structure

## 📞 Support & Community

### Getting Help

1. **Documentation**: Check this README and inline code comments
2. **Issues**: Create GitHub issues for bugs and feature requests
3. **Discussions**: Use GitHub Discussions for questions
4. **Laravel Docs**: https://laravel.com/docs for framework help

### Community Guidelines

- Be respectful and inclusive
- Provide detailed information when reporting issues
- Search existing issues before creating new ones
- Follow the code of conduct

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

### MIT License Summary
- ✅ Commercial use allowed
- ✅ Modification allowed
- ✅ Distribution allowed
- ✅ Private use allowed
- ❗ No warranty provided
- ❗ License and copyright notice required

## 🎯 Roadmap

### Upcoming Features

- [ ] **Mobile Application**: React Native app for field volunteers
- [ ] **Real-time Chat**: WebSocket-based communication system
- [ ] **GIS Integration**: Interactive maps for location tracking
- [ ] **Reporting Dashboard**: Advanced analytics and reporting
- [ ] **Multi-language Support**: Internationalization (i18n)
- [ ] **API Documentation**: Comprehensive REST API docs
- [ ] **Third-party Integrations**: Weather services, payment gateways
- [ ] **Automated Testing**: Comprehensive test suite

### Version History

- **v1.0.0** - Initial release with core functionality
- **v1.1.0** - User management and admin panel
- **v1.2.0** - Enhanced security and validation
- **v2.0.0** - (Planned) Mobile app and real-time features

## 🙏 Acknowledgments

Special thanks to:

- **Laravel Team** for the excellent framework
- **FontAwesome** for the comprehensive icon library
- **PHP Community** for continuous support and resources
- **Open Source Contributors** who help improve this project

---

<p align="center">
    <strong>Built with ❤️ By Mehedi for flood relief coordination and community resilience</strong><br>
    <em>Helping communities prepare, respond, and recover from flood disasters</em>
    <em>BTW Mehedi Bonnay Haray Nai !!!</em>
</p>

<p align="center">
    <a href="https://github.com/Peru00/Floodguard-Network/issues">Report Bug</a> •
    <a href="https://github.com/Peru00/Floodguard-Network/issues">Request Feature</a> •
    <a href="https://github.com/Peru00/Floodguard-Network/discussions">Ask Question</a>
</p>
