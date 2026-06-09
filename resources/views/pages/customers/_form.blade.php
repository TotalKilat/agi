{{-- Customer Form Partial --}}
@php
  $isEdit = isset($customer) && $customer->exists;
@endphp

<form method="POST" action="{{ $isEdit ? route('customers.update', $customer) : route('customers.store') }}" style="padding: 24px;">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
    <div class="form-group">
      <label class="form-label" for="name">Full Name</label>
      <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $customer->name ?? '') }}" required maxlength="200">
      @error('name') <span class="form-hint" style="color: var(--color-danger)">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
      <label class="form-label" for="email">Email Address</label>
      <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $customer->email ?? '') }}" required maxlength="200">
      @error('email') <span class="form-hint" style="color: var(--color-danger)">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
      <label class="form-label" for="password">{{ $isEdit ? 'New Password (leave blank to keep)' : 'Password' }}</label>
      <input type="password" name="password" id="password" class="form-input" minlength="8" maxlength="255" {{ $isEdit ? '' : 'required' }}>
      @error('password') <span class="form-hint" style="color: var(--color-danger)">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
      <label class="form-label" for="phone">Phone</label>
      <input type="text" name="phone" id="phone" class="form-input" value="{{ old('phone', $customer->phone ?? '') }}" maxlength="30">
      @error('phone') <span class="form-hint" style="color: var(--color-danger)">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
      <label class="form-label" for="address">Address</label>
      <input type="text" name="address" id="address" class="form-input" value="{{ old('address', $customer->address ?? '') }}" maxlength="500">
      @error('address') <span class="form-hint" style="color: var(--color-danger)">{{ $message }}</span> @enderror
    </div>

    <div class="form-group" style="grid-template-columns: 1fr 1fr; gap: 16px;">
      <div>
        <label class="form-label" for="city">City</label>
        <input type="text" name="city" id="city" class="form-input" value="{{ old('city', $customer->city ?? '') }}" maxlength="100">
      </div>
      <div style="margin-top: 16px;">
        <label class="form-label" for="state">State / Province</label>
        <input type="text" name="state" id="state" class="form-input" value="{{ old('state', $customer->state ?? '') }}" maxlength="100">
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div class="form-group">
        <label class="form-label" for="postal_code">Postal Code</label>
        <input type="text" name="postal_code" id="postal_code" class="form-input" value="{{ old('postal_code', $customer->postal_code ?? '') }}" maxlength="20">
      </div>
      <div class="form-group">
        <label class="form-label" for="country">Country</label>
        <input type="text" name="country" id="country" class="form-input" value="{{ old('country', $customer->country ?? '') }}" maxlength="100">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label class="form-label" for="notes">Notes</label>
    <textarea name="notes" id="notes" class="form-textarea" maxlength="2000" placeholder="Internal notes about this customer...">{{ old('notes', $customer->notes ?? '') }}</textarea>
    @error('notes') <span class="form-hint" style="color: var(--color-danger)">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="toggle" style="display: inline-flex; align-items: center; gap: 10px;">
      <input type="hidden" name="is_active" value="0">
      <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $customer->is_active ?? true) ? 'checked' : '' }}>
      <span class="toggle-slider"></span>
      <span class="form-label" style="margin-bottom: 0;">Active Account</span>
    </label>
  </div>

  <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid var(--color-border-light); margin-top: 8px;">
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Customer' : 'Create Customer' }}</button>
  </div>
</form>
