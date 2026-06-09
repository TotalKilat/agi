@extends('layouts.app')

@section('title', 'Create Menu')
@section('page-title', 'Create Menu')
@section('sweetalert-feedback', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Create Menu</h1>
        <p class="page-header-subtitle">Add a new database-driven sidebar entry</p>
      </div>
      <a href="{{ route('menus.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('menus.store') }}" class="card menu-form-card" id="menuForm">
      @csrf
      @include('pages.menus._form')
      <div class="form-actions">
        <a href="{{ route('menus.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Menu</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
  @vite('resources/js/menu.js')
@endpush
