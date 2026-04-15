@extends('admin.layouts.app')

@section('title', 'System Reports')

@section('content')
<div class="row g-4">
    <div class="col-md-12">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Platform Performance Report</h5>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle border" data-bs-toggle="dropdown">Select Period</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                        <li><a class="dropdown-item" href="#">This Year</a></li>
                        <li><a class="dropdown-item" href="#">Select Custom</a></li>
                    </ul>
                </div>
            </div>
            <div class="bg-light rounded text-center py-5 border-dashed">
                <i class="bi bi-bar-chart-line-fill text-primary opacity-25 fs-1 mb-3 d-block"></i>
                <h4 class="text-muted fw-bold">Comprehensive Analytics UI Placeholder</h4>
                <p class="text-secondary">Visual charts and detailed revenue metrics will appear here.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card p-4">
            <h6 class="fw-bold mb-3 text-uppercase small text-muted">City Distribution</h6>
            <div class="progress mb-3" style="height: 25px;">
                <div class="progress-bar bg-primary" style="width: 45%;">Dubai (45%)</div>
            </div>
            <div class="progress mb-3" style="height: 25px;">
                <div class="progress-bar bg-info" style="width: 30%;">Paris (30%)</div>
            </div>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar bg-warning text-dark" style="width: 25%;">Others (25%)</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card p-4 text-center">
            <h6 class="fw-bold mb-3 text-uppercase small text-muted">Total Revenue 2023</h6>
            <h1 class="display-4 fw-bold text-success">$1,245,300</h1>
            <p class="text-secondary small">+15.4% from previous year</p>
        </div>
    </div>
</div>
@endsection
