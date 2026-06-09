@extends('layouts.app')

@section('title', 'Edit Fleet')
@section('page-title', 'Edit Fleet')
@section('crud-assets', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Edit Fleet</h1>
        <p class="page-header-subtitle">Update fleet details for {{ $fleet->vehicle_name }}</p>
      </div>
      <a href="{{ route('fleets.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('fleets.update', $fleet) }}" class="card form-card" id="fleetForm">
      @csrf
      @method('PUT')
      @include('pages.fleets._form')
      <div class="form-actions">
        <a href="{{ route('fleets.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection
