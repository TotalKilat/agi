@extends('layouts.app')

@section('title', 'Edit Fleet Transaction')
@section('page-title', 'Edit Fleet Transaction')
@section('crud-assets', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Edit Fleet Transaction</h1>
        <p class="page-header-subtitle">Update transaction data for {{ $transaction->vehicle_name_snapshot }}</p>
      </div>
      <a href="{{ route('fleet-transactions.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('fleet-transactions.update', $transaction) }}" class="card form-card">
      @csrf
      @method('PUT')
      @include('pages.fleet-transactions._form')
      <div class="form-actions">
        <a href="{{ route('fleet-transactions.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection
