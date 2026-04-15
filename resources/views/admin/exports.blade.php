@extends('admin.layouts.app')

@section('title', 'Data Export Center')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-md-9">
        <div class="card">
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <i class="bi bi-download text-primary display-3 mb-3"></i>
                    <h3 class="fw-bold">Export Platform Data</h3>
                    <p class="text-muted">Generate and download PDF/Excel reports for various platform modules.</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-4 border rounded-3 bg-light text-center h-100">
                            <i class="bi bi-building fs-1 text-danger mb-2"></i>
                            <h6 class="fw-bold mt-2">Hotels Directory</h6>
                            <p class="small text-muted mb-3">Full list of active and inactive hotels with ownership details.</p>
                            <button class="btn btn-outline-danger btn-sm w-100">Export All Hotels PDF</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 border rounded-3 bg-light text-center h-100">
                            <i class="bi bi-geo-alt fs-1 text-primary mb-2"></i>
                            <h6 class="fw-bold mt-2">Cities Overview</h6>
                            <p class="small text-muted mb-3">List of covered cities and hotel counts per location.</p>
                            <button class="btn btn-outline-primary btn-sm w-100">Export Cities PDF</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 border rounded-3 bg-light text-center h-100">
                            <i class="bi bi-people fs-1 text-info mb-2"></i>
                            <h6 class="fw-bold mt-2">Admin Accounts</h6>
                            <p class="small text-muted mb-3">Audit report of all registered hotel admin accounts.</p>
                            <button class="btn btn-outline-info btn-sm w-100 text-dark">Export Admin List PDF</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 border rounded-3 bg-light text-center h-100">
                            <i class="bi bi-cash-coin fs-1 text-success mb-2"></i>
                            <h6 class="fw-bold mt-2">Financial Records</h6>
                            <p class="small text-muted mb-3">Detailed reservation revenue report for platform accounting.</p>
                            <button class="btn btn-outline-success btn-sm w-100">Export Transactions PDF</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
