<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Profile;
use App\Models\ReadingSession;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\BadgeSeeder::class);
        Setting::current();

        $this->admin = User::factory()->admin()->create(['email' => 'admin@test.com']);
        Profile::create(['user_id' => $this->admin->id]);

        $this->member = User::factory()->create(['email' => 'member@test.com']);
        Profile::create(['user_id' => $this->member->id]);
        ReadingSession::factory()->count(5)->for($this->member)->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/member/dashboard')->assertRedirect(route('login'));
    }

    public function test_admin_pages_render(): void
    {
        $member = $this->member;

        foreach ([
            route('admin.dashboard'),
            route('admin.members.index'),
            route('admin.members.create'),
            route('admin.members.show', $member),
            route('admin.members.edit', $member),
            route('admin.reports.index'),
            route('admin.settings.edit'),
            route('admin.activity.index'),
        ] as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }
    }

    public function test_member_pages_render(): void
    {
        $session = $this->member->readingSessions()->first();

        foreach ([
            route('member.dashboard'),
            route('reading.index'),
            route('reading.create'),
            route('reading.timer'),
            route('reading.edit', $session),
            route('leaderboard.index'),
            route('badges.index'),
            route('quran.index'),
            route('quran.show', 1),
            route('quran.show', 114),
            route('profile.edit'),
        ] as $url) {
            $this->actingAs($this->member)->get($url)->assertOk();
        }
    }

    public function test_quran_reader_serves_arabic_and_translation(): void
    {
        $response = $this->actingAs($this->member)->get(route('quran.show', 2));

        $response->assertOk()
            ->assertSee('Al-Baqara')                       // english name
            ->assertSee('there is no deity except Him');   // Ayat al-Kursi translation

        // Invalid surah numbers 404.
        $this->actingAs($this->member)->get('/quran/115')->assertNotFound();
    }

    public function test_member_cannot_access_admin(): void
    {
        $this->actingAs($this->member)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_create_member(): void
    {
        $this->actingAs($this->admin)->post(route('admin.members.store'), [
            'first_name' => 'New',
            'last_name' => 'Member',
            'email' => 'new@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'new@test.com', 'role' => 'member']);
    }

    public function test_member_can_log_reading(): void
    {
        $this->actingAs($this->member)->post(route('reading.store'), [
            'date' => now()->toDateString(),
            'surah' => 'Al-Kahf',
            'start_page' => 293,
            'end_page' => 304,
            'minutes_read' => 20,
            'method' => 'manual',
        ])->assertRedirect();

        $this->assertDatabaseHas('reading_sessions', [
            'user_id' => $this->member->id,
            'surah' => 'Al-Kahf',
            'pages_read' => 12,
        ]);
    }

    public function test_report_exports(): void
    {
        foreach (['csv', 'xlsx', 'pdf'] as $format) {
            $this->actingAs($this->admin)
                ->get(route('admin.reports.export', ['format' => $format, 'period' => 'monthly']))
                ->assertOk();
        }
    }

    public function test_disabled_member_is_blocked(): void
    {
        $this->member->update(['status' => 'disabled']);
        $this->actingAs($this->member)->get(route('member.dashboard'))->assertRedirect(route('login'));
    }
}
