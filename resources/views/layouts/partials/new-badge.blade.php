{{-- Small count badge for the navbar. Expects $count. Shows nothing when the count is 0. --}}
@if ($count > 0)
    <span class="badge ms-1">{{ $count }}<span class="visually-hidden"> new {{ \Illuminate\Support\Str::plural('announcement', $count) }}</span></span>
@endif
