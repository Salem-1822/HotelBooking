@extends('client.layouts.app')

@section('title', __('contact.contact_us_title'))

@push('styles')
<style>
    .contact-hero {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent-dark) 100%);
        padding: 5rem 0;
        text-align: center;
        color: white;
        margin-top: 76px; /* Offset for fixed navbar */
    }

    .contact-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .contact-info-panel {
        background: var(--brand-primary);
        color: white;
        padding: 3rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .contact-info-panel h4 {
        color: var(--brand-accent);
        font-weight: 700;
        margin-bottom: 2rem;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .info-item i {
        font-size: 1.5rem;
        color: var(--brand-accent);
        margin-right: 1rem;
        margin-top: 0.25rem;
    }

    @if(app()->getLocale() === 'ar')
    .info-item i {
        margin-right: 0;
        margin-left: 1rem;
    }
    @endif

    .contact-form-panel {
        padding: 3rem;
    }

    .form-control:focus {
        border-color: var(--brand-accent);
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3 reveal">{{ __('contact.contact_us_title') }}</h1>
            <p class="lead mb-0 text-white-50 reveal delay-1">{{ __('contact.contact_subtitle') }}</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5" style="background-color: var(--bg-body);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                            <i class="bi bi-exclamation-octagon-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="contact-card row g-0 reveal delay-2">
                        <!-- Contact Info -->
                        <div class="col-md-5 d-none d-md-block">
                            <div class="contact-info-panel">
                                <h4>{{ __('contact.get_in_touch') }}</h4>
                                <div class="info-item">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ __('contact.our_location') }}</h6>
                                        <p class="text-white-50 mb-0">{!! __('contact.our_location_val') !!}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-envelope-paper-fill"></i>
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ __('contact.email_us') }}</h6>
                                        <p class="text-white-50 mb-0">support@hotelbooking.com</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-telephone-fill"></i>
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ __('contact.call_us') }}</h6>
                                        <p class="text-white-50 mb-0">+212 500 000 000</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form -->
                        <div class="col-md-7">
                            <div class="contact-form-panel">
                                <h3 class="fw-bold mb-4">{{ __('contact.send_us_a_message') }}</h3>
                                <form action="{{ route('contact.store') }}" method="POST">
                                    @csrf
                                    
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label for="name" class="form-label fw-semibold">{{ __('contact.your_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::guard('web')->user()?->name) }}" placeholder="{{ __('contact.name_placeholder') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="email" class="form-label fw-semibold">{{ __('contact.your_email') }} <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::guard('web')->user()?->email) }}" placeholder="{{ __('contact.email_placeholder') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="subject" class="form-label fw-semibold">{{ __('contact.subject') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="{{ __('contact.subject_placeholder') }}" required>
                                            @error('subject')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="message" class="form-label fw-semibold">{{ __('contact.message') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="{{ __('contact.message_placeholder') }}" required>{{ old('message') }}</textarea>
                                            @error('message')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 mt-4">
                                            <button type="submit" class="btn btn-accent w-100 py-2 fw-bold text-uppercase" style="letter-spacing: 1px;">
                                                {{ __('contact.send_message') }} <i class="bi bi-send ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
