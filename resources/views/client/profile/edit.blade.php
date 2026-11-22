@extends('client.layouts.app')

@section('title', __('client.profile_title'))

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('client.my_profile') }}</h2>
            <p class="text-muted">{{ __('client.manage_profile_desc') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Profile Information Form -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 1rem; border: 1px solid var(--border-color);">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">{{ __('client.profile_information') }}</h5>
                    <form method="POST" action="{{ route('client.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('client.full_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('client.email_address') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" dir="ltr" style="text-align:start;" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label">{{ __('client.phone_number') }}</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" dir="ltr" style="text-align:start;">
                            <div class="form-text">{{ __('client.phone_help') }}</div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary" style="background-color: var(--brand-primary); border-color: var(--brand-primary);">{{ __('client.save_changes') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Update Form -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 1rem; border: 1px solid var(--border-color);">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">{{ __('client.update_password') }}</h5>
                    <form method="POST" action="{{ route('client.profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('client.current_password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('client.new_password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">{{ __('client.confirm_new_password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary" style="background-color: var(--brand-primary); border-color: var(--brand-primary);">{{ __('client.update_password_btn') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
