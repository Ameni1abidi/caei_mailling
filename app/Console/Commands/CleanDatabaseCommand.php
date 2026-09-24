<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanDatabaseCommand extends Command
{
    protected $signature = 'db:clean
                            {--dry-run : Afficher ce qui serait supprimé sans réellement supprimer}
                            {--days=90 : Conserver les logs des N derniers jours (défaut: 90)}';

    protected $description = 'Nettoyer la base de données pour éviter la saturation OVH (2 Go limit)';

    public function handle(): int
    {
        $dryRun  = $this->option('dry-run');
        $days    = (int) $this->option('days');
        $cutoff  = now()->subDays($days);
        $total   = 0;

        $this->info("🧹 Nettoyage de la base de données — conservation des {$days} derniers jours");
        $this->info($dryRun ? '⚠️  Mode DRY-RUN : aucune suppression réelle' : '🔴 Mode RÉEL : suppression en cours...');
        $this->newLine();

        // ── 1. failed_jobs ────────────────────────────────────────────────────
        $count = DB::table('failed_jobs')->count();
        $this->line("  failed_jobs       : {$count} ligne(s)");
        if (! $dryRun && $count > 0) {
            DB::table('failed_jobs')->delete();
            $this->info("  ✅ failed_jobs vidée ({$count} supprimés)");
        }
        $total += $count;

        // ── 2. jobs (queue) — garder seulement les récents (24h max) ─────────
        $jobsOld = DB::table('jobs')
            ->where('created_at', '<', now()->subHours(24)->timestamp)
            ->count();
        $this->line("  jobs (vieux >24h) : {$jobsOld} ligne(s)");
        if (! $dryRun && $jobsOld > 0) {
            DB::table('jobs')
                ->where('created_at', '<', now()->subHours(24)->timestamp)
                ->delete();
            $this->info("  ✅ jobs anciens supprimés ({$jobsOld})");
        }
        $total += $jobsOld;

        // ── 3. email_logs — garder les N derniers jours ───────────────────────
        $logsOld = DB::table('email_logs')
            ->where('created_at', '<', $cutoff)
            ->count();
        $this->line("  email_logs (>{$days}j): {$logsOld} ligne(s)");
        if (! $dryRun && $logsOld > 0) {
            // Supprimer par chunks pour ne pas bloquer la DB
            $deleted = 0;
            do {
                $chunk = DB::table('email_logs')
                    ->where('created_at', '<', $cutoff)
                    ->limit(2000)
                    ->delete();
                $deleted += $chunk;
            } while ($chunk > 0);
            $this->info("  ✅ email_logs anciens supprimés ({$deleted})");
        }
        $total += $logsOld;

        // ── 4. sessions expirées ──────────────────────────────────────────────
        if (DB::getSchemaBuilder()->hasTable('sessions')) {
            $sessionsOld = DB::table('sessions')
                ->where('last_activity', '<', now()->subDays(7)->timestamp)
                ->count();
            $this->line("  sessions (>7j)    : {$sessionsOld} ligne(s)");
            if (! $dryRun && $sessionsOld > 0) {
                DB::table('sessions')
                    ->where('last_activity', '<', now()->subDays(7)->timestamp)
                    ->delete();
                $this->info("  ✅ sessions expirées supprimées ({$sessionsOld})");
            }
            $total += $sessionsOld;
        }

        // ── 5. cache expirés ─────────────────────────────────────────────────
        if (DB::getSchemaBuilder()->hasTable('cache')) {
            $cacheOld = DB::table('cache')
                ->where('expiration', '<', now()->timestamp)
                ->count();
            $this->line("  cache expiré      : {$cacheOld} ligne(s)");
            if (! $dryRun && $cacheOld > 0) {
                DB::table('cache')
                    ->where('expiration', '<', now()->timestamp)
                    ->delete();
                $this->info("  ✅ cache expiré supprimé ({$cacheOld})");
            }
            $total += $cacheOld;
        }

        // ── 6. prospect_interactions > 180j ──────────────────────────────────
        if (DB::getSchemaBuilder()->hasTable('prospect_interactions')) {
            $interOld = DB::table('prospect_interactions')
                ->where('created_at', '<', now()->subDays(180))
                ->count();
            $this->line("  interactions(>6m) : {$interOld} ligne(s)");
            if (! $dryRun && $interOld > 0) {
                DB::table('prospect_interactions')
                    ->where('created_at', '<', now()->subDays(180))
                    ->delete();
                $this->info("  ✅ interactions anciennes supprimées ({$interOld})");
            }
            $total += $interOld;
        }

        // ── Résumé ────────────────────────────────────────────────────────────
        $this->newLine();
        $this->info("══════════════════════════════════════════");
        if ($dryRun) {
            $this->warn("  DRY-RUN : {$total} lignes seraient supprimées");
        } else {
            $this->info("  ✅ Nettoyage terminé : ~{$total} lignes traitées");
            Log::info("db:clean exécuté — {$total} lignes nettoyées (conservation {$days}j)");
        }
        $this->info("══════════════════════════════════════════");

        return Command::SUCCESS;
    }
}
