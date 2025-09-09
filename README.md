# Invest App

Full-stack Laravel + Vue 3 application with Dockerized MySQL database, Tailwind CSS, and Vite.

---

## Setup Guide

### 1. Database (Docker MySQL)
```bash
cd invest-repo/invest-db
docker-compose up -d --build
docker exec -it invest-db mysql -u root -p
```
#### Inside MySQL
```bash
create user 'usr'@'%' identified by 'root';
grant all privileges on *.* to 'usr'@'%';
flush privileges;
exit
```
### 2. Laravel Application
```bash
cd invest-repo/invest-app
docker-compose up -d --build
composer install
code .
```
1. Create a .env file in the project root.
2. Run ipconfig in CMD and get the IPv4 Address of WSL (e.g., 172.19.128.1).
3. Update your .env database configuration:
 ```bash
DB_CONNECTION=mysql
DB_HOST=172.19.128.1
DB_PORT=3306
DB_DATABASE=mysql
DB_USERNAME=root
DB_PASSWORD=root
```
4. Update your app URL:
```bash
APP_URL=http://localhost:8000
```
#### Run the Laravel setup
```bash
php artisan migrate
php artisan db:seed
php artisan key:generate
php artisan storage:link
```
#### Run (Optional - if neededed only)
Check directory [./resources/views/vendor]
```bash
php artisan vendor:publish --tag=laravel-mail
php artisan vendor:publish --tag=laravel-notifications
```
#### Create required directory
```bash
mkdir -p storage/app/public/child_templates
```
### 3. Frontend (Vue + Tailwind + Vite)
#### Install dependencies
```bash
npm i @vitejs/plugin-vue vue vue-router pinia axios
npm install -D tailwindcss postcss autoprefixer
```
#### Start the dev server
```bash
npm run dev
```
## Notes

- public/storage is linked to storage/app/public for file uploads.
- Place child templates under storage/app/public/child_templates.
