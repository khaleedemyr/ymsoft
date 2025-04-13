@foreach($activities as $activity)
<div class="activity-item p-3 border-bottom border-bottom-dashed">
    <div class="d-flex align-items-center gap-3">
        <div class="flex-shrink-0">
            <div class="avatar-sm">
                <div class="avatar-title bg-light text-primary rounded d-flex align-items-center justify-content-center">
                    @php
                        $name = $activity->user->name ?? 'User';
                        $nameParts = explode(' ', $name);
                        $initials = '';
                        foreach ($nameParts as $part) {
                            $initials .= substr($part, 0, 1);
                        }
                        $initials = substr($initials, 0, 2);
                    @endphp
                    {{ $initials }}
                </div>
            </div>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">
                        {{ $activity->user->nama_lengkap ?? 'System' }}
                        <span class="text-muted fs-sm">
                            {{ $activity->action }}
                            @if($activity->description)
                                - {{ $activity->description }}
                            @endif
                        </span>
                    </h6>
                    <p class="text-muted mb-0">
                        <small>{{ $activity->created_at->format('d M Y, H:i:s') }}</small>
                    </p>
                </div>
                <div>
                    @if($activity->task)
                        <span class="badge bg-primary-subtle text-primary">
                            {{ $activity->task->task_number }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
