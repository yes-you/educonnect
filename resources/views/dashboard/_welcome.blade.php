{{-- First-visit tip for each role. Hidden until educonnect.js confirms this user has not dismissed it on this device. --}}
@php
    $tips = [
        'student' => 'Open Modules for your grade\'s study guides, then take a quiz to see your score right away. Your grades and class schedule are under School.',
        'teacher' => 'Upload a module, build a quiz or post an announcement from the quick actions below. Encode grades and manage schedules under School.',
        'parent' => 'Your child\'s latest grades and quiz results appear below. If you do not see your child yet, ask the school administrator to link your account.',
        'organization' => 'Create a posting for a scholarship, tutoring program or event. Students see it after the school administrator approves it.',
        'admin' => 'Link parents to students and review pending postings under Admin. Manage accounts under Users.',
    ];
    $role = auth()->user()->role;
@endphp

<div class="alert alert-info alert-dismissible" role="note" hidden
     data-welcome="ec-welcome-{{ $role }}-{{ auth()->id() }}">
    <strong>New here?</strong> {{ $tips[$role] }}
    <button type="button" class="btn-close" data-welcome-dismiss aria-label="Dismiss this tip"></button>
</div>
