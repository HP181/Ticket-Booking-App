<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Event Booking System

A comprehensive Laravel-based application for managing events, ticket sales, and bookings. This system allows event organizers to create and manage events, attendees to browse and purchase tickets, and administrators to oversee the entire platform.

## Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [User Roles](#user-roles)
- [Testing](#testing)
- [Deployment](#deployment)
- [Project Structure](#project-structure)
- [Screenshots](#screenshots)

## Features

### Core Features
- **User Authentication & Authorization**
  - Role-based access control (Admin, Organizer, Attendee)
  - Secure login and registration
  - Profile management

- **Event Management**
  - Create, edit, delete events
  - Upload event images
  - Manage event dates, times, and locations
  - Publish/unpublish events

- **Ticket Management**
  - Create multiple ticket types per event
  - Set pricing and availability
  - Real-time inventory tracking

- **Booking System**
  - Select event and ticket quantity
  - Secure reservation process
  - Booking modification with additional payment flow
  - Booking cancellation with refund tracking

- **Payment Processing**
  - Stripe integration for credit card payments
  - PayPal integration as alternative payment method
  - Transaction recording and verification
  - Additional payment handling for booking updates
  - Refund recording

- **Notifications**
  - Email confirmations for bookings, updates, and cancellations
  - System notifications for important events

- **Admin Dashboard**
  - User management
  - Event oversight
  - System-wide reporting

- **Organizer Dashboard**
  - Event performance metrics
  - Booking statistics
  - Revenue tracking

### Additional Features
- Comprehensive activity logging
- Database transaction management for data integrity
- Race condition prevention for ticket bookings
- Detailed error handling
- Extensive test coverage

## Technology Stack

- **Backend**: Laravel 11.x, PHP 8.x
- **Database**: MySQL
- **Frontend**: Blade templates, Bootstrap 5, JavaScript
- **Authentication**: Laravel's built-in authentication
- **Payment Gateways**: Stripe, PayPal
- **Email**: Laravel Mail with SMTP
- **Testing**: PHPUnit

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/HP181/Ticket-Booking-App.git
   cd Ticket-Booking-App
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Set up environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database in .env file**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=event_ticket_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Create storage link**
   ```bash
   php artisan storage:link
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## Configuration

### Payment Gateway Integration

1. **Stripe Configuration**
   Add your Stripe API keys to the `.env` file:
   ```
   STRIPE_KEY=your_stripe_public_key
   STRIPE_SECRET=your_stripe_secret_key
   STRIPE_WEBHOOK_SECRET=your_webhook_secret
   ```

2. **PayPal Configuration**
   Add your PayPal API credentials to the `.env` file:
   ```
   PAYPAL_CLIENT_ID=your_paypal_client_id
   PAYPAL_SECRET=your_paypal_secret
   PAYPAL_MODE=sandbox
   ```

### Email Configuration

Configure your email settings in the `.env` file:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=events@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Logging Configuration

Multiple log channels are configured for different types of events:
- Authentication logs
- CRUD operation logs
- Error logs
- Development/production environment logs

## Usage

### Default Users

After running the seeders, the following users will be available:

1. **Admin**
   - Email: admin@example.com
   - Password: password

2. **Organizer**
   - Email: organizer@example.com
   - Password: password

3. **Attendee**
   - Email: attendee@example.com
   - Password: password

### Common Workflows

1. **Creating an Event (Organizer)**
   - Log in as an Organizer
   - Navigate to the Organizer Dashboard
   - Click "Create Event"
   - Fill in event details and upload an image
   - Add ticket types with pricing information
   - Publish the event when ready

2. **Booking Tickets (Attendee)**
   - Browse available events on the homepage
   - Select an event to view details
   - Choose ticket type and quantity
   - Proceed to checkout
   - Complete payment via Stripe or PayPal
   - Receive booking confirmation email

3. **Managing Users (Admin)**
   - Log in as an Administrator
   - Navigate to the Admin Dashboard
   - Access the User Management section
   - View, edit user roles, or manage user accounts

## User Roles

### Admin
- Manage all users (view, edit roles)
- Oversee all events (approve/reject, view details)
- Access reporting dashboard with system-wide statistics
- Monitor booking and payment activities

### Organizer
- Create and manage events
- Create various ticket types with different prices
- View bookings for their events
- Generate event-specific reports
- Publish/unpublish events

### Attendee
- Browse and search for events
- View event details and available ticket types
- Make bookings with secure payment
- View, update, and cancel existing bookings
- Receive email notifications for booking activities

## Testing

The application includes comprehensive tests to ensure functionality and reliability:

- **Unit Tests**: Verify model relationships and core functionality
- **Feature Tests**: Test end-to-end workflows and user interactions

To run the tests:
```bash
php artisan test
```

### Test Coverage
- Authentication flows
- Event management
- Booking processes
- Payment handling
- Access control

## Deployment

### Server Requirements
- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Node.js & NPM
- SSL certificate (for secure payment processing)

### Deployment Steps

1. **Prepare the environment**
   - Set up a production server with the required specifications
   - Configure web server (Apache/Nginx) to point to the public directory

2. **Deploy the application**
   ```bash
   git clone https://github.com/HP181/Ticket-Booking-App.git
   cd Ticket-Booking-App
   composer install --optimize-autoloader --no-dev
   npm install
   npm run build
   ```

3. **Configure production environment**
   - Set up `.env` file with production settings
   - Set `APP_ENV=production` and `APP_DEBUG=false`
   - Configure database connection
   - Set up mail and payment gateway credentials

4. **Finalize deployment**
   ```bash
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Set up scheduled tasks**
   Add a cron entry to run Laravel's scheduler:
   ```
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Project Structure

The application follows the standard Laravel directory structure with a few additional custom components:

- `app/Models`: Database models (User, Event, TicketType, Booking, Payment, etc.)
- `app/Http/Controllers`: Controllers organized by functionality
- `app/Http/Middleware`: Custom middleware including AuthLogging
- `app/Services`: Service classes including LoggingService
- `app/Mail`: Email classes for different notifications
- `database/migrations`: Database structure definitions
- `database/seeders`: Seed data for testing and development
- `resources/views`: Blade templates organized by feature
- `routes/web.php`: Web routes with role-based access control
- `tests/`: Unit and Feature tests

## Screenshots

(Include screenshots of key interfaces here)

## License

[MIT License](LICENSE.md)

## Contributors

- Your Name - Initial work and development

## Acknowledgments

- Laravel Team for the amazing framework
- Bootstrap team for the frontend components
- All open-source packages used in this project

