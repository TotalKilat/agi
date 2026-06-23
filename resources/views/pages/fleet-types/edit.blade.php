@extends('layouts.app')

@section('title', 'Edit Fleet Type')
@section('page-title', 'Edit Fleet Type')
@section('crud-assets', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Edit Fleet Type</h1>
        <p class="page-header-subtitle">Update fleet type details for {{ $fleetType->name }}</p>
      </div>
      <a href="{{ route('fleet-types.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('fleet-types.update', $fleetType) }}" class="card form-card" id="fleetTypeForm">
      @csrf
      @method('PUT')
      @include('pages.fleet-types._form')
      <div class="form-actions">
        <a href="{{ route('fleet-types.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection
