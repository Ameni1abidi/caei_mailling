<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailLog;
use App\Models\SmtpSetting;
use App\Mail\CampaignMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class SendCampaignEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public Campaign $campaign,
        public Contact $contact,
        public int $emailLogId
    ) {}

    public function handle(): void
    {
        $emailLog = EmailLog::find($this->emailLogId);
        if (!$emailLog) return;

        try {
            $smtp = SmtpSetting::where('is_active', true)->first();

            $mailable = new CampaignMail($this->campaign, $this->contact);

            if ($smtp) {
                $mailerName = 'dynamic_smtp';
                Config::set("mail.mailers.{$mailerName}", [
                    'transport'  => $smtp->driver ?? 'smtp',
                    'host'       => $smtp->host,
                    'port'       => $smtp->port,
                    'username'   => $smtp->username,
                    'password'   => $smtp->password,
                    'encryption' => $smtp->encryption,
                ]);

                if ($smtp->sender_email) {
                    $mailable->from($smtp->sender_email, $smtp->sender_name);
                }
                if ($smtp->reply_to_email) {
                    $mailable->replyTo($smtp->reply_to_email);
                }

                Mail::mailer($mailerName)->to($this->contact->email)->send($mailable);
            } else {
                Mail::to($this->contact->email)->send($mailable);
            }

            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Campaign::find($this->campaign->id)?->markAsSentIfAllEmailsAreSent();
        } catch (\Exception $e) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            Log::error("Échec envoi campagne #{$this->campaign->id} à {$this->contact->email} : " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        EmailLog::where('id', $this->emailLogId)->update(['status' => 'failed']);
    }
}
