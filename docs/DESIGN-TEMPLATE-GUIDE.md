# 📐 Fotoku Design Template Guide

Panduan lengkap untuk desainer dalam membuat template frame photobox dengan ukuran yang akurat.

## 🎯 Spesifikasi Template

### Ukuran Canvas
- **Format**: A5 (148 × 210 mm)
- **Resolusi**: 300 DPI
- **Ukuran Pixel**: 1748 × 2480 pixels
- **Color Mode**: RGB (untuk tampilan digital) atau CMYK (untuk cetak)
- **Background**: Putih (#FFFFFF)

### Brand Colors Fotoku
```css
Navy Blue: #1e3a8a
Green: #166534
Light Blue: #dbeafe
Light Green: #dcfce7
White: #ffffff
Gray: #f3f4f6
```

## 📏 Layout Specifications

### Frame 4 Slot (2×2)
```
Canvas: 1748 × 2480 px
Margin: 60 px dari semua sisi
Spacing antar foto: 60 px
Ukuran setiap foto: 784 × 1150 px
Area branding: 200 px dari bawah
```

### Frame 6 Slot (2×3)
```
Canvas: 1748 × 2480 px
Margin: 50 px dari semua sisi
Spacing antar foto: 50 px
Ukuran setiap foto: 799 × 753 px
Area branding: 180 px dari bawah
```

### Frame 8 Slot (2×4)
```
Canvas: 1748 × 2480 px
Margin: 40 px dari semua sisi
Spacing antar foto: 40 px
Ukuran setiap foto: 814 × 570 px
Area branding: 160 px dari bawah
```

## 🛠️ Cara Download Template

1. Masuk ke Admin Dashboard → Template Frame
2. Klik tombol hijau **"Download Template"**
3. Pilih template sesuai jumlah slot yang diinginkan:
   - **4 Slot** - Layout 2×2
   - **6 Slot** - Layout 2×3  
   - **8 Slot** - Layout 2×4
4. File SVG akan terdownload otomatis

## 📂 Struktur Layer yang Disarankan

### Layer Organization (Photoshop)
```
📁 FOTOKU FRAME TEMPLATE
├── 📁 PHOTOS
│   ├── 🖼️ Photo 1 (Smart Object)
│   ├── 🖼️ Photo 2 (Smart Object)
│   ├── 🖼️ Photo 3 (Smart Object)
│   └── 🖼️ Photo 4/6/8 (Smart Object)
├── 📁 BRANDING
│   ├── 🎨 Logo Fotoku
│   ├── 📝 Customer Info
│   └── 📱 QR Code
├── 📁 DECORATIVE
│   ├── 🎨 Border/Frame
│   ├── ✨ Ornaments
│   └── 🌟 Effects
└── 🎨 Background
```

## 🎨 Design Guidelines

### 1. Photo Areas
- **Gunakan Smart Objects** untuk penempatan foto
- **Aspect Ratio**: Sesuaikan dengan ukuran yang sudah ditentukan
- **Border**: 2-3px rounded corners (opsional)
- **Shadow**: Subtle drop shadow untuk depth

### 2. Branding Area (WAJIB)
- **Logo Fotoku**: Wajib ada di setiap template
- **Customer Info**: Nama, tanggal, event (opsional)
- **QR Code**: Akses ke galeri digital (opsional)
- **Typography**: Inter font family
- **Posisi**: Flexible, bisa di mana saja asal terlihat jelas
- **PENTING**: Sistem tidak akan menambahkan watermark otomatis

### 3. Decorative Elements
- **Style**: Pastel, minimalist, friendly
- **Colors**: Gunakan brand colors
- **Avoid**: Gradient yang kompleks, terlalu banyak ornamen

## 📋 Workflow Desain

### Step 1: Setup Canvas
1. Buat dokumen baru di Photoshop
2. Ukuran: 148 × 210 mm @ 300 DPI
3. Background: White
4. Setup guides sesuai margin

### Step 2: Import Template
1. Download SVG template dari admin
2. Import ke Photoshop (File → Place)
3. Gunakan sebagai panduan layout

### Step 3: Create Photo Areas
1. Buat rectangle sesuai ukuran yang ditentukan
2. Convert ke Smart Object
3. Naming: "Photo 1", "Photo 2", dst.

### Step 4: Add Branding
1. Place logo Fotoku
2. Add text areas untuk customer info
3. Reserve space untuk QR code

### Step 5: Styling
1. Add borders, shadows, ornaments
2. Gunakan brand colors
3. Keep it simple dan clean

### Step 6: Save Template
1. Save sebagai PSD dengan layer terpisah
2. Export preview sebagai JPG/PNG
3. Test dengan foto sample

## 📤 Upload ke Sistem

### File Requirements
- **Template File**: PSD format, max 10MB
- **Preview Image**: JPG/PNG, max 5MB
- **Naming**: Deskriptif (contoh: "Elegant Wedding 4 Slots")

### Upload Process
1. Admin Dashboard → Template Frame → Tambah Template
2. Fill form dengan informasi template
3. Upload PSD file dan preview image
4. Set status: Active/Inactive
5. Save template

## ✅ Quality Checklist

### Before Upload
- [ ] Ukuran canvas sudah benar (1748×2480px)
- [ ] Margin dan spacing sesuai spesifikasi
- [ ] Photo areas menggunakan Smart Objects
- [ ] Layer terorganisir dengan baik
- [ ] Brand colors digunakan dengan konsisten
- [ ] Preview image menunjukkan hasil akhir
- [ ] File size dalam batas yang ditentukan

### Testing
- [ ] Test dengan foto sample
- [ ] Check di berbagai device (desktop, tablet)
- [ ] Pastikan bisa dicetak dengan kualitas baik
- [ ] Verify branding area readable

## 🚀 Tips & Best Practices

### Performance
- **Optimize Images**: Compress decorative elements
- **Smart Objects**: Gunakan untuk foto placeholder
- **File Size**: Keep PSD under 10MB

### Design
- **Consistency**: Gunakan style guide yang sama
- **Hierarchy**: Photo adalah focal point
- **Balance**: Jangan overcrowd dengan ornamen

### Technical
- **Bleed Area**: 3mm untuk print safety
- **Safe Area**: 5mm dari edge untuk important elements
- **Resolution**: Minimal 300 DPI untuk print quality

## 📞 Support

Jika ada pertanyaan atau butuh bantuan:
- **Technical Issues**: Contact developer
- **Design Guidelines**: Refer to brand manual
- **Template Issues**: Check with admin

---

**© 2025 Fotoku - Aplikasi Photobox Otomatis**

Template guide ini dibuat untuk memastikan konsistensi dan kualitas semua frame template di sistem Fotoku.
