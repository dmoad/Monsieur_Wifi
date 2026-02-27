@extends('layouts.app')

@section('title', 'Devices - Monsieur WiFi')

@push('styles')
<style>
    .device-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }
    .device-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(115, 103, 240, 0.15);
    }
    .device-header {
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .device-info {
        flex: 1;
    }
    .device-serial {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    .device-details {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .device-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }
    .device-actions {
        display: flex;
        gap: 0.5rem;
    }
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Devices</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Devices</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <div class="filter-section">
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" id="search" class="form-control" placeholder="Search by serial, MAC, or model...">
            </div>
            <div class="col-md-3 mb-2">
                <select id="location-status-filter" class="form-control">
                    <option value="">All Devices</option>
                    <option value="unassigned">Unassigned to Location</option>
                    <option value="assigned">Assigned to Location</option>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <button class="btn btn-primary" onclick="loadDevices()">
                    <i data-feather="search"></i> Search
                </button>
            </div>
        </div>
    </div>
    
    <div id="devices-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    
    <div id="devices-list"></div>
    <div id="pagination-container"></div>
</div>

<!-- Change Owner Modal -->
<div class="modal fade" id="change-owner-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Device Owner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="device-id">
                <div class="form-group">
                    <label for="new-owner">New Owner *</label>
                    <select id="new-owner" class="form-control">
                        <option value="">Select owner...</option>
                    </select>
                </div>
                <div id="device-info" class="alert alert-info"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateDeviceOwner()">Update Owner</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/devices.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'en';
@endphp
