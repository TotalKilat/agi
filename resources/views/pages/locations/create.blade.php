@extends('layouts.app')

@section('title', 'Create Location')
@section('page-title', 'Create Location')
@section('crud-assets', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Create Location</h1>
        <p class="page-header-subtitle">Add a location that can be assigned to fleets</p>
      </div>
      <a href="{{ route('locations.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('locations.store') }}" class="card form-card" id="locationForm">
      @csrf
      @include('pages.locations._form', ['location' => null])
      <div class="form-actions">
        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Location</button>
      </div>
    </form>
  </div>
</div>
@endsection
