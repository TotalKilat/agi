@extends('layouts.app')

@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu')
@section('crud-assets', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Edit Menu</h1>
        <p class="page-header-subtitle">Update {{ $menu->name }} and its sidebar behavior</p>
      </div>
      <a href="{{ route('menus.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('menus.update', $menu) }}" class="card form-card" id="menuForm">
      @csrf
      @method('PUT')
      @include('pages.menus._form')
      <div class="form-actions">
        <a href="{{ route('menus.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection
