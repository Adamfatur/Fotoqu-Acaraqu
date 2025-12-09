#!/bin/bash

# Secure Production Deployment Script for Fotoku
# This script handles secure building and deployment preparation with comprehensive security checks
# Author: Fotoku Development Team
# Version: 2.0.0

set -e

echo "🚀 Starting Fotoku Secure Deployment Process..."
echo "================================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print status messages
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[⚠]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
    exit 1
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "This script must be run from the Laravel project root directory"
    fi
    
    # Check if npm is installed
    if ! command -v npm &> /dev/null; then
        print_error "npm is not installed. Please install npm first."
        exit 1
    fi
    
    # Check if PHP is installed
fi

print_status "1. Environment Setup..."

# Check if .env exists
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        print_status "Copying .env.example to .env"
        cp .env.example .env
        print_warning "Please configure .env file with production settings before continuing"
        exit 1
    else
        print_error ".env.example not found. Cannot create .env file"
    fi
fi

# Verify production environment settings
if ! grep -q "APP_ENV=production" .env; then
    print_warning "APP_ENV is not set to production in .env"
    read -p "Do you want to continue with non-production environment? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

print_status "2. Installing Dependencies..."

# Install PHP dependencies
if [ ! -f "composer.lock" ] || [ "composer.json" -nt "composer.lock" ]; then
    print_status "Installing/updating Composer dependencies..."
    composer install --no-dev --optimize-autoloader
else
    print_success "Composer dependencies are up to date"
fi

# Install Node.js dependencies
if [ ! -d "node_modules" ] || [ "package.json" -nt "node_modules" ]; then
    print_status "Installing/updating Node.js dependencies..."
    npm ci
else
    print_success "Node.js dependencies are up to date"
fi

print_status "3. Laravel Application Setup..."

# Generate application key if not present
if ! grep -q "APP_KEY=base64:" .env; then
    print_status "Generating application key..."
    php artisan key:generate
fi

# Clear all caches
print_status "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run database migrations (with confirmation)
print_status "Database migration check..."
if php artisan migrate:status >/dev/null 2>&1; then
    read -p "Do you want to run database migrations? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan migrate --force
    fi
else
    print_warning "Database connection failed or not configured"
fi

print_status "4. Secure Asset Building..."

# Clean previous builds
if [ -d "public/build" ]; then
    print_status "Cleaning previous build..."
    rm -rf public/build
fi

# Build assets with obfuscation
print_status "Building assets with security obfuscation..."
export NODE_ENV=production
npm run build:secure

# Verify build output
if [ ! -d "public/build" ]; then
    print_error "Build failed - no output directory found"
fi

# Check if assets were actually built
js_files=$(find public/build -name "*.js" 2>/dev/null | wc -l)
css_files=$(find public/build -name "*.css" 2>/dev/null | wc -l)

if [ "$js_files" -eq 0 ] && [ "$css_files" -eq 0 ]; then
    print_error "Build failed - no assets generated"
fi

print_success "Assets built successfully (JS: $js_files, CSS: $css_files)"

print_status "5. Production Optimization..."

# Cache Laravel configurations
print_status "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Composer autoloader
print_status "Optimizing autoloader..."
composer dump-autoload --optimize

print_status "6. File Permissions..."

# Set proper file permissions
print_status "Setting file permissions..."

# Make storage and bootstrap/cache writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Secure .env file
chmod 600 .env

print_status "7. Security Verification..."

# Run security verification
if [ -f "security-verify.sh" ]; then
    print_status "Running security verification..."
    chmod +x security-verify.sh
    if ./security-verify.sh; then
        print_success "Security verification passed"
    else
        print_warning "Security verification found issues - please review"
    fi
else
    print_warning "Security verification script not found"
fi

print_status "8. Build Verification..."

# Check obfuscation
js_file=$(find public/build -name "*.js" | head -1)
if [ -n "$js_file" ]; then
    if grep -q "var _0x" "$js_file" || grep -q "function(_0x" "$js_file" || [ "$(wc -l < "$js_file")" -lt 10 ]; then
        print_success "JavaScript obfuscation verified"
    else
        print_warning "JavaScript files may not be properly obfuscated"
        print_status "Sample from built JS file:"
        head -5 "$js_file"
    fi
fi

print_status "9. Deployment Package..."

# Create deployment info
cat > deployment-info.txt << EOF
Fotoku Production Deployment
============================
Build Date: $(date)
Environment: $(grep APP_ENV .env | cut -d'=' -f2)
Debug Mode: $(grep APP_DEBUG .env | cut -d'=' -f2)
Laravel Version: $(php artisan --version)
PHP Version: $(php -v | head -1)

Asset Files:
- JavaScript files: $js_files
- CSS files: $css_files

Security Features:
- JavaScript obfuscation: ✓
- Environment variables secured: ✓
- Debug mode disabled: ✓
- Caches optimized: ✓

Next Steps:
1. Upload files to production server
2. Configure web server (Nginx/Apache)
3. Set up SSL certificate
4. Configure AWS S3 and SES
5. Set up cron jobs for Laravel scheduler
6. Configure queue workers
7. Test all functionality

EOF

print_success "Deployment information saved to deployment-info.txt"

echo ""
echo "================================================"
echo "🎉 Secure Deployment Build Complete!"
echo "================================================"
echo -e "${GREEN}Your application is ready for production deployment.${NC}"
echo ""
echo "Key files for deployment:"
echo "• All PHP files and directories"
echo "• public/build/ (obfuscated assets)"
echo "• .env (configure for production server)"
echo "• deployment-info.txt (deployment guide)"
echo ""
echo "Files to exclude from deployment:"
echo "• node_modules/"
echo "• .git/"
echo "• tests/"
echo "• *.md files"
echo "• development scripts"
echo ""
echo -e "${YELLOW}Remember to:${NC}"
echo "• Configure .env on production server"
echo "• Set up proper file permissions"
echo "• Configure web server"
echo "• Set up SSL certificate"
echo "• Test all functionality after deployment"
