@extends('layouts.app')

@section('title', 'Create Fleet Transaction')
@section('page-title', 'Create Fleet Transaction')
@section('crud-assets', 'true')

@section('content')
<div class="page-section active">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Create Fleet Transaction</h1>
        <p class="page-header-subtitle">Add one daily fuel performance transaction manually</p>
      </div>
      <a href="{{ route('fleet-transactions.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('fleet-transactions.store') }}" class="card form-card">
      @csrf
      @include('pages.fleet-transactions._form', ['transaction' => null])
      <div class="form-actions">
        <a href="{{ route('fleet-transactions.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Transaction</button>
      </div>
    </form>
  </div>
</div>
@endsection
