#!/usr/bin/env pwsh
# Laravel Project Setup Script
# Run this after cloning the repository

Write-Host "🚀 Setting up Laravel project..." -ForegroundColor Green

# Step 1: Install PHP dependencies
Write-Host "📦 Installing PHP dependencies..." -ForegroundColor Yellow
composer install
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Composer install failed!" -ForegroundColor Red
    exit 1
}

# Step 2: Copy environment file
Write-Host "⚙️ Setting up environment file..." -ForegroundColor Yellow
if (!(Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "✅ .env file created from .env.example" -ForegroundColor Green
} else {
    Write-Host "ℹ️ .env file already exists" -ForegroundColor Blue
}

# Step 3: Generate application key
Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
php artisan key:generate

# Step 4: Database setup instructions
Write-Host "🗄️ Database setup..." -ForegroundColor Yellow
Write-Host "⚠️  IMPORTANT: Make sure to:" -ForegroundColor Yellow
Write-Host "   1. Start your MySQL server (XAMPP, WAMP, or standalone MySQL)" -ForegroundColor Yellow
Write-Host "   2. Create a database named 'mccopes' in MySQL" -ForegroundColor Yellow
Write-Host "   3. Update your .env file with correct MySQL credentials if needed" -ForegroundColor Yellow
Write-Host ""

# Step 5: Run migrations
Write-Host "🔄 Running database migrations..." -ForegroundColor Yellow
php artisan migrate --force

# Step 6: Install Node.js dependencies (if npm is available)
if (Get-Command npm -ErrorAction SilentlyContinue) {
    Write-Host "📦 Installing Node.js dependencies..." -ForegroundColor Yellow
    npm install
    
    Write-Host "🏗️ Building frontend assets..." -ForegroundColor Yellow
    npm run build
} else {
    Write-Host "⚠️ npm not found. Skipping Node.js dependencies." -ForegroundColor Yellow
    Write-Host "   Install Node.js if you need frontend asset compilation." -ForegroundColor Yellow
}

# Step 7: Clear caches
Write-Host "🧹 Clearing caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

Write-Host ""
Write-Host "🎉 Setup complete!" -ForegroundColor Green
Write-Host "Run 'php artisan serve' to start the development server." -ForegroundColor Green
Write-Host "Your application will be available at: http://127.0.0.1:8000" -ForegroundColor Green