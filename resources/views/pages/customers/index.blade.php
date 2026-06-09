@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
<div class="page-section active" id="customerIndexPage">
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Customers</h1>
        <p class="page-header-subtitle">Manage registered customers and their credentials</p>
      </div>
      <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Customer
      </a>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <h3 class="card-title">All Customers</h3>
          <p class="card-subtitle">{{ $customers->total() }} customer(s) registered</p>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th></th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Location</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($customers as $customer)
              <tr>
                <td>
                  <div class="table-actions">
                    <a href="{{ route('customers.edit', $customer) }}" class="table-action-btn" title="Edit">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete customer &quot;{{ $customer->name }}&quot;?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="table-action-btn" title="Delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                      </button>
                    </form>
                  </div>
                </td>
                <td>
                  <div class="agent-name">{{ $customer->name }}</div>
                </td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone ?? '—' }}</td>
                <td>{{ collect([$customer->city, $customer->country])->filter()->join(', ') ?: '—' }}</td>
                <td><x-badge :type="$customer->is_active ? 'success' : 'neutral'">{{ $customer->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
              </tr>
            @empty
              <tr>
                <td colspan="6">
                  <div class="empty-state">
                    <div class="empty-state-icon">
                      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div class="empty-state-title">No customers yet</div>
                    <div class="empty-state-desc">Create your first customer to get started.</div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <span>Showing <strong>{{ $customers->firstItem() ?? 0 }}</strong> to <strong>{{ $customers->lastItem() ?? 0 }}</strong> of <strong>{{ $customers->total() }}</strong> customers</span>
        {{ $customers->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
