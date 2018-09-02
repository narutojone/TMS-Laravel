<span class="{{ getUserWeeklyCapabilityStyleClass($user) }}">
    Worked {{ $user->timeEntriesThisWeek() }}
    @if ($user->weekly_capacity)
        / {{ Auth::user()->weekly_capacity }}h
    @endif
</span>