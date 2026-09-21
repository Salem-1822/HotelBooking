@extends('client.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="font-family: 'Poppins', sans-serif; font-weight: 700;">My Profile</h2>
            <p class="text-muted">Manage your account information and security.</p>
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
                    <h5 class="card-title mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Profile Information</h5>
                    <form method="POST" action="{{ route('client.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            <div class="form-text">Used for your reservations. Normalizes to a standard format.</div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary" style="background-color: var(--brand-primary); border-color: var(--brand-primary);">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Update Form -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 1rem; border: 1px solid var(--border-color);">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Update Password</h5>
                    <form method="POST" action="{{ route('client.profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary" style="background-color: var(--brand-primary); border-color: var(--brand-primary);">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
