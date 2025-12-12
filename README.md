# Venta de Carros App

Full-stack car sales management system built with **Laravel 11 API + Vue 3 SPA**.

## Tech Stack
- **Backend**: Laravel 11, Sanctum (Auth), MySQL
- **Frontend**: Vue 3, Pinia, Vue Router, Vite
- **Styling**: Tailwind CSS (Mobile-first, Dark mode)

## Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL

## Installation

1. **Clone the repository**
   \\\ash
   git clone <repo-url>
   cd venta_car_vue_laravel
   \\\`n
2. **Backend Setup**
   \\\ash
   composer install
   cp .env.example .env
   php artisan key:generate
   \\\`n
3. **Database Setup**
   - Create a MySQL database named \enta_carros_app\ (or update \.env\).
   - Run migrations:
     \\\ash
     php artisan migrate --seed
     \\\`n   - Link storage:
     \\\ash
     php artisan storage:link
     \\\`n
4. **Frontend Setup**
   \\\ash
   npm install
   \\\`n
## Running the App

1. **Start Laravel API**
   \\\ash
   php artisan serve
   \\\`n
2. **Start Vite Dev Server**
   \\\ash
   npm run dev
   \\\`n
3. **Access the App**
   - Open [http://localhost:8000](http://localhost:8000)

## Project Structure
- \pp/\: Laravel Backend (Models, Controllers)
- \esources/js/\: Vue Frontend
  - \pages/\: Vue Pages (Cars, Finance, Dashboards)
  - \components/ui/\: Reusable UI components
  - \stores/\: Pinia state management
  - \outer/\: Vue Router configuration
- \outes/api.php\: API Endpoints

## Key Commands
- \php artisan test\: Run backend tests
- \
pm run build\: Build frontend for production
