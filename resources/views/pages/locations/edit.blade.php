@extends('layouts.app')

@section('title', 'Edit Location')
@section('page-title', 'Edit Location')
@section('crud-assets', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Edit Location</h1>
        <p class="page-header-subtitle">Update location details for {{ $location->name }}</p>
      </div>
      <a href="{{ route('locations.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('locations.update', $location) }}" class="card form-card" id="locationForm">
      @csrf
      @method('PUT')
      @include('pages.locations._form')
      <div class="form-actions">
        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection
