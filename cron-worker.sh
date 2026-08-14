#!/bin/bash
# Script de déclenchement du scheduler et queue worker Laravel pour OVH Cron

# Se placer dans le répertoire du projet
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$DIR"

# Détecter PHP ou utiliser le PHP configuré
PHP_BIN=$(which php 2>/dev/null || echo "/usr/local/php8.2/bin/php")

# Option 1: Exécuter le scheduler Laravel (qui gère la queue et les retries)
$PHP_BIN artisan schedule:run >> storage/logs/cron.log 2>&1

# Option 2: Exécuter directement le worker jusqu'à épuisement de la file
$PHP_BIN artisan queue:work --queue=emails,default --stop-when-empty --tries=3 --timeout=60 >> storage/logs/worker.log 2>&1
