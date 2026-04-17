@extends('admin.layouts.app')

@section('title', 'Platform Settings')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4">
            <h5 class="fw-bold mb-4">General Configuration</h5>
            <form>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Platform Name</label>
                        <input type="text" class="form-control" value="Hotelia Reservation System">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Contact Email</label>
                        <input type="email" class="form-control" value="support@hotelia.com">
                    </div>
                    <div class="col-md-12 mt-4">
                        <label class="form-label small fw-bold text-muted">Platform Logo</label>
                        <div class="border rounded-3 p-3 text-center bg-light">
                            <i class="bi bi-image fs-3 opacity-25 d-block"></i>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2">Upload New Logo</button>
                        </div>
                    </div>
                    <div class="col-md-6 mt-4">
                        <label class="form-label small fw-bold text-muted">Currency Code</label>
                        <select class="form-select">
                            <option>USD ($)</option>
                            <option>EUR (€)</option>
                            <option>GBP (£)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-4">
                        <label class="form-label small fw-bold text-muted">Timezone</label>
                        <select class="form-select">
                            <option>UTC +00:00</option>
                            <option>UTC +01:00 (Paris)</option>
                            <option>UTC +04:00 (Dubai)</option>
                        </select>
                    </div>
                    <div class="col-md-12 mt-5 text-end">
                        <button type="button" class="btn btn-light border me-2">Reset to Defaults</button>
                        <button type="submit" class="btn btn-primary px-4">Update Settings</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card p-4 mt-4 border-danger">
            <h5 class="fw-bold mb-3 text-danger">Maintenance Mode</h5>
            <p class="text-muted small">Enabling maintenance mode will block all non-admin access to the client portal and hotel dashboards.</p>
            <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" role="switch" id="maintenanceSwitch">
                <label class="form-check-label text-dark fw-bold" for="maintenanceSwitch">Deactivate Platform Temporarily</label>
            </div>
        </div>
    </div>
</div>
@endsection
