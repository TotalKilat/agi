@extends('layouts.app')

@section('title', 'Create Customer')
@section('page-title', 'Create Customer')

@section('content')
<div class="page-section active">
  <div class="page-container">

    <div class="page-header">
      <div>
        <h1 class="page-header-title">Create Customer</h1>
        <p class="page-header-subtitle">Register a new customer with login credentials</p>
      </div>
      <a href="{{ route('customers.index') }}" class="btn btn-ghost btn-sm">← Back to Customers</a>
    </div>

    <div class="card" style="max-width: 720px;">
      @include('pages.customers._form', ['customer' => null])
    </div>

  </div>
</div>
@endsection
