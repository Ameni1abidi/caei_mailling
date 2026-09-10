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

Route::get('/track/open/{log_id}', [App\Http\Controllers\TrackingController::class, 'open'])->name('track.open');
Route::get('/unsubscribe/{email}', [App\Http\Controllers\UnsubscribeController::class, 'unsubscribe'])->name('contact.unsubscribe');

// Route Cron sécurisée appelée par cron-job.org pour traiter la file d'attente automatiquement
Route::get('/cron/run', function (\Illuminate\Http\Request $request) {
    $token = env('CRON_TOKEN', 'caei-cron-secret-2026');
    if ($request->query('token') !== $token) {
        return response()->json(['error' => 'Non autorisé'], 403);
    }

    @set_time_limit(120);

    // 1. Déclencher les campagnes programmées
    \Illuminate\Support\Facades\Artisan::call('campaigns:dispatch-scheduled');

    // 2. Exécuter le scheduler Laravel (relances auto)
    \Illuminate\Support\Facades\Artisan::call('schedule:run');

    // 3. Traiter immédiatement la file d'attente
    \Illuminate\Support\Facades\Artisan::call('queue:work', [
        'connection' => 'database',
        '--queue' => 'emails,default',
        '--stop-when-empty' => true,
        '--max-jobs' => 50,
        '--tries' => 3,
        '--timeout' => 55,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Queue worker exécuté avec succès',
        'time' => now()->toDateTimeString(),
    ]);
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
    Route::post('contacts/import', [ContactController::class, 'import'])->name('contacts.import');
    Route::get('contacts/import-history', [ContactController::class, 'importHistory'])->name('contacts.import-history');
    Route::resource('contacts', ContactController::class);

    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/add-contacts', [CategoryController::class, 'addContacts'])->name('categories.addContacts');
    Route::delete('categories/{category}/remove-contact/{contact}', [CategoryController::class, 'removeContact'])->name('categories.removeContact');

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
    Route::resource('attachments', CampaignAttachmentController::class);
    Route::get('attachments/{attachment}/download', [CampaignAttachmentController::class, 'download'])->name('attachments.download');

    Route::middleware('role:admin')->group(function () {
        // MODULE 9 : Suivi des prospects (Admin)
        Route::get('prospects', [ProspectController::class, 'index'])->name('prospects.index');
        Route::get('prospects/{contact}', [ProspectController::class, 'show'])->name('prospects.show');
        Route::patch('prospects/{contact}/status', [ProspectController::class, 'updateStatus'])->name('prospects.update-status');
        Route::post('prospects/{contact}/notes', [ProspectController::class, 'addNote'])->name('prospects.add-note');
        Route::post('prospects/{contact}/followup', [ProspectController::class, 'scheduleFollowUp'])->name('prospects.schedule-followup');

        // Paramètres SMTP (Admin)
        Route::resource('smtp-settings', SmtpSettingController::class);
        Route::post('smtp-settings/{smtp_setting}/test', [SmtpSettingController::class, 'testConnection'])->name('smtp-settings.test');
        Route::post('smtp-settings/{smtp_setting}/activate', [SmtpSettingController::class, 'activate'])->name('smtp-settings.activate');

        // Personnalisation En-tête & Logo Email (Admin)
        Route::get('settings/email-header', [App\Http\Controllers\EmailHeaderSettingController::class, 'edit'])->name('settings.email-header');
        Route::post('settings/email-header', [App\Http\Controllers\EmailHeaderSettingController::class, 'update'])->name('settings.email-header.update');
        Route::post('settings/email-header/reset', [App\Http\Controllers\EmailHeaderSettingController::class, 'reset'])->name('settings.email-header.reset');

        Route::get('users/monitoring', [UserController::class, 'monitoring'])->name('users.monitoring');
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
