@extends('layouts.app')

@section('title', 'Locations')
@section('page-title', 'Locations')
@section('crud-assets', 'true')

@section('content')
<div
  class="page-section active js-crud-page"
  id="locationIndexPage"
  data-csrf-token="{{ csrf_token() }}"
  data-success-message="{{ session('success') }}"
  data-info-message="{{ session('info') }}"
>
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Locations</h1>
        <p class="page-header-subtitle">Manage fleet locations used by vehicle records</p>
      </div>
      <a href="{{ route('locations.create') }}" class="btn btn-primary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Location
      </a>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <h3 class="card-title">All Locations</h3>
          <p class="card-subtitle">Maintain location names and see how many fleets use each location.</p>
        </div>
      </div>

      <div class="data-table-container">
        <table
          class="table js-data-table"
          id="locationTable"
          data-url="{{ route('locations.data') }}"
          data-order='[[2,"asc"]]'
          data-plural-label="locations"
        >
          <thead>
            <tr>
              <th data-column="row_number" data-orderable="false" data-searchable="false">No</th>
              <th data-column="action" data-orderable="false" data-searchable="false"></th>
              <th data-column="name">Name</th>
              <th data-column="fleets_count" data-name="fleets_count" data-searchable="false" data-align="center">Fleets</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
