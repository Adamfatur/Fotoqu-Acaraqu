#!/bin/bash

# Fotoku - Security Verification Script
# Comprehensive security and obfuscation verification
# Author: Fotoku Development Team

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

PASSED=0
FAILED=0
WARNINGS=0

print_header() {
    echo -e "${BLUE}================================${NC}"
    echo -e "${BLUE}  🔍 Fotoku Security Audit${NC}"
    echo -e "${BLUE}================================${NC}\n"
}

print_check() {
    echo -e "${BLUE}[CHECK]${NC} $1"
}

print_pass() {
    echo -e "${GREEN}[PASS]${NC} $1"
    PASSED=$((PASSED + 1))
}

print_fail() {
    echo -e "${RED}[FAIL]${NC} $1"
    FAILED=$((FAILED + 1))
}

print_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
    WARNINGS=$((WARNINGS + 1))
}

# Check environment configuration
check_environment() {
    print_check "Checking environment configuration..."
    
    if [ ! -f ".env" ]; then
        print_fail "Missing .env file"
        return
    fi
    
    # Check APP_ENV
    if grep -q "APP_ENV=production" .env; then
        print_pass "APP_ENV set to production"
    else
        print_fail "APP_ENV not set to production"
    fi
    
    # Check APP_DEBUG
    if grep -q "APP_DEBUG=false" .env; then
        print_pass "APP_DEBUG disabled"
    else
        print_fail "APP_DEBUG not disabled"
    fi
    
    # Check APP_KEY
    if grep -q "APP_KEY=" .env && [ -n "$(grep APP_KEY= .env | cut -d'=' -f2)" ]; then
        print_pass "APP_KEY is set"
    else
        print_fail "APP_KEY not set"
    fi
    
    # Check database configuration
    if grep -q "DB_PASSWORD=" .env && [ -n "$(grep DB_PASSWORD= .env | cut -d'=' -f2)" ]; then
        print_pass "Database password configured"
    else
        print_warn "Database password not set or empty"
    fi
}

# Check file permissions and security
check_file_security() {
    print_check "Checking file permissions and security..."
    
    # Check .env file permissions
    if [ -f ".env" ]; then
        env_perms=$(stat -f "%A" .env 2>/dev/null || stat -c "%a" .env 2>/dev/null)
        if [ "$env_perms" = "600" ] || [ "$env_perms" = "644" ]; then
            print_pass ".env file permissions secure"
        else
            print_warn ".env file permissions: $env_perms (recommend 600)"
        fi
    fi
    
    # Check storage directory permissions
    if [ -d "storage" ]; then
        if [ -w "storage/logs" ] && [ -w "storage/framework" ]; then
            print_pass "Storage directories writable"
        else
            print_fail "Storage directories not writable"
        fi
    fi
    
    # Check for sensitive files in public
    sensitive_files=$(find public/ -name "*.env*" -o -name "*.log" -o -name "composer.*" 2>/dev/null || true)
    if [ -z "$sensitive_files" ]; then
        print_pass "No sensitive files in public directory"
    else
        print_fail "Sensitive files found in public directory"
        echo "$sensitive_files"
    fi
}

# Check obfuscation setup
check_obfuscation() {
    print_check "Checking code obfuscation setup..."
    
    # Check if obfuscator plugin exists
    if [ -f "vite-plugins/obfuscator.js" ]; then
        print_pass "Obfuscator plugin found"
    else
        print_fail "Obfuscator plugin missing"
    fi
    
    # Check package.json for obfuscation dependencies
    if [ -f "package.json" ]; then
        if grep -q "javascript-obfuscator" package.json; then
            print_pass "JavaScript obfuscator dependency found"
        else
            print_fail "JavaScript obfuscator dependency missing"
        fi
        
        if grep -q "build:secure" package.json; then
            print_pass "Secure build script found"
        else
            print_warn "Secure build script not found in package.json"
        fi
    fi
    
    # Check Vite configuration
    if [ -f "vite.config.js" ]; then
        if grep -q "obfuscatorPlugin" vite.config.js; then
            print_pass "Vite obfuscation configured"
        else
            print_fail "Vite obfuscation not configured"
        fi
        
        if grep -q "NODE_ENV.*production" vite.config.js; then
            print_pass "Production-only obfuscation configured"
        else
            print_warn "Production check not found in Vite config"
        fi
    fi
}

# Check build output
check_build_output() {
    print_check "Checking build output and obfuscation..."
    
    build_dir="public/build"
    if [ ! -d "$build_dir" ]; then
        print_warn "Build directory not found - run 'npm run build:secure' first"
        return
    fi
    
    # Check for JavaScript files
    js_files=$(find "$build_dir" -name "*.js" 2>/dev/null || true)
    if [ -z "$js_files" ]; then
        print_warn "No JavaScript files found in build"
        return
    fi
    
    obfuscated_count=0
    total_js_files=0
    
    for file in $js_files; do
        total_js_files=$((total_js_files + 1))
        
        # Check file size (obfuscated files are typically larger)
        file_size=$(wc -c < "$file")
        
        # Check for obfuscation indicators
        has_readable_vars=$(grep -c "\bvar\s\+[a-zA-Z_][a-zA-Z0-9_]*\s*=" "$file" 2>/dev/null || echo 0)
        has_readable_funcs=$(grep -c "\bfunction\s\+[a-zA-Z_][a-zA-Z0-9_]*\s*(" "$file" 2>/dev/null || echo 0)
        has_obfuscated_vars=$(grep -c "\b_0x[a-f0-9]\+\b" "$file" 2>/dev/null || echo 0)
        
        if [ $has_obfuscated_vars -gt 10 ] && [ $has_readable_vars -lt 5 ] && [ $has_readable_funcs -lt 5 ]; then
            obfuscated_count=$((obfuscated_count + 1))
        fi
    done
    
    if [ $obfuscated_count -eq $total_js_files ] && [ $total_js_files -gt 0 ]; then
        print_pass "All JavaScript files appear obfuscated ($obfuscated_count/$total_js_files)"
    elif [ $obfuscated_count -gt 0 ]; then
        print_warn "Some JavaScript files obfuscated ($obfuscated_count/$total_js_files)"
    else
        print_fail "JavaScript files do not appear to be obfuscated"
    fi
    
    # Check for .htaccess security file
    if [ -f "$build_dir/.htaccess" ]; then
        print_pass "Security .htaccess file generated"
    else
        print_warn "Security .htaccess file not found"
    fi
}

# Check Laravel security configurations
check_laravel_security() {
    print_check "Checking Laravel security configurations..."
    
    # Check if artisan is available
    if [ ! -f "artisan" ]; then
        print_fail "Laravel artisan not found"
        return
    fi
    
    # Check config cache
    if php artisan config:show app.debug 2>/dev/null | grep -q "false"; then
        print_pass "Debug mode disabled in cached config"
    else
        print_warn "Config not cached or debug mode enabled"
    fi
    
    # Check CSRF middleware
    if grep -r "VerifyCsrfToken" app/Http/Middleware/ 2>/dev/null | grep -q "class"; then
        print_pass "CSRF protection middleware found"
    else
        print_warn "CSRF protection middleware not found"
    fi
    
    # Check for security-related configurations
    if [ -f "config/fotoku.php" ]; then
        if grep -q "security" config/fotoku.php; then
            print_pass "Security configurations found in fotoku.php"
        else
            print_warn "Security configurations not found in fotoku.php"
        fi
    fi
}

# Check AWS/Cloud security
check_cloud_security() {
    print_check "Checking cloud and AWS security..."
    
    # Check S3 configuration
    if grep -q "FILESYSTEM_DISK=s3" .env 2>/dev/null; then
        print_pass "S3 filesystem configured"
        
        if grep -q "AWS_ACCESS_KEY_ID=" .env && [ -n "$(grep AWS_ACCESS_KEY_ID= .env | cut -d'=' -f2)" ]; then
            print_pass "AWS credentials configured"
        else
            print_fail "AWS credentials not configured"
        fi
    else
        print_warn "S3 filesystem not configured"
    fi
    
    # Check SES configuration
    if grep -q "MAIL_MAILER=ses" .env 2>/dev/null; then
        print_pass "SES email service configured"
    else
        print_warn "SES email service not configured"
    fi
    
    # Check Redis configuration for sessions/cache
    if grep -q "CACHE_STORE=redis" .env 2>/dev/null; then
        print_pass "Redis cache configured"
    else
        print_warn "Redis cache not configured"
    fi
}

# Generate security report
generate_report() {
    echo -e "\n${BLUE}================================${NC}"
    echo -e "${BLUE}  📊 Security Audit Report${NC}"
    echo -e "${BLUE}================================${NC}"
    
    total_checks=$((PASSED + FAILED + WARNINGS))
    
    echo -e "Total Checks: $total_checks"
    echo -e "${GREEN}Passed: $PASSED${NC}"
    echo -e "${RED}Failed: $FAILED${NC}"
    echo -e "${YELLOW}Warnings: $WARNINGS${NC}"
    
    if [ $FAILED -eq 0 ]; then
        echo -e "\n${GREEN}🔒 Security Status: GOOD${NC}"
        if [ $WARNINGS -gt 0 ]; then
            echo -e "${YELLOW}⚠️  Please review warnings above${NC}"
        fi
    else
        echo -e "\n${RED}🚨 Security Status: NEEDS ATTENTION${NC}"
        echo -e "${RED}Please fix failed checks before deployment${NC}"
    fi
    
    echo -e "\n${BLUE}Recommendations:${NC}"
    echo -e "1. Run 'npm run build:secure' for production build"
    echo -e "2. Ensure all environment variables are properly set"
    echo -e "3. Verify file permissions on server"
    echo -e "4. Test obfuscated JavaScript functionality"
    echo -e "5. Monitor logs for any security issues"
}

# Main execution
main() {
    print_header
    
    check_environment
    echo ""
    
    check_file_security
    echo ""
    
    check_obfuscation
    echo ""
    
    check_build_output
    echo ""
    
    check_laravel_security
    echo ""
    
    check_cloud_security
    echo ""
    
    generate_report
}

# Handle script arguments
case "${1:-}" in
    "env")
        check_environment
        ;;
    "files")
        check_file_security
        ;;
    "obfuscation")
        check_obfuscation
        check_build_output
        ;;
    "laravel")
        check_laravel_security
        ;;
    "cloud")
        check_cloud_security
        ;;
    *)
        main
        ;;
esac
