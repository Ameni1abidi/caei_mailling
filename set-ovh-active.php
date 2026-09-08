<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SmtpSetting;

try {
    // Désactiver tous les anciens réglages
    SmtpSetting::where('is_active', true)->update(['is_active' => false]);

    // Mettre à jour la configuration OVH active avec l'adresse contact@caei-afri.com
    // Note: Sur l'hébergement mutualisé OVH, le port 587 (TLS) est le port autorisé.
    $smtp = SmtpSetting::updateOrCreate(
        ['provider' => 'OVHcloud SMTP'],
        [
            'driver' => 'smtp',
            'host' => 'ssl0.ovh.net',
            'port' => 587,
            'username' => 'Contact@caei-afri.com',
            'password' => 'contact47cadIdatu5re@rec',
            'encryption' => 'tls',
            'sender_name' => 'Caei-Mailling',
            'sender_email' => 'contact@caei-afri.com',
            'rate_limit' => 60,
            'is_active' => true,
        ]
    );

    echo "SUCCÈS : Configuration OVH Contact@caei-afri.com (port 587 - TLS) activée avec succès en base de données !\n";
} catch (\Throwable $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}
