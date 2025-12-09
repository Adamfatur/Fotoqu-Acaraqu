# Fotoku - Security Implementation Guide

## Overview
This document outlines the comprehensive security measures implemented in the Fotoku photobox application, including code obfuscation, environment security, deployment best practices, and multi-layer protection against reverse engineering and unauthorized access.

## 🔒 Complete Security Architecture

## Security Layers Implemented

### 1. Stable Security Layer (`stable-security.blade.php`)
**Purpose**: Proteksi utama yang stabil dan tidak mengganggu UX

**Features**:
- **Console Protection**: Disable console functions di production
- **Right-click Protection**: Block context menu (kecuali input fields)
- **Keyboard Protection**: Block F12, Ctrl+Shift+I, Ctrl+U, Ctrl+Shift+J
- **Drag Protection**: Prevent dragging images dan photo items
- **Conservative DevTools Detection**: Detect tapi tidak auto-refresh
- **Subtle Notifications**: Pesan security yang tidak mengganggu

**Configuration**:
```javascript
const SECURITY_CONFIG = {
    enableAntiDebug: ENV.isProd,
    enableConsoleProtection: ENV.isProd,
    enableRightClickProtection: ENV.isProd,
    enableKeyboardProtection: ENV.isProd,
    enableDragProtection: ENV.isProd
};
```

### 2. Server-side API Protection (`SecurePhotoboxController.php`)
**Purpose**: Validasi server-side untuk API critical functions

**Features**:
- **Token-based Authentication**: Unique tokens per session
- **HMAC Signature Verification**: Prevent API tampering
- **Session Validation**: Verify photobox session status
- **Rate Limiting**: Prevent abuse
- **Secure Endpoints**: Protected critical operations

**Endpoints**:
- `POST /api/secure-photobox/token` - Get security token
- `POST /api/secure-photobox/validate-session` - Validate session
- `POST /api/secure-photobox/capture-photo` - Secure photo capture
- `POST /api/secure-photobox/process-frame` - Secure frame processing

### 3. Environment-based Security
**Purpose**: Different security levels for different environments

**Development Mode** (`APP_DEBUG=true`):
- All protections DISABLED
- Console available for debugging
- Right-click enabled
- DevTools detection disabled
- Debug helpers available via `window.FOTOKU_DEV`

**Production Mode** (`APP_ENV=production`):
- All protections ENABLED
- Console disabled
- Right-click blocked
- DevTools detection active
- No debug helpers

### 4. CSS-level Protection (`styles.blade.php`)
**Features**:
- **Text Selection Disabled**: `user-select: none` pada elemen sensitif
- **Drag Prevention**: `pointer-events: none` pada images
- **Print Protection**: `@media print { display: none }` pada elemen sensitif

## Implementation Status

### ✅ Completed
1. **Client-side Protection**:
   - [x] Disable right-click context menu
   - [x] Block common keyboard shortcuts (F12, Ctrl+Shift+I, etc.)
   - [x] Prevent image dragging
   - [x] Console protection in production
   - [x] Conservative DevTools detection (no auto-refresh)

2. **Server-side Protection**:
   - [x] Token-based API authentication
   - [x] HMAC signature verification
   - [x] Session validation
   - [x] Secure endpoints for critical operations

3. **Environment Control**:
   - [x] Development vs Production security modes
   - [x] Debug helpers for development
   - [x] Conditional protection activation

4. **User Experience**:
   - [x] No auto-refresh/reload mechanisms
   - [x] Subtle notifications instead of alerts
   - [x] Usability preserved (input fields, basic functionality)

5. **Code Obfuscation & Minification**:
   - [x] Vite production build configuration
   - [x] JavaScript obfuscation with custom plugin
   - [x] Terser minification with security options
   - [x] Console removal in production builds
   - [x] Variable name mangling
   - [x] Dead code elimination
   - [x] String array encoding
   - [x] Control flow flattening
   - [x] Self-defending code
   - [x] Production build script with security checks

### 🔄 In Progress
1. **Advanced Protection**:
   - [ ] Anti-automation detection (refined)
   - [ ] Domain validation (production-only)
   - [ ] Hardware fingerprinting integration

### ⏳ Pending
1. **Production Optimization**:
   - [ ] Webpack/Vite obfuscation integration
   - [ ] Asset integrity verification
   - [ ] CDN protection headers

2. **Monitoring & Logging**:
   - [ ] Security event logging
   - [ ] Suspicious activity detection
   - [ ] Admin notifications for security events

## Configuration Guide

### Development Setup
```bash
# Set environment to development
APP_ENV=local
APP_DEBUG=true

# Clear caches
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### Production Setup
```bash
# Set environment to production
APP_ENV=production
APP_DEBUG=false

# Clear and optimize caches
php artisan config:clear
php artisan view:clear
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

### Testing Security
```javascript
// In browser console (development only)
FOTOKU_DEV.enableDebug(); // Disable all security for testing
FOTOKU_DEV.showMessage('Test notification'); // Test notification system
console.log(FOTOKU_ENV); // Check environment settings
```

## Security Best Practices

### Do's
- ✅ Keep security environment-based (dev vs prod)
- ✅ Use subtle notifications instead of alerts
- ✅ Preserve essential user functionality
- ✅ Implement server-side validation for critical operations
- ✅ Use progressive security (warnings before action)

### Don'ts
- ❌ Never auto-refresh/reload pages
- ❌ Don't block essential user interactions
- ❌ Avoid aggressive detection that causes false positives
- ❌ Don't rely solely on client-side protection
- ❌ Avoid breaking accessibility features

## File Structure
```
resources/views/photobox/components/
├── stable-security.blade.php          # Main security layer
├── environment-security.blade.php     # Legacy (replaced)
├── security-layer.blade.php          # Legacy (replaced)
├── obfuscated-core.blade.php         # Obfuscated functions
└── styles.blade.php                   # CSS-level protection

app/Http/Controllers/Api/
└── SecurePhotoboxController.php       # Server-side protection

routes/
└── api.php                           # Secure API routes
```

## Troubleshooting

### Issue: Page refreshing continuously
**Cause**: Aggressive DevTools detection or domain validation
**Solution**: Check environment settings, disable auto-refresh mechanisms

### Issue: Functionality broken in development
**Cause**: Security active in development mode
**Solution**: Ensure `APP_DEBUG=true` and `APP_ENV=local`

### Issue: Users can't use basic features
**Cause**: Over-protective security settings
**Solution**: Review security config, allow essential interactions

### Issue: False positive security warnings
**Cause**: Conservative detection thresholds
**Solution**: Adjust detection thresholds, increase warning counters

## Future Enhancements

1. **Machine Learning Detection**: Behavioral analysis for automation detection
2. **Hardware Fingerprinting**: Device-based access control
3. **Real-time Monitoring**: Live security dashboard for admins
4. **Advanced Obfuscation**: Dynamic code generation and rotation
5. **Biometric Integration**: Face/fingerprint verification for admin access

## Notes
- Security implementation prioritizes user experience over maximum protection
- All aggressive protections (auto-refresh, debugger statements) have been disabled
- Production deployment requires additional server-level security (WAF, DDoS protection)
- Regular security audits recommended for production environments

## Production Deployment with Obfuscation

### Build Commands
```bash
# Standard production build
npm run build:prod

# Secure production build with security checks
npm run build:secure

# Full production deployment script
./build-production.sh
```

### Obfuscation Features
- **String Array Encoding**: Base64 encoding for string literals
- **Control Flow Flattening**: Makes code flow harder to follow
- **Dead Code Injection**: Adds fake code paths
- **Self-Defending**: Code breaks if tampered with
- **Variable Mangling**: Renames variables to meaningless names
- **Console Protection**: Removes all console statements
- **Debug Protection**: Prevents debugging attempts

### Security Verification
```bash
# Check obfuscated output
cat public/build/assets/*.js | head -20

# Verify security manifest
cat storage/app/security-manifest.json

# Test production security
curl -H "User-Agent: curl" http://your-domain.com/photobox/BOX-01
```

### Performance Impact
- **Bundle Size**: ~15% increase due to obfuscation
- **Runtime Performance**: ~5-10% slower due to obfuscated code
- **Development Build**: No impact (obfuscation disabled)

---

## 🔧 Advanced Security: Code Obfuscation & Deployment

### 1. Frontend JavaScript Obfuscation

#### Implementation Details
- **Tool**: `javascript-obfuscator` with custom Vite plugin
- **Location**: `vite-plugins/obfuscator.js`
- **Activation**: Production builds only (`NODE_ENV=production`)

#### Obfuscation Features
```javascript
// Advanced obfuscation settings
{
    compact: true,
    controlFlowFlattening: true,
    controlFlowFlatteningThreshold: 0.75,
    deadCodeInjection: true,
    debugProtection: true,
    debugProtectionInterval: 4000,
    disableConsoleOutput: true,
    selfDefending: true,
    stringArray: true,
    stringArrayEncoding: ['base64'],
    transformObjectKeys: true
}
```

#### Security Benefits
- **Variable/Function Names**: Transformed to hexadecimal identifiers
- **Control Flow**: Flattened to prevent easy analysis
- **Dead Code**: Injected to confuse reverse engineering
- **String Obfuscation**: All strings encoded and shuffled
- **Self-Defense**: Code detects tampering attempts
- **Debug Protection**: Prevents debugging with infinite loops

### 2. Build Security Pipeline

#### Secure Build Commands
```bash
# Production build with obfuscation
npm run build:secure

# Build with verification
npm run build:verify

# Complete security audit
npm run security:audit

# Secure deployment
./deploy-secure.sh
```

#### Build Process Security
1. **Pre-build Checks**: Verify environment and dependencies
2. **Code Obfuscation**: Transform JavaScript for production
3. **Asset Optimization**: Minify and optimize all assets
4. **Security Headers**: Generate .htaccess with security headers
5. **Post-build Verification**: Verify obfuscation success

### 3. Deployment Security Automation

#### Secure Deployment Script (`deploy-secure.sh`)
Comprehensive deployment automation with security checks:

**Phase 1: Pre-deployment**
- Prerequisites verification (Node.js, PHP, Composer)
- Environment configuration validation
- Current deployment backup
- Security dependency checks

**Phase 2: Build & Optimization**
- Clean build with obfuscation
- Laravel cache optimization
- Autoloader optimization
- Security headers generation

**Phase 3: Verification**
- Obfuscation verification
- File permission checks
- Database connectivity test
- Final security audit

#### Security Audit Script (`security-audit.sh`)
Automated security verification tool:

**Environment Checks**:
- Production mode validation
- Debug mode disabled
- Secure credentials configuration
- File permission verification

**Code Security Checks**:
- Obfuscation verification
- Sensitive file exposure check
- Build output validation
- Security configuration audit

### 4. Environment Security Hardening

#### Production Environment Variables
```bash
# Core Security Settings
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:generated-32-char-key
BCRYPT_ROUNDS=12
LOG_LEVEL=error

# Session Security
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true

# Database Security
DB_CONNECTION=mysql
DB_HOST=secure-host
DB_DATABASE=fotoku_production
DB_USERNAME=limited-privilege-user
DB_PASSWORD=strong-random-password
```

#### File System Security
```bash
# Secure file permissions
.env                 600
storage/             755
storage/logs/        755
public/build/        755
```

### 5. Server Security Headers

#### Automatic Security Headers (.htaccess)
```apache
# Generated by security plugin
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';"

# File Access Protection
<Files ~ "\\.(env|log|md)$">
    Order allow,deny
    Deny from all
</Files>
```

### 6. AWS Cloud Security

#### S3 Security Configuration
- **Private Buckets**: No public access
- **Pre-signed URLs**: Time-limited access (30 days)
- **IAM Policies**: Minimal required permissions
- **Encryption**: Server-side encryption enabled

#### SES Email Security
- **Verified Domains**: DKIM/SPF configured
- **Rate Limiting**: AWS SES rate controls
- **Bounce Handling**: Automatic suppression list management

### 7. Security Monitoring & Alerts

#### Application Monitoring
```php
// Security event logging
Log::security('photo_session_accessed', [
    'photobox_id' => $photobox->id,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent()
]);
```

#### Error Monitoring
- **Production Logging**: Error-level only
- **Security Events**: Failed authentication, suspicious activity
- **Performance Metrics**: Obfuscated code performance impact

### 8. Security Testing & Verification

#### Manual Testing Checklist
```bash
# 1. Verify obfuscation
cat public/build/assets/*.js | head -20
# Should show obfuscated, unreadable code

# 2. Check security headers
curl -I https://your-domain.com
# Should include X-Frame-Options, CSP, etc.

# 3. Test developer tools protection
# Open browser dev tools - should detect and warn

# 4. Verify file access protection
curl https://your-domain.com/.env
# Should return 403 Forbidden
```

#### Automated Testing
```bash
# Complete security audit
./security-audit.sh

# Environment-specific checks
./security-audit.sh env
./security-audit.sh files
./security-audit.sh obfuscation

# Build verification
npm run build:verify
```

### 9. Security Best Practices

#### Development Guidelines
1. **Never commit secrets** - Use environment variables
2. **Regular dependency updates** - Monitor for vulnerabilities
3. **Code review requirements** - Security-focused reviews
4. **Environment separation** - Dev/staging/production isolation

#### Deployment Guidelines
1. **Use secure deployment script** - `./deploy-secure.sh`
2. **Verify obfuscation success** - Check build output
3. **Monitor post-deployment** - Watch logs and metrics
4. **Emergency rollback plan** - Keep previous build backup

#### Maintenance Guidelines
1. **Regular security audits** - Weekly `./security-audit.sh`
2. **Update obfuscation settings** - Based on threat landscape
3. **Monitor performance impact** - Adjust settings if needed
4. **Review and update CSP** - As application evolves

### 10. Troubleshooting Security Issues

#### Common Problems & Solutions

**Obfuscation Failures**:
```bash
# Check obfuscator dependencies
npm list javascript-obfuscator

# Rebuild with debug
NODE_ENV=production npm run build 2>&1 | tee build.log

# Verify plugin configuration
cat vite-plugins/obfuscator.js
```

**Environment Configuration Issues**:
```bash
# Verify environment
./security-audit.sh env

# Clear and rebuild caches
php artisan config:clear
php artisan config:cache
```

**Permission Problems**:
```bash
# Fix storage permissions
chmod -R 755 storage/
chmod 600 .env

# Verify web server permissions
ls -la storage/ public/
```

### 11. Security Documentation Maintenance

#### Regular Updates Required
- **Monthly**: Review and update security configurations
- **Quarterly**: Audit obfuscation effectiveness
- **Annually**: Complete security architecture review

#### Version Control
- **Security configs**: Track changes to security settings
- **Build scripts**: Version security and deployment scripts
- **Documentation**: Keep security docs current

---

## 📊 Security Implementation Status

### ✅ Completed
- Multi-layer client-side protection
- Advanced JavaScript obfuscation
- Automated security deployment
- Comprehensive security auditing
- AWS cloud security integration
- Server security headers
- Environment hardening

### 🔄 Ongoing
- Security monitoring and alerting
- Performance optimization
- Regular security audits
- Threat landscape adaptation

### 📋 Next Steps
1. Implement real-time security monitoring
2. Add automated vulnerability scanning
3. Enhance obfuscation techniques based on analysis
4. Develop security incident response procedures

---

**Security Level**: Enterprise Grade  
**Last Security Audit**: December 2024  
**Next Review**: January 2025  
**Maintained By**: Fotoku Security Team
