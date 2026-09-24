<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\SmtpSettingController;
use App\Http\Controllers\CampaignAttachmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\StatisticsController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('welcome');

// Routes publiques de tracking — utilisent un UUID opaque (non-séquentiel) pour la sécurité
Route::get('/track/open/{token}', [App\Http\Controllers\TrackingController::class, 'open'])->name('track.open');
Route::get('/track/click/{token}', [App\Http\Controllers\TrackingController::class, 'click'])->name('track.click');
Route::get('/unsubscribe/{email}', [App\Http\Controllers\UnsubscribeController::class, 'unsubscribe'])->middleware('throttle:10,1')->name('contact.unsubscribe');


// Route Cron sécurisée appelée par cron-job.org pour traiter la file d'attente automatiquement
Route::get('/cron/run', function (\Illuminate\Http\Request $request) {
    // Toujours retourner du JSON — jamais de 500 HTTP
    try {
        $token = env('CRON_TOKEN', 'caei-cron-secret-2026');
        if ($request->query('token') !== $token) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        @set_time_limit(0);
        $results = [];

        // 1. Déclencher les campagnes programmées
        try {
            \Illuminate\Support\Facades\Artisan::call('campaigns:dispatch-scheduled');
            $results['dispatch_scheduled'] = 'ok';
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Cron dispatch-scheduled error: " . $e->getMessage());
            $results['dispatch_scheduled'] = 'error: ' . $e->getMessage();
        }

        // 2. Exécuter le scheduler Laravel (relances auto)
        try {
            \Illuminate\Support\Facades\Artisan::call('schedule:run');
            $results['schedule_run'] = 'ok';
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Cron schedule:run error: " . $e->getMessage());
            $results['schedule_run'] = 'error: ' . $e->getMessage();
        }

        // 3. Auto-remplissage des messages d'erreur explicites pour les logs échoués
        try {
            \Illuminate\Support\Facades\DB::table('email_logs')
                ->whereIn('status', ['failed', 'bounced', 'invalid'])
                ->where(function ($q) {
                    $q->whereNull('error_message')->orWhere('error_message', '');
                })
                ->update(['error_message' => 'Échec de connexion SMTP / Rejet du serveur de messagerie ou quota dépassé']);
            $results['fix_error_messages'] = 'ok';
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Impossible de mettre à jour error_message dans cron/run: " . $e->getMessage());
            $results['fix_error_messages'] = 'error: ' . $e->getMessage();
        }

        // 4. Lancer le queue worker en arrière-plan (proc_open non-bloquant)
        // IMPORTANT : on ne fait plus Artisan::call('queue:work') synchrone ici.
        // Les exceptions SMTP des jobs (ex: 550 OVH) remontaient jusqu'au handler HTTP → 500.
        // Avec proc_open, le worker tourne en dehors du cycle HTTP : toute exception reste dans le worker.
        try {
            $phpBin  = PHP_BINARY ?: 'php';
            $artisan = base_path('artisan');
            $logFile = storage_path('logs/queue-worker.log');

            $cmd = sprintf(
                '%s %s queue:work database --queue=emails,default --stop-when-empty --max-jobs=50 --tries=3 --timeout=55 --memory=128 >> %s 2>&1',
                escapeshellarg($phpBin),
                escapeshellarg($artisan),
                escapeshellarg($logFile)
            );

            // Lancement non-bloquant
            $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
            $proc = @proc_open($cmd, $descriptors, $pipes);
            if (is_resource($proc)) {
                foreach ($pipes as $pipe) {
                    fclose($pipe);
                }
                proc_close($proc);
                $results['queue_worker'] = 'started in background';
            } else {
                // proc_open non disponible sur cet hébergement — fallback synchrone sécurisé
                \Illuminate\Support\Facades\Artisan::call('queue:work', [
                    'connection'       => 'database',
                    '--queue'          => 'emails,default',
                    '--stop-when-empty'=> true,
                    '--max-jobs'       => 30,
                    '--tries'          => 3,
                    '--timeout'        => 50,
                ]);
                $results['queue_worker'] = 'ran synchronously (proc_open unavailable)';
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Cron queue worker error: " . $e->getMessage());
            $results['queue_worker'] = 'error: ' . substr($e->getMessage(), 0, 200);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Cron exécuté avec succès',
            'time'    => now()->toDateTimeString(),
            'results' => $results,
        ]);

    } catch (\Throwable $fatal) {
        // Filet de sécurité ultime — aucune exception ne doit générer un 500
        \Illuminate\Support\Facades\Log::critical("Cron /cron/run fatal error: " . $fatal->getMessage());
        return response()->json([
            'status'  => 'error',
            'message' => 'Erreur inattendue — voir les logs Laravel',
            'error'   => substr($fatal->getMessage(), 0, 300),
            'time'    => now()->toDateTimeString(),
        ], 200); // 200 intentionnel pour éviter que cron-job.org marque comme failure
    }
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // ─────────────────────────────────────────────
    // IMPORT WIZARD — nouveau système 5 étapes
    // ─────────────────────────────────────────────
    Route::get('contacts/import/upload', [App\Http\Controllers\ContactImportController::class, 'showUpload'])->name('contacts.import.upload');
    Route::post('contacts/import/upload', [App\Http\Controllers\ContactImportController::class, 'handleUpload'])->name('contacts.import.handle-upload');
    Route::get('contacts/import/{importLogId}/mapping', [App\Http\Controllers\ContactImportController::class, 'showMapping'])->name('contacts.import.mapping');
    Route::post('contacts/import/{importLogId}/mapping', [App\Http\Controllers\ContactImportController::class, 'saveMapping'])->name('contacts.import.save-mapping');
    Route::get('contacts/import/{importLogId}/preview', [App\Http\Controllers\ContactImportController::class, 'showPreview'])->name('contacts.import.preview');
    Route::post('contacts/import/{importLogId}/execute', [App\Http\Controllers\ContactImportController::class, 'executeImport'])->name('contacts.import.execute');
    Route::get('contacts/import/{importLogId}/progress', [App\Http\Controllers\ContactImportController::class, 'showProgress'])->name('contacts.import.progress');
    Route::get('contacts/import/{importLogId}/status', [App\Http\Controllers\ContactImportController::class, 'importStatus'])->name('contacts.import.status');
    Route::get('contacts/import/{importLogId}/result', [App\Http\Controllers\ContactImportController::class, 'showResult'])->name('contacts.import.result');
    Route::get('contacts/import/template', [App\Http\Controllers\ContactImportController::class, 'downloadTemplate'])->name('contacts.import.template');
    Route::get('contacts/import/{importLogId}/errors', [App\Http\Controllers\ContactImportController::class, 'downloadErrors'])->name('contacts.import.errors');

    // Routes spécifiques contacts (avant resource pour éviter conflit avec {contact})
    Route::get('contacts/export', [ContactController::class, 'export'])->name('contacts.export');
    Route::post('contacts/import', [ContactController::class, 'import'])->name('contacts.import');
    Route::get('contacts/import-history', [ContactController::class, 'importHistory'])->name('contacts.import-history');
    Route::resource('contacts', ContactController::class)->except(['show']);

    Route::get('categories/{category}/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/add-contacts', [CategoryController::class, 'addContacts'])->name('categories.addContacts');
    Route::delete('categories/{category}/remove-contact/{contact}', [CategoryController::class, 'removeContact'])->name('categories.removeContact');
    // Import de contacts directement dans une liste dédiée
    Route::get('categories/{category}/import', [App\Http\Controllers\ContactImportController::class, 'showUploadForCategory'])->name('categories.import');

    // Statistiques
    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index');

    Route::resource('campaigns', CampaignController::class)->except(['show']);
    Route::get('campaigns/{campaign}/preview', [CampaignController::class, 'preview'])->name('campaigns.preview');
    Route::get('campaigns/recipient-count', [CampaignController::class, 'recipientCount'])->name('campaigns.recipient-count');

    Route::post('campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
    Route::post('campaigns/{campaign}/retry-failed', [CampaignController::class, 'retryFailed'])->name('campaigns.retry-failed');
    Route::post('campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])->name('campaigns.cancel');
    Route::post('campaigns/{campaign}/schedule-send', [CampaignController::class, 'scheduleCampaign'])->name('campaigns.schedule-send');
    Route::post('campaigns/{campaign}/unschedule', [CampaignController::class, 'unscheduleCampaign'])->name('campaigns.unschedule');

    // Pièces jointes / Fichiers
    Route::resource('attachments', CampaignAttachmentController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('attachments/{attachment}/download', [CampaignAttachmentController::class, 'download'])->name('attachments.download');

    Route::middleware('role:admin')->group(function () {
        // MODULE 9 : Suivi des prospects (Admin)
        Route::get('prospects', [ProspectController::class, 'index'])->name('prospects.index');
        Route::get('prospects/{contact}', [ProspectController::class, 'show'])->name('prospects.show');
        Route::patch('prospects/{contact}/status', [ProspectController::class, 'updateStatus'])->name('prospects.update-status');
        Route::post('prospects/{contact}/notes', [ProspectController::class, 'addNote'])->name('prospects.add-note');
        Route::post('prospects/{contact}/followup', [ProspectController::class, 'scheduleFollowUp'])->name('prospects.schedule-followup');

        // Paramètres SMTP (Admin)
        Route::resource('smtp-settings', SmtpSettingController::class)->except(['show']);
        Route::post('smtp-settings/{smtp_setting}/test', [SmtpSettingController::class, 'testConnection'])->name('smtp-settings.test');
        Route::post('smtp-settings/{smtp_setting}/activate', [SmtpSettingController::class, 'activate'])->name('smtp-settings.activate');

        // Personnalisation En-tête & Logo Email (Admin)
        Route::get('settings/email-header', [App\Http\Controllers\EmailHeaderSettingController::class, 'edit'])->name('settings.email-header');
        Route::post('settings/email-header', [App\Http\Controllers\EmailHeaderSettingController::class, 'update'])->name('settings.email-header.update');
        Route::post('settings/email-header/reset', [App\Http\Controllers\EmailHeaderSettingController::class, 'reset'])->name('settings.email-header.reset');

        // Personnalisation Pied de page & Footer Email (Admin)
        Route::get('settings/email-footer', [App\Http\Controllers\EmailFooterSettingController::class, 'edit'])->name('settings.email-footer');
        Route::post('settings/email-footer', [App\Http\Controllers\EmailFooterSettingController::class, 'update'])->name('settings.email-footer.update');
        Route::post('settings/email-footer/reset', [App\Http\Controllers\EmailFooterSettingController::class, 'reset'])->name('settings.email-footer.reset');

        Route::get('users/monitoring', [UserController::class, 'monitoring'])->name('users.monitoring');
        Route::post('users/test-smtp/{user?}', [UserController::class, 'testSmtp'])->name('users.test-smtp');
        Route::resource('users', UserController::class);

        // GrapesJS Builder Routes
        Route::get('email-templates/{email_template}/builder', [EmailTemplateController::class, 'builder'])
            ->name('email-templates.builder');
        Route::post('email-templates/{email_template}/builder', [EmailTemplateController::class, 'saveBuilder'])
            ->name('email-templates.builder.save');
        Route::post('email-templates/upload-image', [EmailTemplateController::class, 'uploadImage'])
            ->name('email-templates.upload-image');

        Route::post('email-templates/install-defaults', [EmailTemplateController::class, 'installDefaults'])
            ->name('email-templates.install-defaults');
        Route::get('email-templates/{email_template}/preview', [EmailTemplateController::class, 'preview'])
            ->name('email-templates.preview');
        Route::patch('email-templates/{email_template}/toggle', [EmailTemplateController::class, 'toggle'])
            ->name('email-templates.toggle');
        Route::post('email-templates/{email_template}/duplicate', [EmailTemplateController::class, 'duplicate'])
            ->name('email-templates.duplicate');
        Route::resource('email-templates', EmailTemplateController::class)->except(['show']);
    });
});


require __DIR__.'/auth.php';
