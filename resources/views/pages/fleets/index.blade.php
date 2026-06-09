@extends('layouts.app')

@section('title', 'Fleets')
@section('page-title', 'Fleets')
@section('crud-assets', 'true')

@section('content')
<div
  class="page-section active js-crud-page"
  id="fleetIndexPage"
  data-csrf-token="{{ csrf_token() }}"
  data-success-message="{{ session('success') }}"
  data-info-message="{{ session('info') }}"
>
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Fleets</h1>
        <p class="page-header-subtitle">Manage vehicle fleets and their tracking devices</p>
      </div>
      <a href="{{ route('fleets.create') }}" class="btn btn-primary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Fleet
      </a>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <h3 class="card-title">All Fleets</h3>
          <p class="card-subtitle">View and manage vehicle-device assignments.</p>
        </div>
      </div>

      <div class="data-table-container">
        <table
          class="table js-data-table"
          id="fleetTable"
          data-url="{{ route('fleets.data') }}"
          data-order='[[1,"asc"]]'
          data-plural-label="fleets"
        >
          <thead>
            <tr>
              <th data-column="action" data-orderable="false" data-searchable="false"></th>
              <th data-column="vehicle_name">Vehicle Name</th>
              <th data-column="device_name">Device Name</th>
              <th data-column="customer_name" data-orderable="false">Customer</th>
              <th data-column="status" data-name="is_active">Status</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
