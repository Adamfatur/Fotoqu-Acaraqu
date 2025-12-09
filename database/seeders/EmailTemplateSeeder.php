<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Frame Delivery - Default',
                'slug' => 'frame-delivery-default',
                'subject' => 'Frame Foto Anda Sudah Siap! - {{session_code}}',
                'content' => $this->getFrameDeliveryTemplate(),
                'type' => 'frame_delivery',
                'status' => 'active',
                'is_default' => true,
                'variables' => EmailTemplate::getAvailableVariables('frame_delivery'),
            ],
            [
                'name' => 'Session Confirmation - Default',
                'slug' => 'session-confirmation-default',
                'subject' => 'Konfirmasi Sesi Foto - {{session_code}}',
                'content' => $this->getSessionConfirmationTemplate(),
                'type' => 'session_confirmation',
                'status' => 'active',
                'is_default' => true,
                'variables' => EmailTemplate::getAvailableVariables('session_confirmation'),
            ],
            [
                'name' => 'Payment Receipt - Default',
                'slug' => 'payment-receipt-default',
                'subject' => 'Struk Pembayaran - {{transaction_id}}',
                'content' => $this->getPaymentReceiptTemplate(),
                'type' => 'payment_receipt',
                'status' => 'active',
                'is_default' => true,
                'variables' => EmailTemplate::getAvailableVariables('payment_receipt'),
            ],
            [
                'name' => 'Welcome Message - Default',
                'slug' => 'welcome-default',
                'subject' => 'Selamat Datang di {{company_name}}!',
                'content' => $this->getWelcomeTemplate(),
                'type' => 'welcome',
                'status' => 'active',
                'is_default' => true,
                'variables' => EmailTemplate::getAvailableVariables('welcome'),
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }

        $this->command->info('Email templates seeded successfully!');
    }

    private function getFrameDeliveryTemplate(): string
    {
        return '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frame Foto Siap</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%); color: white; border-radius: 10px 10px 0 0; margin: -20px -20px 20px -20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 20px 0; }
        .button { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%); color: white; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 20px 0; }
        .info-box { background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #3b82f6; }
        .footer { text-align: center; padding: 20px 0; color: #666; font-size: 12px; border-top: 1px solid #eee; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Frame Foto Anda Sudah Siap!</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{customer_name}}</strong>,</p>
            
            <p>Terima kasih telah menggunakan layanan photobox kami! Frame foto dari sesi <strong>{{session_code}}</strong> sudah selesai dibuat dan siap untuk diunduh.</p>
            
            <div class="info-box">
                <h3>📋 Detail Sesi:</h3>
                <ul>
                    <li><strong>Kode Sesi:</strong> {{session_code}}</li>
                    <li><strong>Paket:</strong> {{package_name}}</li>
                    <li><strong>Total Foto:</strong> {{total_photos}}</li>
                    <li><strong>Tanggal:</strong> {{date}}</li>
                    <li><strong>Photobox:</strong> {{photobox_name}}</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{frame_url}}" class="button">📱 Download Frame Foto</a>
            </div>
            
            <div class="info-box">
                <h3>⚠️ Penting:</h3>
                <ul>
                    <li>Link download akan aktif hingga <strong>{{frame_expires}}</strong></li>
                    <li>Frame sudah dalam ukuran A5 dan siap cetak</li>
                    <li>Simpan file di device Anda untuk backup</li>
                    <li>Jika ada kendala, hubungi {{support_email}}</li>
                </ul>
            </div>
            
            <p>Terima kasih sudah mempercayai {{company_name}} untuk mengabadikan momen spesial Anda! 📸✨</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim otomatis dari {{company_name}}<br>
            Jika ada pertanyaan, hubungi kami di {{support_email}}</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getSessionConfirmationTemplate(): string
    {
        return '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Sesi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%); color: white; border-radius: 10px 10px 0 0; margin: -20px -20px 20px -20px; }
        .info-box { background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #10b981; }
        .footer { text-align: center; padding: 20px 0; color: #666; font-size: 12px; border-top: 1px solid #eee; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Sesi Foto Terkonfirmasi</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{customer_name}}</strong>,</p>
            
            <p>Sesi foto Anda telah dikonfirmasi dan pembayaran berhasil diproses!</p>
            
            <div class="info-box">
                <h3>📋 Detail Sesi:</h3>
                <ul>
                    <li><strong>Kode Sesi:</strong> {{session_code}}</li>
                    <li><strong>Paket:</strong> {{package_name}}</li>
                    <li><strong>Total Pembayaran:</strong> {{total_amount}}</li>
                    <li><strong>Metode Pembayaran:</strong> {{payment_method}}</li>
                    <li><strong>Tanggal & Waktu:</strong> {{date}} {{time}}</li>
                </ul>
            </div>
            
            <p>Silakan menuju ke photobox <strong>{{photobox_name}}</strong> untuk memulai sesi foto Anda. Frame foto akan dikirim ke email ini setelah selesai.</p>
            
            <p>Terima kasih telah memilih {{company_name}}! 📸</p>
        </div>
        
        <div class="footer">
            <p>{{company_name}}<br>{{support_email}}</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getPaymentReceiptTemplate(): string
    {
        return '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%); color: white; border-radius: 10px 10px 0 0; margin: -20px -20px 20px -20px; }
        .receipt-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border: 2px dashed #10b981; }
        .footer { text-align: center; padding: 20px 0; color: #666; font-size: 12px; border-top: 1px solid #eee; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧾 Struk Pembayaran</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{customer_name}}</strong>,</p>
            
            <p>Pembayaran Anda telah berhasil diproses. Berikut adalah struk pembayaran untuk transaksi Anda:</p>
            
            <div class="receipt-box">
                <h3 style="text-align: center; color: #10b981; margin-top: 0;">💳 STRUK PEMBAYARAN</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td><strong>ID Transaksi:</strong></td><td>{{transaction_id}}</td></tr>
                    <tr><td><strong>Tanggal:</strong></td><td>{{payment_date}}</td></tr>
                    <tr><td><strong>Nama:</strong></td><td>{{customer_name}}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>{{customer_email}}</td></tr>
                    <tr><td style="padding-top: 10px;"><strong>Metode Pembayaran:</strong></td><td style="padding-top: 10px;">{{payment_method}}</td></tr>
                    <tr style="border-top: 1px solid #ddd;"><td style="padding-top: 10px;"><strong>TOTAL:</strong></td><td style="padding-top: 10px; font-size: 18px; font-weight: bold; color: #10b981;">{{total_amount}}</td></tr>
                </table>
            </div>
            
            <p>Simpan email ini sebagai bukti pembayaran yang sah. Jika ada pertanyaan, hubungi kami di {{support_email}}.</p>
            
            <p>Terima kasih atas kepercayaan Anda kepada {{company_name}}!</p>
        </div>
        
        <div class="footer">
            <p>{{company_name}}<br>{{support_email}}</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getWelcomeTemplate(): string
    {
        return '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%); color: white; border-radius: 10px 10px 0 0; margin: -20px -20px 20px -20px; }
        .welcome-box { background-color: #f0fdf4; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981; }
        .footer { text-align: center; padding: 20px 0; color: #666; font-size: 12px; border-top: 1px solid #eee; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Selamat Datang!</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{customer_name}}</strong>,</p>
            
            <p>Selamat datang di {{company_name}}! Kami senang Anda bergabung dengan kami untuk mengabadikan momen spesial.</p>
            
            <div class="welcome-box">
                <h3>📸 Cara Menggunakan Photobox:</h3>
                <ol>
                    <li>Tunggu konfirmasi dari admin</li>
                    <li>Datang ke photobox yang sudah ditentukan</li>
                    <li>Ikuti instruksi di layar</li>
                    <li>Berpose dan tersenyum! 😊</li>
                    <li>Frame foto akan dikirim ke email Anda</li>
                </ol>
            </div>
            
            <p>{{instructions}}</p>
            
            <p>Jika ada pertanyaan, jangan ragu untuk menghubungi kami di {{support_email}}.</p>
            
            <p>Selamat berfoto dan semoga mendapatkan hasil yang memuaskan! 📸✨</p>
        </div>
        
        <div class="footer">
            <p>{{company_name}}<br>{{website_url}}<br>{{support_email}}</p>
        </div>
    </div>
</body>
</html>';
    }
}
