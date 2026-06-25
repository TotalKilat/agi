@extends('layouts.app')

@section('title', 'Fleet Transactions')
@section('page-title', 'Fleet Transactions')
@section('crud-assets', 'true')

@section('content')
    <div class="page-section active js-crud-page" id="fleetTransactionIndexPage" data-csrf-token="{{ csrf_token() }}"
        data-success-message="{{ session('success') }}" data-info-message="{{ session('info') }}"
        data-error-message="{{ session('error') }}">
        <div class="page-container">
            <div class="page-header">
                <div>
                    <h1 class="page-header-title">Fleet Transactions</h1>
                    <p class="page-header-subtitle">Upload and manage daily vehicle fuel performance transactions</p>
                </div>
                <div class="page-header-actions">
                    <button type="button" class="btn btn-secondary btn-sm"
                        data-modal-target="fleetTransactionRecalculateModal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 7h-5V2" />
                            <path d="M4 17h5v5" />
                            <path d="M5.5 9a7 7 0 0 1 11.8-3L20 7" />
                            <path d="M4 17l2.7 1A7 7 0 0 0 18.5 15" />
                        </svg>
                        Hitung Ulang KM/L
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-modal-target="fleetTransactionImportModal">
                        <x-menu-icon name="receipt" />
                        Upload File
                    </button>
                    <a href="{{ route('fleet-transactions.create') }}" class="btn btn-primary btn-sm">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        New Transaction
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Daily Transactions</h3>
                        <p class="card-subtitle">Rows are linked to fleet master data by vehicle name during import.</p>
                    </div>
                </div>

                <div class="transaction-filter-bar" id="fleetTransactionFilterBar">
                    <div class="transaction-filter-group">
                        <label class="transaction-filter-label" for="filterTransactionFleet">Fleet</label>
                        <input type="text" id="filterTransactionFleet" class="transaction-filter-input"
                            placeholder="Nama fleet">
                    </div>
                    <div class="transaction-filter-group">
                        <label class="transaction-filter-label" for="filterTransactionCustomer">Customer</label>
                        <input type="text" id="filterTransactionCustomer" class="transaction-filter-input"
                            placeholder="Nama customer">
                    </div>
                    <div class="transaction-filter-group">
                        <label class="transaction-filter-label" for="filterTransactionFleetType">Fleet Type</label>
                        <select id="filterTransactionFleetType"
                            class="transaction-filter-select js-select2 js-filter-multi-select" multiple
                            data-placeholder="Semua" data-allow-clear="true">
                            @foreach ($fleetTypes as $fleetType)
                                <option value="{{ $fleetType->id }}">{{ $fleetType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="transaction-filter-group">
                        <label class="transaction-filter-label" for="filterTransactionLocation">Location</label>
                        <select id="filterTransactionLocation"
                            class="transaction-filter-select js-select2 js-filter-multi-select" multiple
                            data-placeholder="Semua" data-allow-clear="true">
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="transaction-filter-group">
                        <label class="transaction-filter-label" for="filterTransactionDateStart">Date From</label>
                        <input type="date" id="filterTransactionDateStart" class="transaction-filter-input">
                    </div>
                    <div class="transaction-filter-group">
                        <label class="transaction-filter-label" for="filterTransactionDateEnd">Date To</label>
                        <input type="date" id="filterTransactionDateEnd" class="transaction-filter-input">
                    </div>
                    <div class="transaction-filter-group">
                        <label class="transaction-filter-label" for="filterTransactionStatus">Status</label>
                        <select id="filterTransactionStatus" class="transaction-filter-select">
                            <option value="">Semua</option>
                            <option value="normal">Wajar</option>
                            <option value="abnormal">Tidak Wajar</option>
                            <option value="no_sensor">Fuel Sensor Belum Terpasang</option>
                            <option value="no_data">KM/L Belum Ada</option>
                        </select>
                    </div>
                    <div class="transaction-filter-actions">
                        <button type="button" id="applyFleetTransactionFilter" class="transaction-filter-apply">
                            Terapkan Filter
                        </button>
                        <button type="button" id="resetFleetTransactionFilter" class="transaction-filter-reset"
                            style="display:none">
                            Reset
                        </button>
                    </div>
                </div>

                <div class="data-table-container">
                    <table class="table js-data-table" id="fleetTransactionTable"
                        data-url="{{ route('fleet-transactions.data') }}" data-order='[[3,"desc"]]'
                        data-plural-label="transactions" data-search-placeholder="Search transactions...">
                        <thead>
                            <tr>
                                <th data-column="row_number" data-orderable="false" data-searchable="false">No</th>
                                <th data-column="action" data-orderable="false" data-searchable="false"></th>
                                <th data-column="fleet_name" data-name="vehicle_name_snapshot">Fleet</th>
                                <th data-column="transaction_date">Date</th>
                                <th data-column="customer_name" data-orderable="false">Customer</th>
                                <th data-column="fleet_type_name" data-orderable="false">Fleet Type</th>
                                <th data-column="location_name" data-orderable="false">Location</th>
                                <th data-column="odometer_km" data-name="odometer_km">Odometer</th>
                                <th data-column="usage_l" data-name="usage_l">Usage</th>
                                <th data-column="cost_rp" data-name="cost_rp">Cost</th>
                                <th data-column="refuel_l" data-name="refuel_l">Refuel</th>
                                <th data-column="km_per_l" data-name="km_per_l" data-align="center">KM/L</th>
                                <th data-column="l_per_km" data-name="l_per_km" data-align="center">L/KM</th>
                                <th data-column="status" data-name="km_per_l" data-align="center" data-orderable="false">
                                    Status</th>
                                <th data-column="running_duration" data-name="running_duration_seconds">Running</th>
                                <th data-column="idle_duration" data-name="idle_duration_seconds">Idle</th>
                                <th data-column="stop_duration" data-name="stop_duration_seconds">Stop</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <x-modal id="fleetTransactionImportModal" title="Upload Transactions">
            <form method="POST" action="{{ route('fleet-transactions.import') }}" class="js-async-form"
                data-success-title="Import complete" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="transaction_file" class="form-label">Daily Performance File</label>
                        <input type="file" name="file" id="transaction_file" class="form-input"
                            accept=".xls,.html,.htm" required>
                        <div class="form-hint">
                            Upload the Daily Performance Analysis Report export. Vehicle names in the file must already
                            exist in Fleet. Maximum file size is 100 MB.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Importing...">
                        Import Transactions
                    </button>
                </div>
            </form>
        </x-modal>

        <x-modal id="fleetTransactionRecalculateModal" title="Hitung Ulang Efisiensi">
            <form method="POST" action="{{ route('fleet-transactions.recalculate-efficiency') }}" class="js-async-form"
                data-success-title="Perhitungan ulang selesai">
                @csrf
                <div class="modal-body">
                    <p class="text-sm text-gray-600">
                        Sistem akan menghitung ulang nilai <strong>KM/L</strong> dan <strong>L/KM</strong> untuk semua data
                        transaksi
                        yang sudah ter-upload.
                    </p>
                    <div class="form-hint" style="margin-top: 10px;">
                        Proses ini aman dan tidak menghapus data transaksi.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Menghitung ulang...">
                        Mulai Hitung Ulang
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var BASE_URL = '{{ route('fleet-transactions.data') }}';
            var FILTER_FIELDS = [{
                    id: 'filterTransactionFleet',
                    param: 'fleet_name'
                },
                {
                    id: 'filterTransactionCustomer',
                    param: 'customer_name'
                },
                {
                    id: 'filterTransactionFleetType',
                    param: 'fleet_type_ids',
                    multiple: true
                },
                {
                    id: 'filterTransactionLocation',
                    param: 'location_ids',
                    multiple: true
                },
                {
                    id: 'filterTransactionDateStart',
                    param: 'transaction_date_start'
                },
                {
                    id: 'filterTransactionDateEnd',
                    param: 'transaction_date_end'
                },
                {
                    id: 'filterTransactionStatus',
                    param: 'efficiency_status'
                }
            ];

            function buildUrl() {
                var params = new URLSearchParams();

                FILTER_FIELDS.forEach(function(field) {
                    var el = document.getElementById(field.id);

                    if (!el) {
                        return;
                    }

                    if (field.multiple) {
                        Array.from(el.selectedOptions || []).forEach(function(option) {
                            if (option.value !== '') {
                                params.append(field.param + '[]', option.value);
                            }
                        });

                        return;
                    }

                    var value = (el.value || '').trim();

                    if (value !== '') {
                        params.set(field.param, value);
                    }
                });

                var qs = params.toString();
                return qs ? (BASE_URL + '?' + qs) : BASE_URL;
            }

            function syncFilterState() {
                var hasActiveFilter = false;

                FILTER_FIELDS.forEach(function(field) {
                    var el = document.getElementById(field.id);

                    if (!el) {
                        return;
                    }

                    var active = field.multiple
                        ? Array.from(el.selectedOptions || []).some(function(option) {
                            return option.value !== '';
                        })
                        : (el.value || '').trim() !== '';
                    hasActiveFilter = hasActiveFilter || active;
                    el.classList.toggle('transaction-filter-control--active', active);
                });

                var resetBtn = document.getElementById('resetFleetTransactionFilter');
                resetBtn.style.display = hasActiveFilter ? '' : 'none';
            }

            document.addEventListener('DOMContentLoaded', function() {
                var tableEl = document.getElementById('fleetTransactionTable');

                if (!tableEl) {
                    return;
                }

                tableEl.addEventListener('datatable:ready', function(e) {
                    var dt = e.detail;
                    var applyBtn = document.getElementById('applyFleetTransactionFilter');
                    var resetBtn = document.getElementById('resetFleetTransactionFilter');
                    var filterBar = document.getElementById('fleetTransactionFilterBar');

                    function applyFilter() {
                        syncFilterState();
                        dt.ajax.url(buildUrl()).load();
                    }

                    applyBtn.addEventListener('click', applyFilter);

                    resetBtn.addEventListener('click', function() {
                        FILTER_FIELDS.forEach(function(field) {
                            var el = document.getElementById(field.id);
                            if (el) {
                                if (field.multiple) {
                                    Array.from(el.options || []).forEach(function(option) {
                                        option.selected = false;
                                    });

                                    if (window.jQuery) {
                                        window.jQuery(el).val(null).trigger('change');
                                    }
                                } else {
                                    el.value = '';
                                }
                            }
                        });

                        applyFilter();
                    });

                    filterBar.addEventListener('keydown', function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            applyFilter();
                        }
                    });

                    syncFilterState();
                });
            });
        })();
    </script>
@endpush
