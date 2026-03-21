@extends('layouts.app')

@section('title', 'Domain Blocking - Monsieur WiFi')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">

<style>
    .avatar-content svg {
        color: inherit;
        width: 24px !important;
        height: 24px !important;
        stroke-width: 2;
        display: block !important;
    }
    
    [data-feather] {
        display: inline-block !important;
        vertical-align: middle;
    }

    .badge-category-adult {
        background-color: rgba(234, 84, 85, 0.12);
        color: #ea5455;
    }
    .badge-category-gambling {
        background-color: rgba(255, 159, 67, 0.12);
        color: #ff9f43;
    }
    .badge-category-malware {
        background-color: rgba(130, 28, 128, 0.12);
        color: #821c80;
    }
    .badge-category-social {
        background-color: rgba(0, 137, 255, 0.12);
        color: #0089ff;
    }
    .badge-category-streaming {
        background-color: rgba(40, 199, 111, 0.12);
        color: #28c76f;
    }
    .badge-category-custom {
        background-color: rgba(45, 45, 45, 0.12);
        color: #2d2d2d;
    }

    .cursor-pointer {
        cursor: pointer;
    }

</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Domain Blocking</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Domain Blocking</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
        <div class="form-group breadcrumb-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#domain-blocking-info">
                <i data-feather="info" class="mr-25"></i>
                <span>Info</span>
            </button>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Blocking Categories -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Blocking Categories</h4>
                    <p class="card-text">Toggle categories to enable or disable domain blocking by category.</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-danger p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="octagon"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Adult Content</h4>
                                                <span>1,024 domains</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-adult" checked>
                                        <label class="custom-control-label" for="category-adult"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-warning p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="dollar-sign"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Gambling</h4>
                                                <span>856 domains</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-gambling" checked>
                                        <label class="custom-control-label" for="category-gambling"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-primary p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="shield-off"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Malware</h4>
                                                <span>2,345 domains</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-malware" checked>
                                        <label class="custom-control-label" for="category-malware"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-info p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="users"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Social Media</h4>
                                                <span>342 domains</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-social">
                                        <label class="custom-control-label" for="category-social"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-success p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="film"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Streaming</h4>
                                                <span>128 domains</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-streaming">
                                        <label class="custom-control-label" for="category-streaming"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-secondary p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="tag"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Custom List</h4>
                                                <span>43 domains</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-custom" checked>
                                        <label class="custom-control-label" for="category-custom"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Domain List Table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Blocked Domains</h4>
                        <div>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-domain">
                                <i data-feather="plus" class="mr-25"></i>
                                <span>Add Domain</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-domains table">
                                <thead>
                                    <tr>
                                        <th>Domain</th>
                                        <th>Category</th>
                                        <th>Added Date</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add New Domain Modal -->
<div class="modal fade text-left" id="add-new-domain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel34" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel34">Add New Domain</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="domain-name">Domain</label>
                        <input type="text" class="form-control" id="domain-name" placeholder="example.com" />
                        <small class="form-text text-muted">Enter a domain without http:// or https://</small>
                    </div>
                    <div class="form-group">
                        <label for="domain-category">Category</label>
                        <select class="form-control" id="domain-category">
                            <option value="1">Adult Content</option>
                            <option value="2">Gambling</option>
                            <option value="3">Malware</option>
                            <option value="4">Social Media</option>
                            <option value="5">Streaming</option>
                            <option value="6">Custom List</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="domain-notes">Notes</label>
                        <textarea class="form-control" id="domain-notes" rows="3" placeholder="Enter any notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Domain</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Domain Modal -->
<div class="modal fade text-left" id="edit-domain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel35">Edit Domain</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-domain-name">Domain</label>
                        <input type="text" class="form-control" id="edit-domain-name" readonly />
                    </div>
                    <div class="form-group">
                        <label for="edit-domain-category">Category</label>
                        <select class="form-control" id="edit-domain-category">
                            <option value="1">Adult Content</option>
                            <option value="2">Gambling</option>
                            <option value="3">Malware</option>
                            <option value="4">Social Media</option>
                            <option value="5">Streaming</option>
                            <option value="6">Custom List</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-domain-notes">Notes</label>
                        <textarea class="form-control" id="edit-domain-notes" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox hidden">
                            <input type="checkbox" class="custom-control-input" id="edit-block-subdomains" checked>
                            <label class="custom-control-label" for="edit-block-subdomains">Block all subdomains</label>
                        </div>
                        <small class="form-text text-muted">All subdomains will be blocked automatically if the domain is blocked.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Domain Blocking Info Modal -->
<div class="modal fade text-left" id="domain-blocking-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel37" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel37">
                    <i data-feather="info" class="mr-1"></i>
                    How Domain Blocking Works
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-none border-left-primary">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="shield" class="mr-1"></i>
                                    What is Domain Blocking?
                                </h5>
                                <p class="card-text">
                                    Domain blocking prevents users on your network from accessing specific websites by blocking their domain names. 
                                    When a user tries to visit a blocked domain, the request is intercepted and denied, protecting your network from 
                                    unwanted content, security threats, or productivity distractions.
                                </p>
                            </div>
                        </div>

                        <div class="card shadow-none border-left-info mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="plus-circle" class="mr-1"></i>
                                    How to Add Domains
                                </h5>
                                <ol class="mb-0">
                                    <li><strong>Single Domain:</strong> Click "Add Domain" button to add individual websites</li>
                                    <li><strong>Categories:</strong> Organize domains into predefined categories for better management</li>
                                </ol>
                            </div>
                        </div>

                        <div class="card shadow-none border-left-warning mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="alert-triangle" class="mr-1"></i>
                                    Why Multiple Domains Are Needed
                                </h5>
                                <p class="card-text">
                                    Many websites use multiple domains to deliver content, avoid blocking, or improve performance. 
                                    To effectively block a service, you often need to block several related domains:
                                </p>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Service</th>
                                                <th>Domains to Block</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Facebook</strong></td>
                                                <td>facebook.com, fb.com, fbcdn.net, fb.me, messenger.com</td>
                                            </tr>
                                            <tr>
                                                <td><strong>YouTube</strong></td>
                                                <td>youtube.com, youtu.be, ytimg.com, googlevideo.com</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Instagram</strong></td>
                                                <td>instagram.com, cdninstagram.com, ig.me</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Twitter/X</strong></td>
                                                <td>twitter.com, x.com, t.co, twimg.com</td>
                                            </tr>
                                            <tr>
                                                <td><strong>TikTok</strong></td>
                                                <td>tiktok.com, tiktokv.com, tiktokcdn.com, musical.ly</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-none border-left-success mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="check-circle" class="mr-1"></i>
                                    Best Practices
                                </h5>
                                <ul class="mb-0">
                                    <li><strong>Use Categories:</strong> Group related domains for easier management</li>
                                    <li><strong>Research Thoroughly:</strong> Look up all domains used by a service before blocking</li>
                                    <li><strong>Test Blocking:</strong> Verify that the blocking works as expected</li>
                                    <li><strong>Regular Updates:</strong> Keep your block lists updated as services change domains</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <div class="alert-body">
                                <i data-feather="zap" class="mr-1"></i>
                                <strong>Pro Tip:</strong> Use browser developer tools (F12) to inspect network requests and identify 
                                all domains used by a website. This helps ensure comprehensive blocking.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Got It!</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"></script>

<script>
    const locale = '{{ $locale }}';
</script>
<script src="/assets/js/domain-blocking.js?v={{ time() }}"></script>
@endpush

@php
    $locale = 'en';
@endphp
