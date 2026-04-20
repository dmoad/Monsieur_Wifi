@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $guestsT = [
        'heading_suffix' => __('location_guests.heading_suffix'),
        'empty_table' => __('location_guests.empty_table'),
        'zero_records' => __('location_guests.zero_records'),
        'loading' => __('common.loading'),
        'time_just_now' => __('location_guests.time_just_now'),
        'time_min_ago' => __('location_guests.time_min_ago'),
        'time_mins_ago' => __('location_guests.time_mins_ago'),
        'time_hour_ago' => __('location_guests.time_hour_ago'),
        'time_hours_ago' => __('location_guests.time_hours_ago'),
        'time_day_ago' => __('location_guests.time_day_ago'),
        'time_days_ago' => __('location_guests.time_days_ago'),
        'load_error' => __('location_guests.load_error'),
        'load_failed' => __('location_guests.load_failed'),
        'export_started' => __('location_guests.export_started'),
        'export_failed' => __('location_guests.export_failed'),
    ];
@endphp

@section('title', __('location_guests.page_title'))

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0" id="location-name">{{ __('location_guests.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/locations">{{ __('sidebar.locations') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('location_guests.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <div class="form-group breadcrumb-right">
            <button class="btn btn-primary" id="export-csv-btn">
                <i data-feather="download" class="mr-50"></i>
                <span>{{ __('location_guests.export_csv') }}</span>
            </button>
        </div>
    </div>
</div>

<div class="content-body">
    <section id="guests-content">
        <div class="row" id="table-hover-row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('location_guests.guest_list') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="guests-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('location_guests.col_mac') }}</th>
                                    <th>{{ __('location_guests.col_email') }}</th>
                                    <th>{{ __('location_guests.col_phone') }}</th>
                                    <th>{{ __('location_guests.col_first_login') }}</th>
                                </tr>
                            </thead>
                            <tbody id="guests-table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>

<script>
    window.GUESTS_T = {!! json_encode($guestsT) !!};
    const T = window.GUESTS_T;
    const PAGE_LOCALE = '{{ $locale }}';
    const DATE_LOCALE = PAGE_LOCALE === 'fr' ? 'fr-FR' : 'en-US';

    let guestsTable;
    let locationId = '{{ $location }}';
    let currentSearch = '';

    $(document).ready(function() {
        const user = UserManager.getUser();
        const token = UserManager.getToken();

        if (!token || !user) {
            window.location.href = '/';
            return;
        }

        loadLocationData();

        guestsTable = $('#guests-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            searching: true,
            order: [[4, 'desc']],
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'mac_address', name: 'mac_address', defaultContent: '-' },
                { data: 'email', name: 'email', defaultContent: '-' },
                { data: 'phone', name: 'phone', defaultContent: '-' },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                    defaultContent: '-',
                    render: function(data, type, row) {
                        if (!data || data === '-') return '-';
                        if (type === 'sort') return data;

                        const date = new Date(data);
                        const now = new Date();
                        const diffMs = now - date;
                        const diffMins = Math.floor(diffMs / 60000);
                        const diffHours = Math.floor(diffMs / 3600000);
                        const diffDays = Math.floor(diffMs / 86400000);

                        if (diffMins < 1) {
                            return '<span class="badge badge-light-success">' + T.time_just_now + '</span>';
                        } else if (diffMins < 60) {
                            const key = diffMins > 1 ? T.time_mins_ago : T.time_min_ago;
                            return '<span class="badge badge-light-success">' + key.replace('{count}', diffMins) + '</span>';
                        } else if (diffHours < 24) {
                            const key = diffHours > 1 ? T.time_hours_ago : T.time_hour_ago;
                            return '<span class="badge badge-light-info">' + key.replace('{count}', diffHours) + '</span>';
                        } else if (diffDays < 7) {
                            const key = diffDays > 1 ? T.time_days_ago : T.time_day_ago;
                            return '<span class="badge badge-light-warning">' + key.replace('{count}', diffDays) + '</span>';
                        } else {
                            return '<span class="badge badge-light-secondary">' +
                                   date.toLocaleDateString(DATE_LOCALE) + ' ' +
                                   date.toLocaleTimeString(DATE_LOCALE, {hour: '2-digit', minute:'2-digit'}) +
                                   '</span>';
                        }
                    }
                }
            ],
            language: {
                emptyTable: T.empty_table,
                zeroRecords: T.zero_records,
                processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">' + T.loading + '</span></div>'
            }
        });

        loadGuestsData();

        $('#search-guests').on('keyup', function() {
            currentSearch = $(this).val();
            guestsTable.search(currentSearch).draw();
        });

        $('#export-csv-btn').on('click', function() {
            exportToCSV();
        });
    });

    function loadLocationData() {
        const token = UserManager.getToken();

        $.ajax({
            url: `/api/locations/${locationId}`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            success: function(response) {
                let location = null;
                if (response.data) {
                    location = response.data;
                } else if (response.location) {
                    location = response.location;
                }

                if (location && location.name) {
                    $('#location-name').text(location.name + T.heading_suffix);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading location data:', error);
                if (xhr.status === 401) {
                    window.location.href = '/';
                }
            }
        });
    }

    function loadGuestsData() {
        const token = UserManager.getToken();

        $.ajax({
            url: `/api/locations/${locationId}/guest-users`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success && response.data) {
                    const guests = response.data;
                    guestsTable.clear();
                    if (guests.length > 0) {
                        guests.forEach(function(guest) {
                            guestsTable.row.add({
                                mac_address: guest.mac_address || '-',
                                email: guest.email || '-',
                                phone: guest.phone || '-',
                                updated_at: guest.created_at || '-'
                            });
                        });
                    }
                    guestsTable.draw();
                } else {
                    guestsTable.clear().draw();
                    toastr.error(T.load_error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading guests:', error);
                guestsTable.clear().draw();
                if (xhr.status === 401) {
                    window.location.href = '/';
                } else {
                    toastr.error(T.load_failed);
                }
            }
        });
    }

    function exportToCSV() {
        const token = UserManager.getToken();
        const searchParam = currentSearch ? `?search=${encodeURIComponent(currentSearch)}` : '';
        const url = `/api/locations/${locationId}/guest-users/export${searchParam}`;

        const link = document.createElement('a');
        link.href = url;
        link.style.display = 'none';

        fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'text/csv'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Export failed');
            return response.blob();
        })
        .then(blob => {
            const downloadUrl = window.URL.createObjectURL(blob);
            link.href = downloadUrl;
            link.download = `location_${locationId}_guests_${new Date().toISOString().slice(0,10)}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(downloadUrl);
            toastr.success(T.export_started);
        })
        .catch(error => {
            console.error('Export error:', error);
            toastr.error(T.export_failed);
        });
    }
</script>
@endpush
