# Floodguard Network - Flood Relief Management System

<p align="center">
    <img src="public/images/logo.png" alt="Floodguard Network Logo" width="200">
</p>

A comprehensive flood relief management system built with Laravel 11, designed to coordinate disaster response efforts between administrators, volunteers, and donors.

## 🌟 Features

- **Multi-Role Authentication System**: Admin, Volunteer, and Donor roles with role-specific dashboards
- **Donor Management**: Track donations, donor profiles, and donation history
- **Volunteer Coordination**: Manage volunteer profiles, locations, and assignments
- **Victim Registry**: Comprehensive database of flood victims and their needs
- **Inventory Management**: Track relief supplies and distribution
- **Responsive Design**: Modern glassmorphism UI with mobile-friendly interface
- **Real-time Dashboard**: Role-specific dashboards showing relevant data and statistics

## 🚀 Quick Start

### Prerequisites

Before running this application, make sure you have the following installed:

- **PHP >= 8.2**
- **Composer** (PHP dependency manager)
- **Node.js & NPM** (for frontend assets)
- **MySQL** or **MariaDB**
- **Git**

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Peru00/Floodguard-Network.git
   cd Floodguard-Network
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node Dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   # Copy the environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   ```

5. **Database Setup**
   
   Create a MySQL database named `test_1` and update your `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=test_1
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run Database Migrations**
   ```bash
   # Run migrations to create all tables
   php artisan migrate
   
   # Create sessions table (required for authentication)
   php artisan session:table
   php artisan migrate
   ```

7. **Build Frontend Assets**
   ```bash
   npm run build
   ```

8. **Start the Development Server**
   ```bash
   php artisan serve
   ```

   The application will be available at `http://localhost:8000`

## 🗄️ Database Structure

The application includes the following main tables:

- **users**: Core user authentication and profile data
- **donor_profiles**: Extended profile information for donors
- **volunteer_profiles**: Extended profile information for volunteers
- **victims**: Registry of flood victims requiring assistance
- **donations**: Track all donations and their status
- **inventory**: Manage relief supplies and stock levels
- **sessions**: Handle user session management

## 👤 Default Test Users

For testing purposes, you can create test users using the built-in command:

```bash
php artisan create:test-user
```

Or create them manually using Tinker:

```bash
php artisan tinker

# Create test users
User::create([
    'user_id' => 'test001',
    'first_name' => 'Test',
    'last_name' => 'Admin',
    'email' => 'admin@test.com',
    'phone' => '1234567890',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);

User::create([
    'user_id' => 'test002',
    'first_name' => 'Test',
    'last_name' => 'Volunteer',
    'email' => 'volunteer@test.com',
    'phone' => '1234567891',
    'password' => Hash::make('password'),
    'role' => 'volunteer'
]);

User::create([
    'user_id' => 'test003',
    'first_name' => 'Test',
    'last_name' => 'Donor',
    'email' => 'donor@test.com',
    'phone' => '1234567892',
    'password' => Hash::make('password'),
    'role' => 'donor'
]);
```

## 🔐 Authentication

The application uses Laravel's built-in authentication with the following login credentials:

- **Admin**: `admin@test.com` / `password`
- **Volunteer**: `volunteer@test.com` / `password`
- **Donor**: `donor@test.com` / `password`

**Note**: You must select the appropriate role during login as the system validates both email and role.

## 📱 Application Routes

### Public Routes
- `/` - Homepage with system overview
- `/login` - Login page with role selection
- `/signup` - User registration page

### Protected Routes (Require Authentication)
- `/dashboard` - Role-specific dashboard showing login success and user information

## 🎨 Frontend Assets

The application uses:
- **CSS**: Custom glassmorphism design with CSS variables
- **JavaScript**: Vanilla JS for form interactions and UI components
- **Font Awesome**: Icons and visual elements
- **Responsive Design**: Mobile-first approach

## 🔧 Development

### Running in Development Mode

```bash
# Start the Laravel development server
php artisan serve

# In another terminal, watch for asset changes (if needed)
npm run dev
```

### Common Artisan Commands

```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Run all clear commands at once
php artisan optimize:clear
```

### Database Commands

```bash
# Refresh database with fresh migrations
php artisan migrate:refresh

# Seed the database (if seeders are available)
php artisan db:seed

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo()
```

## 🔍 Troubleshooting

### Common Issues

1. **"The provided credentials do not match our records"**
   - Ensure you're selecting the correct role during login
   - Verify the user exists in the database with the correct role
   - Check that passwords are properly hashed using `Hash::make()`

2. **Database Connection Errors**
   - Verify your `.env` database configuration
   - Ensure MySQL service is running
   - Check that the database `test_1` exists

3. **Session Issues**
   - Run `php artisan session:table` and `php artisan migrate`
   - Clear sessions: `php artisan cache:clear`

4. **Asset Loading Issues**
   - Run `npm run build` to compile assets
   - Check that `public/css/style.css` exists
   - Verify asset paths in templates

## 🏗️ Project Structure

```
floodguard-network/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php      # Authentication logic
│   │   └── HomeController.php      # Homepage controller
│   └── Models/
│       ├── User.php                # User model with custom auth
│       ├── DonorProfile.php        # Donor profile extension
│       └── VolunteerProfile.php    # Volunteer profile extension
├── database/
│   └── migrations/                 # Database schema files
├── public/
│   ├── css/                        # Compiled stylesheets
│   └── images/                     # Static images and assets
├── resources/
│   └── views/
│       ├── auth/                   # Login and signup templates
│       └── welcome.blade.php       # Homepage template
└── routes/
    └── web.php                     # Application routes
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📧 Support

If you encounter any issues or have questions, please:

1. Check the troubleshooting section above
2. Review the Laravel documentation: https://laravel.com/docs
3. Create an issue in the GitHub repository

---

**Built with ❤️ for flood relief coordination and community support.**
