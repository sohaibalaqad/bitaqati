<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order matters — each seeder may depend on records created by the previous one.
     *
     * 0. SuperAdmin       — platform owner (no tenant), reads from .env
     * 1. Users            — network_admin + clients
     * 2. Packages         — internet packages (no dependencies)
     * 3. Cards            — available inventory + sold cards (needs users + packages)
     * 4. Invoices         — purchase records       (needs users + packages + cards)
     * 5. Transactions     — balance history         (needs users)
     * 6. RechargeRequests — top-up requests         (needs users)
     * 7. Tickets          — support tickets         (needs users)
     */
    public function run(): void
    {
        // Resolve the default tenant and load it into TenantContext.
        // This makes the BelongsToTenant creating-hook auto-assign tenant_id
        // on every ::create() call in the seeders below.
        // ::insert() calls bypass Eloquent events, so each seeder adds
        // tenant_id to those rows explicitly via app(TenantContext::class)->id().
        $subdomain = env('DEFAULT_TENANT_SUBDOMAIN', 'default');
        $tenant    = \App\Models\Tenant::withoutTenantScope()
                         ->where('subdomain', $subdomain)
                         ->first();

        if (! $tenant) {
            $this->command->error("Default tenant '{$subdomain}' not found. Run migrations first (php artisan migrate).");
            return;
        }

        app(\App\Services\TenantContext::class)->set($tenant);

        $this->call([
            SuperAdminSeeder::class,
            UserSeeder::class,
            PackageSeeder::class,
            CardSeeder::class,
            InvoiceSeeder::class,
            TransactionSeeder::class,
            RechargeRequestSeeder::class,
            TicketSeeder::class,
        ]);

        $this->printCredentials();
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function printCredentials(): void
    {
        $line  = str_repeat('─', 62);
        $dline = str_repeat('═', 62);

        $this->command->newLine();
        $this->command->line("  <fg=cyan;options=bold>╔{$dline}╗</>");
        $this->command->line("  <fg=cyan;options=bold>║" . $this->center('🔐  بيانات الدخول', 62) . "║</>");
        $this->command->line("  <fg=cyan;options=bold>╚{$dline}╝</>");
        $this->command->newLine();

        // ── Super Admin ──────────────────────────────────────────────────
        $superEmail    = env('SUPER_ADMIN_EMAIL',    'superadmin@platform.com');
        $superPassword = env('SUPER_ADMIN_PASSWORD', 'changeme123');
        $superName     = env('SUPER_ADMIN_NAME',     'Super Admin');

        $this->command->line("  <fg=yellow;options=bold>┌{$line}┐</>");
        $this->command->line("  <fg=yellow;options=bold>│</> <fg=yellow;options=bold>" . $this->pad('👑  Super Admin  (مدير المنصة)', 60) . "</><fg=yellow;options=bold>│</>");
        $this->command->line("  <fg=yellow;options=bold>├{$line}┤</>");
        $this->command->line("  <fg=yellow>│</>  الاسم      : <options=bold>{$superName}</>  " . str_repeat(' ', max(0, 43 - mb_strlen($superName))) . "<fg=yellow>│</>");
        $this->command->line("  <fg=yellow>│</>  البريد     : <options=bold>{$superEmail}</>  " . str_repeat(' ', max(0, 43 - mb_strlen($superEmail))) . "<fg=yellow>│</>");
        $this->command->line("  <fg=yellow>│</>  كلمة المرور: <options=bold>{$superPassword}</>  " . str_repeat(' ', max(0, 43 - mb_strlen($superPassword))) . "<fg=yellow>│</>");
        $this->command->line("  <fg=yellow>│</>  الرابط     : <options=bold>/superadmin/login</>  " . str_repeat(' ', 25) . "<fg=yellow>│</>");
        $this->command->line("  <fg=yellow;options=bold>└{$line}┘</>");
        $this->command->newLine();

        // ── Network Admin ────────────────────────────────────────────────
        $admin = DB::table('users')->where('role', 'network_admin')->first();

        $this->command->line("  <fg=green;options=bold>┌{$line}┐</>");
        $this->command->line("  <fg=green;options=bold>│</> <fg=green;options=bold>" . $this->pad('🛠️   مدير الشبكة  (Network Admin)', 60) . "</><fg=green;options=bold>│</>");
        $this->command->line("  <fg=green;options=bold>├{$line}┤</>");
        if ($admin) {
            $this->command->line("  <fg=green>│</>  الاسم      : <options=bold>{$admin->name}</>  " . str_repeat(' ', max(0, 43 - mb_strlen($admin->name))) . "<fg=green>│</>");
            $this->command->line("  <fg=green>│</>  البريد     : <options=bold>{$admin->email}</>  " . str_repeat(' ', max(0, 43 - mb_strlen($admin->email))) . "<fg=green>│</>");
            $this->command->line("  <fg=green>│</>  كلمة المرور: <options=bold>admin</>  " . str_repeat(' ', 36) . "<fg=green>│</>");
            $this->command->line("  <fg=green>│</>  الرابط     : <options=bold>/admin/login</>  " . str_repeat(' ', 30) . "<fg=green>│</>");
        }
        $this->command->line("  <fg=green;options=bold>└{$line}┘</>");
        $this->command->newLine();

        // ── Sample Client ────────────────────────────────────────────────
        $client = DB::table('users')->where('role', 'client')->where('status', 'active')->first();

        $this->command->line("  <fg=blue;options=bold>┌{$line}┐</>");
        $this->command->line("  <fg=blue;options=bold>│</> <fg=blue;options=bold>" . $this->pad('👤  عميل تجريبي  (Client)', 60) . "</><fg=blue;options=bold>│</>");
        $this->command->line("  <fg=blue;options=bold>├{$line}┤</>");
        if ($client) {
            $this->command->line("  <fg=blue>│</>  الاسم      : <options=bold>{$client->name}</>  " . str_repeat(' ', max(0, 43 - mb_strlen($client->name))) . "<fg=blue>│</>");
            $this->command->line("  <fg=blue>│</>  البريد     : <options=bold>{$client->email}</>  " . str_repeat(' ', max(0, 43 - mb_strlen($client->email))) . "<fg=blue>│</>");
            $this->command->line("  <fg=blue>│</>  الرصيد     : <options=bold>{$client->balance} ر.س</>  " . str_repeat(' ', max(0, 39 - mb_strlen($client->balance))) . "<fg=blue>│</>");
            $this->command->line("  <fg=blue>│</>  كلمة المرور: <options=bold>123456</>  " . str_repeat(' ', 36) . "<fg=blue>│</>");
            $this->command->line("  <fg=blue>│</>  الرابط     : <options=bold>/login</>  " . str_repeat(' ', 35) . "<fg=blue>│</>");
        }
        $this->command->line("  <fg=blue;options=bold>└{$line}┘</>");
        $this->command->newLine();

        $this->command->line("  <fg=gray>⚠️  غيّر كلمات المرور قبل النشر على الإنتاج!</>");
        $this->command->newLine();
    }

    private function pad(string $text, int $width): string
    {
        $len     = mb_strlen($text);
        $padding = max(0, $width - $len);
        return ' ' . $text . str_repeat(' ', $padding);
    }

    private function center(string $text, int $width): string
    {
        $len    = mb_strlen($text);
        $total  = max(0, $width - $len);
        $left   = (int) floor($total / 2);
        $right  = $total - $left;
        return str_repeat(' ', $left) . $text . str_repeat(' ', $right);
    }
}
