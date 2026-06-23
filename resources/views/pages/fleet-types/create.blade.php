@extends('layouts.app')

@section('title', 'Create Fleet Type')
@section('page-title', 'Create Fleet Type')
@section('crud-assets', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Create Fleet Type</h1>
        <p class="page-header-subtitle">Add a category that can be assigned to fleets</p>
      </div>
      <a href="{{ route('fleet-types.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('fleet-types.store') }}" class="card form-card" id="fleetTypeForm">
      @csrf
      @include('pages.fleet-types._form', ['fleetType' => null])
      <div class="form-actions">
        <a href="{{ route('fleet-types.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Fleet Type</button>
      </div>
    </form>
  </div>
</div>
@endsection
