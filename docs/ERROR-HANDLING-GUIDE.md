# Error Handling Documentation - Fotoku

## 📋 Overview
Fotoku menggunakan sistem error handling yang komprehensif untuk memastikan pengalaman pengguna yang baik bahkan saat terjadi error. Setiap error akan menampilkan halaman yang informatif dan user-friendly dengan desain yang konsisten dengan tema Fotoku.

## 🎨 Error Pages
Semua halaman error menggunakan layout yang konsisten dengan tema pastel Fotoku dan memberikan informasi yang berguna kepada pengguna.

### Halaman Error Yang Tersedia:

#### 1. **404 - Halaman Tidak Ditemukan**
- **File:** `resources/views/errors/404.blade.php`
- **Kapan muncul:** URL tidak ditemukan
- **Fitur:** 
  - Saran halaman yang mungkin dicari user
  - Link ke dashboard admin, photoboxes, dan sesi foto
  - Tombol navigasi yang berguna

#### 2. **403 - Akses Ditolak**
- **File:** `resources/views/errors/403.blade.php`
- **Kapan muncul:** User tidak memiliki permission
- **Fitur:**
  - Informasi user yang sedang login
  - Saran untuk login atau hubungi admin
  - Panduan troubleshooting

#### 3. **419 - Sesi Telah Berakhir**
- **File:** `resources/views/errors/419.blade.php`
- **Kapan muncul:** CSRF token expired
- **Fitur:**
  - Penjelasan mengapa sesi berakhir
  - Tombol refresh yang mudah diakses
  - Informasi keamanan sesi

#### 4. **500 - Kesalahan Server**
- **File:** `resources/views/errors/500.blade.php`
- **Kapan muncul:** Internal server error
- **Fitur:**
  - Pesan yang menenangkan untuk user
  - Informasi debug (hanya di development)
  - Panduan apa yang harus dilakukan user

#### 5. **503 - Layanan Tidak Tersedia**
- **File:** `resources/views/errors/503.blade.php`
- **Kapan muncul:** Maintenance mode atau service unavailable
- **Fitur:**
  - Informasi maintenance yang sedang berlangsung
  - Estimasi waktu selesai
  - Auto-refresh setiap 30 detik

#### 6. **Error Umum**
- **File:** `resources/views/errors/error.blade.php`
- **Kapan muncul:** Error lain yang tidak terdefinisi
- **Fitur:**
  - Debug information (development mode)
  - System information
  - Comprehensive troubleshooting guide

## 🔧 Technical Implementation

### Exception Handler
- **File:** `app/Exceptions/Handler.php`
- **Fitur:**
  - Custom error handling untuk berbagai jenis exception
  - JSON response untuk AJAX requests
  - Detailed logging untuk debugging
  - User-friendly messages

### Error Handling Middleware
- **File:** `app/Http/Middleware/ErrorHandlingMiddleware.php`
- **Fitur:**
  - Logging semua admin actions untuk audit
  - Context-rich error logging
  - Request information capture

### Configuration
- **File:** `bootstrap/app.php`
- **Setup:**
  - Exception handlers terdaftar
  - Middleware error handling aktif
  - Custom error responses untuk API

## 🚀 Features

### 1. **Design Consistency**
- Menggunakan tema pastel Fotoku
- Responsive design untuk semua device
- Konsisten dengan branding aplikasi
- Floating animations dan visual effects

### 2. **User Experience**
- Pesan yang mudah dipahami dalam Bahasa Indonesia
- Tombol navigasi yang jelas dan berguna
- Saran actionable untuk mengatasi masalah
- Informasi sistem yang relevan

### 3. **Developer Experience**
- Debug information di development mode
- Detailed error logging dengan context
- Easy testing dengan route khusus development
- Comprehensive error tracking

### 4. **Security**
- Tidak membocorkan informasi sensitif di production
- Logging yang aman untuk audit trail
- CSRF protection dengan error handling yang baik
- Authentication error handling yang proper

## 🧪 Testing Error Pages (Development Only)

Untuk testing, gunakan route berikut (hanya tersedia di development mode):

```
/test-errors/404   - Test 404 error
/test-errors/403   - Test 403 error
/test-errors/419   - Test CSRF error
/test-errors/500   - Test server error
/test-errors/503   - Test service unavailable
/test-errors/database - Test database error
/test-errors/auth  - Test authentication error
```

## 🔧 Troubleshooting CSRF Errors (419)

### Common CSRF Token Issues:
1. **Missing meta tag**: Pastikan `<meta name="csrf-token" content="{{ csrf_token() }}">` ada di `<head>`
2. **Missing axios headers**: AJAX requests harus include `X-CSRF-TOKEN` header
3. **Session expired**: Token sudah tidak valid karena session berakhir
4. **Cache issues**: Clear browser cache atau Laravel cache

### CSRF Error Handling Pattern:
```javascript
// Proper CSRF handling untuk DELETE/POST requests
async function deleteItem(itemId) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('CSRF token not found. Please refresh the page.');
        }

        const response = await axios.delete(`/admin/items/${itemId}`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        // Handle success
    } catch (error) {
        if (error.response && error.response.status === 419) {
            alert('Sesi telah berakhir. Halaman akan di-refresh otomatis.');
            setTimeout(() => window.location.reload(), 2000);
        }
        // Handle other errors
    }
}
```

### Fixed CSRF Issues:
- ✅ **Frame Templates Delete**: Fixed missing CSRF token in DELETE request
- ✅ **Axios Default Headers**: Configured globally in admin layout
- ✅ **Error 419 Handling**: Auto-refresh page on session expiry
- ✅ **User-Friendly Messages**: Clear instructions for users

## 📊 Error Monitoring

### Logging
Semua error dicatat dengan informasi:
- URL dan method request
- User information (jika login)
- IP address dan user agent
- Error details (message, file, line)
- Context data

### Audit Trail
Admin actions dicatat untuk:
- Security monitoring
- Troubleshooting
- Performance analysis
- User behavior tracking

## 🎯 Best Practices

1. **Always provide actionable solutions** - Setiap error page memberikan langkah yang bisa diambil user
2. **Maintain consistent branding** - Semua error page mengikuti design system Fotoku
3. **Progressive information disclosure** - Debug info hanya di development, user-friendly di production
4. **Comprehensive logging** - Semua error dicatat dengan context yang cukup untuk debugging
5. **Security-first approach** - Tidak membocorkan informasi sensitif di production

## 🔄 Maintenance

### Regular Checks
- Monitor error logs untuk pattern atau masalah berulang
- Update error messages berdasarkan feedback user
- Test error pages secara berkala
- Review dan update troubleshooting guides

### Performance
- Error pages di-cache untuk performa optimal
- Minimal dependencies untuk reliability
- Fast loading bahkan saat server bermasalah

---

**Status:** ✅ **Implemented & Ready for Production**

Semua error pages telah diimplementasi dengan design yang konsisten, user experience yang baik, dan technical implementation yang solid. User akan selalu mendapatkan guidance yang berguna saat mengalami error.
