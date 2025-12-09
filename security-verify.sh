#!/bin/bash

# Security Verification Script for Fotoku Production Deployment
# This script verifies all security measures are properly configured

set -e

echo "🔒 Starting Fotoku Security Verification..."
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
}

# Track verification results
SECURITY_CHECKS=0
SECURITY_PASSED=0

check_security_item() {
    SECURITY_CHECKS=$((SECURITY_CHECKS + 1))
    if [ $1 -eq 0 ]; then
        print_success "$2"
        SECURITY_PASSED=$((SECURITY_PASSED + 1))
    else
        print_error "$2"
    fi
}

print_status "1. Checking Laravel Security Configuration..."

# Check if .env exists and has required security settings
if [ -f ".env" ]; then
    print_success ".env file exists"
    
    # Check APP_DEBUG
    if grep -q "APP_DEBUG=false" .env; then
        check_security_item 0 "APP_DEBUG is set to false"
    else
        check_security_item 1 "APP_DEBUG should be set to false in production"
    fi
    
    # Check APP_ENV
    if grep -q "APP_ENV=production" .env; then
        check_security_item 0 "APP_ENV is set to production"
    else
        check_security_item 1 "APP_ENV should be set to production"
    fi
    
    # Check for APP_KEY
    if grep -q "APP_KEY=base64:" .env; then
        check_security_item 0 "APP_KEY is properly configured"
    else
        check_security_item 1 "APP_KEY should be generated and configured"
    fi
else
    check_security_item 1 ".env file not found - copy from .env.example and configure"
fi

print_status "2. Checking Vite Configuration and Obfuscation..."

# Check if vite.config.js exists
if [ -f "vite.config.js" ]; then
    print_success "vite.config.js found"
    
    # Check for custom obfuscator plugin import
    if grep -q "obfuscatorPlugin" vite.config.js && grep -q "vite-plugins/obfuscator.js" vite.config.js; then
        check_security_item 0 "Custom JavaScript obfuscator plugin is configured"
    elif grep -q "vite-plugin-javascript-obfuscator" vite.config.js; then
        check_security_item 0 "Standard JavaScript obfuscator plugin is configured"
    else
        check_security_item 1 "JavaScript obfuscator plugin not found in vite.config.js"
    fi
    
    # Check for production environment condition
    if grep -q "NODE_ENV === 'production'" vite.config.js; then
        check_security_item 0 "Obfuscation is properly configured for production only"
    else
        check_security_item 1 "Obfuscation should be limited to production environment"
    fi
else
    check_security_item 1 "vite.config.js not found"
fi

# Check if custom obfuscator plugin exists
if [ -f "vite-plugins/obfuscator.js" ]; then
    check_security_item 0 "Custom obfuscator plugin file exists"
    
    # Check for advanced obfuscation settings
    if grep -q "debugProtection.*true" vite-plugins/obfuscator.js && grep -q "selfDefending.*true" vite-plugins/obfuscator.js; then
        check_security_item 0 "Advanced obfuscation protection is enabled"
    else
        check_security_item 1 "Advanced obfuscation protection settings not found"
    fi
else
    print_warning "Custom obfuscator plugin not found - using standard plugin"
fi

print_status "3. Checking Node.js Dependencies..."

# Check if package.json exists
if [ -f "package.json" ]; then
    print_success "package.json found"
    
    # Check for obfuscator dependency
    if grep -q "javascript-obfuscator" package.json; then
        check_security_item 0 "JavaScript obfuscator dependency is listed in package.json"
    else
        check_security_item 1 "JavaScript obfuscator dependency not found in package.json"
    fi
    
    # Check for security-related scripts
    if grep -q "build:secure" package.json || grep -q "build:prod" package.json; then
        check_security_item 0 "Secure build script is configured"
    else
        check_security_item 1 "Secure build script not found in package.json"
    fi
    
    # Check for production build script
    if grep -q "NODE_ENV=production" package.json; then
        check_security_item 0 "Production environment is properly configured in build scripts"
    else
        check_security_item 1 "Production environment should be set in build scripts"
    fi
else
    check_security_item 1 "package.json not found"
fi

print_status "4. Checking File Permissions..."

# Check critical file permissions
critical_files=(".env" "storage" "bootstrap/cache")
for file in "${critical_files[@]}"; do
    if [ -e "$file" ]; then
        if [ "$file" = ".env" ]; then
            # .env should not be world-readable
            if [ "$(stat -f %A "$file" 2>/dev/null || stat -c %a "$file" 2>/dev/null)" != "600" ]; then
                print_warning ".env file permissions should be 600 (owner read/write only)"
            else
                check_security_item 0 ".env file has correct permissions (600)"
            fi
        else
            # Storage and cache directories should be writable
            if [ -w "$file" ]; then
                check_security_item 0 "$file directory is writable"
            else
                check_security_item 1 "$file directory is not writable"
            fi
        fi
    else
        check_security_item 1 "$file not found or not accessible"
    fi
done

print_status "5. Checking Composer Dependencies..."

# Check if composer.lock exists (ensures reproducible builds)
if [ -f "composer.lock" ]; then
    check_security_item 0 "composer.lock exists (ensures reproducible builds)"
else
    check_security_item 1 "composer.lock not found - run 'composer install' to generate"
fi

print_status "6. Checking Git Security..."

# Check if .env is in .gitignore
if [ -f ".gitignore" ]; then
    if grep -q "\.env" .gitignore; then
        check_security_item 0 ".env is properly excluded from git"
    else
        check_security_item 1 ".env should be added to .gitignore"
    fi
else
    check_security_item 1 ".gitignore file not found"
fi

print_status "7. Testing Build Process..."

# Check if node_modules exists
if [ -d "node_modules" ]; then
    print_success "node_modules directory exists"
    
    # Test if build process works
    print_status "Testing secure build process..."
    if npm run build:secure >/dev/null 2>&1; then
        check_security_item 0 "Secure build process completed successfully"
        
        # Check if obfuscated files exist
        if [ -d "public/build" ]; then
            js_files=$(find public/build -name "*.js" | head -1)
            if [ -n "$js_files" ]; then
                print_status "Analyzing obfuscated JavaScript output..."
                
                # Enhanced obfuscation verification using simple grep patterns
                obfuscation_score=0
                
                # Check for hexadecimal variable patterns
                if grep -q "_0x[a-f0-9]" "$js_files"; then
                    obfuscation_score=$((obfuscation_score + 1))
                fi
                
                # Check for obfuscated function patterns
                if grep -q "function(_0x" "$js_files"; then
                    obfuscation_score=$((obfuscation_score + 1))
                fi
                
                # Check for obfuscated variable declarations
                if grep -q "var _0x" "$js_files"; then
                    obfuscation_score=$((obfuscation_score + 1))
                fi
                
                # Check for string arrays
                if grep -q "stringArray" "$js_files" || head -c 1000 "$js_files" | grep -q "\['[^']*'," ; then
                    obfuscation_score=$((obfuscation_score + 1))
                fi
                
                # Check for control flow flattening indicators
                if head -c 2000 "$js_files" | grep -q "switch" && head -c 2000 "$js_files" | grep -q "case"; then
                    obfuscation_score=$((obfuscation_score + 1))
                fi
                
                # Check for minified/compact code
                line_count=$(wc -l < "$js_files")
                if [ "$line_count" -lt 20 ]; then
                    obfuscation_score=$((obfuscation_score + 1))
                fi
                
                total_patterns=6
                
                # Check file characteristics
                file_size=$(wc -c < "$js_files")
                line_count=$(wc -l < "$js_files")
                
                # Enhanced obfuscation verification
                if [ $obfuscation_score -ge 3 ] && [ $file_size -gt 100000 ] && [ $line_count -lt 50 ]; then
                    check_security_item 0 "JavaScript files are properly obfuscated (Score: $obfuscation_score/$total_patterns)"
                    
                    # Additional security checks
                    if grep -q "console\." "$js_files"; then
                        print_warning "Console statements detected in obfuscated code"
                    else
                        print_success "Console statements properly removed from production build"
                    fi
                    
                    if grep -q "debugger" "$js_files"; then
                        print_warning "Debugger statements detected in obfuscated code"
                    else
                        print_success "Debugger statements properly removed from production build"
                    fi
                    
                elif [ $obfuscation_score -ge 1 ]; then
                    check_security_item 1 "JavaScript files are partially obfuscated (Score: $obfuscation_score/$total_patterns) - may need stronger settings"
                else
                    check_security_item 1 "JavaScript files do not appear to be properly obfuscated"
                fi
                
                # Check for source maps (should not exist in production)
                if find public/build -name "*.map" | grep -q .; then
                    check_security_item 1 "Source map files found - these should be excluded from production"
                else
                    check_security_item 0 "No source map files found in production build"
                fi
            else
                check_security_item 1 "No JavaScript files found in build output"
            fi
        else
            check_security_item 1 "Build output directory not found"
        fi
    else
        check_security_item 1 "Secure build process failed"
    fi
else
    print_warning "node_modules not found - run 'npm install' first"
fi

print_status "8. Production Deployment Readiness..."

# Summary checks for production readiness
production_ready=true

# Check critical production settings
if [ ! -f ".env" ] || ! grep -q "APP_ENV=production" .env; then
    production_ready=false
fi

if [ ! -f ".env" ] || ! grep -q "APP_DEBUG=false" .env; then
    production_ready=false
fi

if [ ! -d "public/build" ]; then
    production_ready=false
fi

if [ "$production_ready" = true ]; then
    check_security_item 0 "Application appears ready for production deployment"
else
    check_security_item 1 "Application is NOT ready for production deployment"
fi

echo ""
echo "================================================"
echo "🔒 Security Verification Summary"
echo "================================================"
echo -e "Total Checks: ${BLUE}$SECURITY_CHECKS${NC}"
echo -e "Passed: ${GREEN}$SECURITY_PASSED${NC}"
echo -e "Failed: ${RED}$((SECURITY_CHECKS - SECURITY_PASSED))${NC}"

if [ $SECURITY_PASSED -eq $SECURITY_CHECKS ]; then
    echo -e "${GREEN}🎉 All security checks passed! Ready for production.${NC}"
    exit 0
else
    echo -e "${YELLOW}⚠️  Some security checks failed. Please review and fix before production deployment.${NC}"
    exit 1
fi
