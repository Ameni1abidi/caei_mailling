<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SmtpSetting;

try {
    // Désactiver tous les anciens réglages
    SmtpSetting::where('is_active', true)->update(['is_active' => false]);

    // Créer ou mettre à jour la conf Mailtrap
    $smtp = SmtpSetting::updateOrCreate(
        ['provider' => 'Mailtrap (Test)'],
        [
            'driver' => 'smtp',
            'host' => 'sandbox.smtp.mailtrap.io',
            'port' => 2525,
            'username' => 'dae04554408426',
            'password' => 'c82044258734f1',
            'encryption' => 'tls',
            'sender_name' => 'CAEI',
            'sender_email' => 'contact@caei-afri.com',
            'rate_limit' => 100,
            'is_active' => true,
        ]
    );

    echo "SUCCÈS : Configuration Mailtrap activée avec succès en base de données !\n";
} catch (\Throwable $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}
