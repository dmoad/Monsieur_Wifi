@extends('layouts.app')

@section('title', 'Manage Inventory - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Manage Inventory</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="/en/shop">Shop</a></li>
                        <li class="breadcrumb-item active">Manage Inventory</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <a href="/en/admin/models" class="btn btn-outline-primary">
            <i data-feather="cpu"></i> Manage Models
        </a>
    </div>
</div>
<div class="content-body">
    <!-- Summary Cards -->
    <div class="row" id="summary-cards">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Total Products</h6>
                    <h3 class="mb-0" id="total-products">-</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Out of Stock</h6>
                    <h3 class="mb-0 text-danger" id="out-of-stock">-</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Low Stock</h6>
                    <h3 class="mb-0 text-warning" id="low-stock">-</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Total Value</h6>
                    <h3 class="mb-0 text-success" id="total-value">-</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Inventory Management</h4>
            <div class="text-muted">
                <small><i data-feather="info" style="width: 14px; height: 14px;"></i> Click <strong>"Add/View Devices"</strong> button to add inventory items one at a time with MAC address & serial number</small>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <select id="stock-status-filter" class="form-control">
                        <option value="">All Stock Status</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="search" class="form-control" placeholder="Search products...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="loadInventory()">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="inventory-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    
    <div id="inventory-list"></div>
    
    <div id="inventory-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Inventory</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="modal-content"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/admin-inventory.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'en';
@endphp
