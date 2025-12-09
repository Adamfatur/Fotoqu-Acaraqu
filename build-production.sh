#!/bin/bash

# FOTOKU Production Build Script with Obfuscation
# This script prepares the application for production deployment with security enhancements

echo "🔒 Starting FOTOKU Production Build with Security..."

# 1. Environment Setup
echo "📋 Setting up production environment..."
export NODE_ENV=production
export APP_ENV=production

# 2. Clear all caches
echo "🗑️ Clearing caches..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

# 3. Install/Update dependencies
echo "📦 Installing production dependencies..."
composer install --optimize-autoloader --no-dev
npm ci --production

# 4. Run security checks
echo "🔍 Running security checks..."
# Check for debug statements in production files
if grep -r "console.log\|debugger\|var_dump\|dd(" resources/views/photobox/ --exclude-dir=node_modules; then
    echo "⚠️  WARNING: Debug statements found in production files!"
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# 5. Build assets with obfuscation
echo "🔨 Building obfuscated assets..."
npm run build

# 6. Optimize PHP
echo "⚡ Optimizing PHP..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set secure permissions
echo "🔐 Setting secure file permissions..."
find storage/ -type f -exec chmod 644 {} \;
find storage/ -type d -exec chmod 755 {} \;
find bootstrap/cache/ -type f -exec chmod 644 {} \;
find bootstrap/cache/ -type d -exec chmod 755 {} \;

# 8. Generate security manifest
echo "📋 Generating security manifest..."
cat > storage/app/security-manifest.json << EOF
{
    "build_date": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "security_features": {
        "obfuscation": true,
        "minification": true,
        "console_protection": true,
        "anti_debugging": true,
        "api_protection": true
    },
    "environment": "production",
    "version": "$(git rev-parse --short HEAD 2>/dev/null || echo 'unknown')"
}
EOF

echo "✅ Production build completed successfully!"
echo "🔒 Security features enabled:"
echo "   - JavaScript obfuscation"
echo "   - Console protection"
echo "   - Anti-debugging measures"
echo "   - API token protection"
echo "   - File permission security"
echo ""
echo "📋 Next steps:"
echo "   1. Deploy to production server"
echo "   2. Update environment variables"
echo "   3. Run database migrations if needed"
echo "   4. Test security features"
echo ""
echo "⚠️  Remember to:"
echo "   - Set APP_DEBUG=false"
echo "   - Set APP_ENV=production"
echo "   - Configure proper SSL certificates"
echo "   - Enable server-level security (WAF, etc.)"
