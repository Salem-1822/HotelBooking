@extends('admin.layouts.app')

@section('title', __('admin.no_hotel_title'))

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm p-4 p-md-5 text-center" style="border-radius: 1.25rem;">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 mx-auto" style="width: 80px; height: 80px;">
                        <i class="bi bi-building-exclamation text-warning" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-2">{{ __('admin.no_hotel_heading') }}</h3>
                <p class="text-muted mb-4">
                    {{ __('admin.no_hotel_desc') }}
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('admin.profile') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person me-1"></i> {{ __('admin.my_profile') }}
                    </a>
                    <form action="{{ route('super_admin.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-box-arrow-right me-1"></i> {{ __('admin.sign_out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
