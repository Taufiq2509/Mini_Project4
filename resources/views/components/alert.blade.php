@props(['type' => 'info'])

@php
    $classes = match($type) {
        'success' => 'alert-success',
        'danger', 'error' => 'alert-danger',
        'warning' => 'alert-warning',
        default => 'alert-info',
    };

    $icon = match($type) {
        'success' => '✓',
        'danger', 'error' => '✕',
        'warning' => '⚠',
        default => 'ℹ',
    };
@endphp

<div class="alert {{ $classes }}" role="alert">
    <div class="alert-icon">{{ $icon }}</div>
    <div class="alert-content">
        {{ $slot }}
    </div>
</div>
