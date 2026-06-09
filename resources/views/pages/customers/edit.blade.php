@extends('layouts.app')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
<div class="page-section active">
  <div class="page-container">

    <div class="page-header">
      <div>
        <h1 class="page-header-title">Edit Customer</h1>
        <p class="page-header-subtitle">Update customer details for {{ $customer->name }}</p>
      </div>
      <a href="{{ route('customers.index') }}" class="btn btn-ghost btn-sm">← Back to Customers</a>
    </div>

    <div class="card" style="max-width: 720px;">
      @include('pages.customers._form', ['customer' => $customer])
    </div>

  </div>
</div>
@endsection
