<?php
echo "--- Test de connectivité SMTP depuis le serveur OVH ---\n";

$host = 'ssl0.ovh.net';

// Test 1: Port 465
echo "1. Test connexion socket vers {$host}:465...\n";
$fp465 = @fsockopen("ssl://{$host}", 465, $errno, $errstr, 5);
if ($fp465) {
    echo "   -> Port 465 (SSL) : OUVERT et JOIGNABLE !\n";
    fclose($fp465);
} else {
    echo "   -> Port 465 (SSL) : ÉCHEC ($errno: $errstr)\n";
}

// Test 2: Port 587
echo "2. Test connexion socket vers {$host}:587...\n";
$fp587 = @fsockopen($host, 587, $errno, $errstr, 5);
if ($fp587) {
    echo "   -> Port 587 (TLS) : OUVERT et JOIGNABLE !\n";
    $banner = fgets($fp587, 512);
    echo "   -> Réponse du serveur : " . trim($banner) . "\n";
    fclose($fp587);
} else {
    echo "   -> Port 587 (TLS) : ÉCHEC ($errno: $errstr)\n";
}
