<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settingGroups = Setting::orderBy('group')->orderBy('label')->get()->groupBy('group');
        
        return view('admin.settings.index', compact('settingGroups'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable'
        ]);

        foreach ($request->settings as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }

    public function reset()
    {
        // Reset to default settings
        $this->createDefaultSettings();
        
        return back()->with('success', 'Pengaturan berhasil direset ke default!');
    }

    public function createDefaultSettings()
    {
        $defaultSettings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Fotoku',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama aplikasi yang ditampilkan di header dan email'
            ],
            [
                'key' => 'app_description',
                'value' => 'Sistem Photobox Otomatis',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Deskripsi Aplikasi',
                'description' => 'Deskripsi singkat aplikasi'
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Timezone',
                'description' => 'Zona waktu untuk aplikasi'
            ],
            
            // Photo Settings
            [
                'key' => 'photo_count',
                'value' => '10',
                'type' => 'integer',
                'group' => 'photo',
                'label' => 'Jumlah Foto per Sesi',
                'description' => 'Berapa banyak foto yang diambil otomatis'
            ],
            [
                'key' => 'photo_interval',
                'value' => '3',
                'type' => 'integer',
                'group' => 'photo',
                'label' => 'Interval Foto (detik)',
                'description' => 'Jeda waktu antar pengambilan foto'
            ],
            [
                'key' => 'countdown_duration',
                'value' => '5',
                'type' => 'integer',
                'group' => 'photo',
                'label' => 'Durasi Countdown (detik)',
                'description' => 'Countdown sebelum mulai foto'
            ],
            [
                'key' => 'frame_quality',
                'value' => '300',
                'type' => 'integer',
                'group' => 'photo',
                'label' => 'Kualitas Frame (DPI)',
                'description' => 'Resolusi frame yang dihasilkan'
            ],
            
            // Email Settings
            [
                'key' => 'email_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Aktifkan Email',
                'description' => 'Kirim frame otomatis via email'
            ],
            [
                'key' => 'email_from_name',
                'value' => 'Fotoku Team',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Nama Pengirim Email',
                'description' => 'Nama yang muncul sebagai pengirim email'
            ],
            [
                'key' => 'email_subject',
                'value' => 'Frame Foto Anda Sudah Siap!',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Subject Email',
                'description' => 'Subject email untuk pengiriman frame'
            ],
            
            // Payment Settings
            [
                'key' => 'accept_cash',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Terima Pembayaran Tunai',
                'description' => 'Menerima pembayaran tunai'
            ],
            [
                'key' => 'accept_qris',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Terima Pembayaran QRIS',
                'description' => 'Menerima pembayaran via QRIS'
            ],
            [
                'key' => 'accept_card',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Terima Pembayaran Kartu',
                'description' => 'Menerima pembayaran via EDC/kartu'
            ],
            
            // System Settings
            [
                'key' => 'auto_approve',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'system',
                'label' => 'Auto Approve Sesi',
                'description' => 'Otomatis approve sesi setelah pembayaran'
            ],
            [
                'key' => 'session_timeout',
                'value' => '30',
                'type' => 'integer',
                'group' => 'system',
                'label' => 'Timeout Sesi (menit)',
                'description' => 'Batas waktu sesi tidak aktif'
            ],
            [
                'key' => 'max_daily_sessions',
                'value' => '100',
                'type' => 'integer',
                'group' => 'system',
                'label' => 'Maksimal Sesi per Hari',
                'description' => 'Batas maksimal sesi foto per hari'
            ]
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
