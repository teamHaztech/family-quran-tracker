<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Profile;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\ActivityLogger;
use App\Services\StatsService;
use App\Services\StreakService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(protected UserRepository $users)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('manage', User::class);

        $members = $this->users->members($request->only('search', 'status'));

        return view('admin.members.index', compact('members'));
    }

    public function create(): View
    {
        $this->authorize('manage', User::class);

        return view('admin.members.create');
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $member = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'name' => trim($data['first_name'] . ' ' . ($data['last_name'] ?? '')),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'photo' => $this->storePhoto($request),
            'role' => 'member',
            'status' => 'active',
            'date_joined' => $data['date_joined'] ?? now(),
            'password' => Hash::make($data['password']),
        ]);

        Profile::create([
            'user_id' => $member->id,
            'current_surah' => $data['current_surah'] ?? null,
            'current_ayah' => $data['current_ayah'] ?? null,
        ]);

        ActivityLogger::log('user.created', "Created member {$member->fullName()}", ['member_id' => $member->id]);

        return redirect()->route('admin.members.index')
            ->with('success', "Member {$member->fullName()} created successfully.");
    }

    public function show(User $member, StatsService $stats, StreakService $streaks): View
    {
        $this->authorize('manage', User::class);

        $memberStats = $stats->memberStats($member);
        $recent = $member->readingSessions()->latest('date')->latest('id')->limit(10)->get();

        return view('admin.members.show', compact('member', 'memberStats', 'recent'));
    }

    public function edit(User $member): View
    {
        $this->authorize('update', $member);

        $member->load('profile');

        return view('admin.members.edit', compact('member'));
    }

    public function update(UpdateMemberRequest $request, User $member): RedirectResponse
    {
        $data = $request->validated();

        $member->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'name' => trim($data['first_name'] . ' ' . ($data['last_name'] ?? '')),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'date_joined' => $data['date_joined'] ?? $member->date_joined,
            'photo' => $this->storePhoto($request) ?? $member->photo,
        ]);

        $member->profile()->updateOrCreate(
            ['user_id' => $member->id],
            [
                'daily_goal_pages' => $data['daily_goal_pages'] ?? null,
                'current_surah' => $data['current_surah'] ?? null,
                'current_ayah' => $data['current_ayah'] ?? null,
            ],
        );

        ActivityLogger::log('user.updated', "Updated member {$member->fullName()}", ['member_id' => $member->id]);

        return redirect()->route('admin.members.index')
            ->with('success', "Member {$member->fullName()} updated.");
    }

    public function destroy(User $member): RedirectResponse
    {
        $this->authorize('delete', $member);

        $name = $member->fullName();
        $member->delete(); // soft delete

        ActivityLogger::log('user.deleted', "Deleted member {$name}", ['member_id' => $member->id]);

        return redirect()->route('admin.members.index')
            ->with('success', "Member {$name} removed.");
    }

    public function disable(User $member): RedirectResponse
    {
        $this->authorize('update', $member);

        $member->update(['status' => 'disabled']);
        ActivityLogger::log('user.disabled', "Disabled member {$member->fullName()}", ['member_id' => $member->id]);

        return back()->with('success', "{$member->fullName()} has been disabled.");
    }

    public function enable(User $member): RedirectResponse
    {
        $this->authorize('update', $member);

        $member->update(['status' => 'active']);
        ActivityLogger::log('user.enabled', "Enabled member {$member->fullName()}", ['member_id' => $member->id]);

        return back()->with('success', "{$member->fullName()} has been enabled.");
    }

    public function resetPassword(User $member): RedirectResponse
    {
        $this->authorize('update', $member);

        $newPassword = Str::password(10);
        $member->update(['password' => Hash::make($newPassword)]);

        ActivityLogger::log('user.password_reset', "Reset password for {$member->fullName()}", ['member_id' => $member->id]);

        return back()->with('success', "New password for {$member->fullName()}: {$newPassword}");
    }

    /**
     * Store an uploaded photo and return its path, or null when none provided.
     */
    protected function storePhoto(Request $request): ?string
    {
        if ($request->hasFile('photo')) {
            return $request->file('photo')->store('photos', 'public');
        }

        return null;
    }
}
