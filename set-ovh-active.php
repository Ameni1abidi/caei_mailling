<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SmtpSetting;

try {
    // Désactiver tous les anciens réglages
    SmtpSetting::where('is_active', true)->update(['is_active' => false]);

    // Créer ou mettre à jour la conf OVH
    $smtp = SmtpSetting::updateOrCreate(
        ['provider' => 'OVHcloud SMTP'],
        [
            'driver' => 'smtp',
            'host' => 'ssl0.ovh.net',
            'port' => 465,
            'username' => 'admin@caei-afri.com',
            'password' => 'AdminCaei2026',
            'encryption' => 'ssl',
            'sender_name' => 'Caei-Mailling',
            'sender_email' => 'admin@caei-afri.com',
            'rate_limit' => 100,
            'is_active' => true,
        ]
    );

    echo "SUCCÈS : Configuration OVHcloud SMTP activée avec succès en base de données !\n";
} catch (\Throwable $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}
