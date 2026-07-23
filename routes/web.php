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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/track/open/{log_id}', [App\Http\Controllers\TrackingController::class, 'open'])->name('track.open');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('contacts', ContactController::class);
    Route::post('contacts/import', [ContactController::class, 'import'])->name('contacts.import');

    // MODULE 9 : Suivi des prospects
    Route::get('prospects', [ProspectController::class, 'index'])->name('prospects.index');
    Route::get('prospects/{contact}', [ProspectController::class, 'show'])->name('prospects.show');
    Route::patch('prospects/{contact}/status', [ProspectController::class, 'updateStatus'])->name('prospects.update-status');
    Route::post('prospects/{contact}/notes', [ProspectController::class, 'addNote'])->name('prospects.add-note');
    Route::post('prospects/{contact}/followup', [ProspectController::class, 'scheduleFollowUp'])->name('prospects.schedule-followup');

    Route::resource('campaigns', CampaignController::class)->except(['show']);
    Route::get('campaigns/{campaign}/preview', [CampaignController::class, 'preview'])->name('campaigns.preview');

    Route::post('campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');

    // Pièces jointes / Fichiers
    Route::resource('attachments', CampaignAttachmentController::class);
    Route::get('attachments/{attachment}/download', [CampaignAttachmentController::class, 'download'])->name('attachments.download');

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);

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

    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/add-contacts', [CategoryController::class, 'addContacts'])->name('categories.addContacts');
    Route::delete('categories/{category}/remove-contact/{contact}', [CategoryController::class, 'removeContact'])->name('categories.removeContact');

    // Paramètres SMTP
    Route::resource('smtp-settings', SmtpSettingController::class);
    Route::post('smtp-settings/{smtp_setting}/test', [SmtpSettingController::class, 'testConnection'])->name('smtp-settings.test');
    Route::post('smtp-settings/{smtp_setting}/activate', [SmtpSettingController::class, 'activate'])->name('smtp-settings.activate');
});


require __DIR__.'/auth.php';
