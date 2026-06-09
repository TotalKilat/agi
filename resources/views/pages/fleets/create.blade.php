@extends('layouts.app')

@section('title', 'Create Fleet')
@section('page-title', 'Create Fleet')
@section('crud-assets', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Create Fleet</h1>
        <p class="page-header-subtitle">Register a new vehicle and assign a tracking device</p>
      </div>
      <a href="{{ route('fleets.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('fleets.store') }}" class="card form-card" id="fleetForm">
      @csrf
      @include('pages.fleets._form', ['fleet' => null])
      <div class="form-actions">
        <a href="{{ route('fleets.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Fleet</button>
      </div>
    </form>
  </div>
</div>
@endsection
