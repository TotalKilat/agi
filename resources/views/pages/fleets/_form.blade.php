{{-- Fleet Form Partial -- follows frontend.md §5 --}}
@php
  $isEdit = isset($fleet) && $fleet->exists;
  $isActive = old('is_active', $fleet->is_active ?? true);
  $selectedCustomer = old('customer_id', $fleet->customer_id ?? '');
@endphp

<div class="form-grid">
  <div class="form-group">
    <label for="vehicle_name" class="form-label">Vehicle Name</label>
    <input type="text" name="vehicle_name" id="vehicle_name" class="form-input @error('vehicle_name') form-input-error @enderror" value="{{ old('vehicle_name', $fleet->vehicle_name ?? '') }}" maxlength="200" required>
    @error('vehicle_name') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="device_name" class="form-label">Device Name</label>
    <input type="text" name="device_name" id="device_name" class="form-input @error('device_name') form-input-error @enderror" value="{{ old('device_name', $fleet->device_name ?? '') }}" maxlength="200" required>
    @error('device_name') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="customer_id" class="form-label">Customer</label>
    <select name="customer_id" id="customer_id" class="form-select js-select2 @error('customer_id') form-input-error @enderror" data-placeholder="Select a customer..." required>
      <option value="">Select a customer...</option>
      @foreach($customers as $customer)
        <option value="{{ $customer->id }}" @selected($selectedCustomer === $customer->id)>{{ $customer->name }} ({{ $customer->username }})</option>
      @endforeach
    </select>
    @error('customer_id') <div class="form-error">{{ $message }}</div> @enderror
  </div>
</div>

<div class="form-group form-group-switch">
  <label class="form-label">Status</label>
  <label class="check-control">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked($isActive)>
    <span>
      <strong>Active</strong>
      <small>This fleet is currently operational.</small>
    </span>
  </label>
</div>
