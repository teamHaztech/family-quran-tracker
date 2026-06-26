<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\BadgeSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

/**
 * One-time, browser-based installer for hosts without SSH (e.g. Hostinger
 * shared hosting). Guarded by the DEPLOY_KEY env value — when DEPLOY_KEY is
 * blank the route 404s, so blank it out (and re-deploy) once setup is done.
 */
class InstallController extends Controller
{
    public function run(Request $request, string $token)
    {
        $key = config('app.deploy_key');

        // Disabled unless a deploy key is configured and matches.
        abort_if(empty($key) || ! hash_equals($key, $token), 404);

        $log = [];

        // 1. Run migrations
        Artisan::call('migrate', ['--force' => true]);
        $log[] = '✓ Database migrated';

        // 2. Storage symlink (best effort — may fail on some shared hosts)
        try {
            Artisan::call('storage:link');
            $log[] = '✓ Storage linked';
        } catch (\Throwable $e) {
            $log[] = '⚠ Storage link skipped: ' . $e->getMessage();
        }

        // 3. Seed badge catalogue + ensure a settings row exists
        (new BadgeSeeder())->run();
        Setting::current();
        $log[] = '✓ Badges & settings ready';

        // 4. Create the first Family Leader if none exists
        if (! User::where('role', 'admin')->exists()) {
            $email = env('DEPLOY_ADMIN_EMAIL', 'admin@example.com');
            $password = env('DEPLOY_ADMIN_PASSWORD', 'ChangeMe123!');

            $admin = User::create([
                'first_name' => env('DEPLOY_ADMIN_FIRST', 'Family'),
                'last_name' => env('DEPLOY_ADMIN_LAST', 'Leader'),
                'name' => trim(env('DEPLOY_ADMIN_FIRST', 'Family') . ' ' . env('DEPLOY_ADMIN_LAST', 'Leader')),
                'email' => $email,
                'role' => 'admin',
                'status' => 'active',
                'date_joined' => now(),
                'email_verified_at' => now(),
                'password' => Hash::make($password),
            ]);
            Profile::create(['user_id' => $admin->id]);

            $log[] = "✓ Admin created — email: {$email}";
            $log[] = "  (set DEPLOY_ADMIN_EMAIL / DEPLOY_ADMIN_PASSWORD in .env to customise)";
        } else {
            $log[] = '• Admin already exists — skipped';
        }

        // 5. Cache config/routes/views for production performance
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        $log[] = '✓ Caches built';

        $log[] = '';
        $log[] = 'DONE. Now blank out DEPLOY_KEY in .env (and re-deploy) to disable this installer.';

        return response('<pre style="font:15px/1.7 monospace;background:#0f172a;color:#a7f3d0;padding:24px;border-radius:12px;max-width:760px;margin:40px auto">'
            . e(implode("\n", $log))
            . '</pre>')->header('Content-Type', 'text/html');
    }
}
