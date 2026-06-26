<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReadingSessionRequest;
use App\Http\Requests\UpdateReadingSessionRequest;
use App\Models\ReadingSession;
use App\Repositories\ReadingSessionRepository;
use App\Services\ReadingSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReadingSessionController extends Controller
{
    public function __construct(
        protected ReadingSessionService $sessions,
        protected ReadingSessionRepository $repo,
    ) {
    }

    /** Reading history with search / filters. */
    public function index(Request $request): View
    {
        $user = $request->user();
        $history = $this->repo->historyFor($user, $request->only('search', 'from', 'to', 'period'));
        $today = $this->repo->todayFor($user);

        return view('reading.index', compact('history', 'today'));
    }

    /** Manual entry form. */
    public function create(): View
    {
        return view('reading.create');
    }

    /** Built-in reading mode (timer). */
    public function timer(): View
    {
        return view('reading.timer');
    }

    public function store(StoreReadingSessionRequest $request): RedirectResponse
    {
        $result = $this->sessions->create($request->user(), $request->validated());

        return $this->redirectWithBadges(
            route('member.dashboard'),
            'Reading session saved. Baarakallahu feek! 🤲',
            $result['newBadges'],
        );
    }

    public function edit(ReadingSession $reading): View
    {
        $this->authorize('update', $reading);

        return view('reading.edit', ['session' => $reading]);
    }

    public function update(UpdateReadingSessionRequest $request, ReadingSession $reading): RedirectResponse
    {
        $this->sessions->update($reading, $request->validated());

        return redirect()->route('reading.index')->with('success', 'Reading session updated.');
    }

    public function destroy(ReadingSession $reading): RedirectResponse
    {
        $this->authorize('delete', $reading);
        $reading->delete();

        return redirect()->route('reading.index')->with('success', 'Reading session deleted.');
    }

    /**
     * Redirect, flashing any newly-awarded badges for the congrats popup.
     */
    protected function redirectWithBadges(string $url, string $message, $newBadges): RedirectResponse
    {
        $redirect = redirect($url)->with('success', $message);

        if ($newBadges->isNotEmpty()) {
            $redirect->with('new_badges', $newBadges->map(fn ($b) => [
                'name' => $b->name,
                'icon' => $b->icon,
            ])->all());
        }

        return $redirect;
    }
}
