@extends('layouts.main')

@section('title', 'Profile')

@section('content')
    <h1 class="h3 mb-3">Profile</h1>

    <div class="row">
        <div class="col-lg-8 col-xl-6">
            <div class="card"><div class="card-body p-4">
                @include('profile.partials.update-profile-information-form')
            </div></div>

            <div class="card"><div class="card-body p-4">
                @include('profile.partials.update-password-form')
            </div></div>

            <div class="card"><div class="card-body p-4">
                @include('profile.partials.delete-user-form')
            </div></div>
        </div>
    </div>
@endsection
