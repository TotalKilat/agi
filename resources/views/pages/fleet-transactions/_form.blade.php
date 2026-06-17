@php
  $selectedFleet = old('fleet_id', $transaction->fleet_id ?? '');
  $transactionDate = old(
      'transaction_date',
      isset($transaction?->transaction_date) ? $transaction->transaction_date->format('Y-m-d') : ''
  );
@endphp

<div class="form-grid">
  <div class="form-group">
    <label for="fleet_id" class="form-label">Fleet</label>
    <select
      name="fleet_id"
      id="fleet_id"
      class="form-select js-select2 @error('fleet_id') form-input-error @enderror"
      data-placeholder="Select a fleet..."
      required
    >
      <option value="">Select a fleet...</option>
      @foreach($fleets as $fleet)
        <option value="{{ $fleet->id }}" @selected($selectedFleet === $fleet->id)>
          {{ $fleet->vehicle_name }} ({{ $fleet->device_name }}) - {{ $fleet->customer?->name ?? 'No Customer' }}
        </option>
      @endforeach
    </select>
    @error('fleet_id') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="transaction_date" class="form-label">Transaction Date</label>
    <input type="date" name="transaction_date" id="transaction_date" class="form-input @error('transaction_date') form-input-error @enderror" value="{{ $transactionDate }}" required>
    @error('transaction_date') <div class="form-error">{{ $message }}</div> @enderror
  </div>
</div>

<div class="form-grid">
  <div class="form-group">
    <label for="odometer_km" class="form-label">Odometer (Km)</label>
    <input type="number" step="0.01" min="0" name="odometer_km" id="odometer_km" class="form-input @error('odometer_km') form-input-error @enderror" value="{{ old('odometer_km', $transaction->odometer_km ?? 0) }}" required>
    @error('odometer_km') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="usage_l" class="form-label">Usage (L)</label>
    <input type="number" step="0.01" min="0" name="usage_l" id="usage_l" class="form-input @error('usage_l') form-input-error @enderror" value="{{ old('usage_l', $transaction->usage_l ?? 0) }}" required>
    @error('usage_l') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="cost_rp" class="form-label">Cost (Rp)</label>
    <input type="number" step="0.01" min="0" name="cost_rp" id="cost_rp" class="form-input @error('cost_rp') form-input-error @enderror" value="{{ old('cost_rp', $transaction->cost_rp ?? 0) }}" required>
    @error('cost_rp') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="idle_usage_l" class="form-label">Idle Usage (L)</label>
    <input type="number" step="0.01" min="0" name="idle_usage_l" id="idle_usage_l" class="form-input @error('idle_usage_l') form-input-error @enderror" value="{{ old('idle_usage_l', $transaction->idle_usage_l ?? '') }}">
    @error('idle_usage_l') <div class="form-error">{{ $message }}</div> @enderror
  </div>
</div>

<div class="form-grid">
  <div class="form-group">
    <label for="initial_volume_l" class="form-label">Initial Volume (L)</label>
    <input type="number" step="0.01" min="0" name="initial_volume_l" id="initial_volume_l" class="form-input @error('initial_volume_l') form-input-error @enderror" value="{{ old('initial_volume_l', $transaction->initial_volume_l ?? '') }}">
    @error('initial_volume_l') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="final_volume_l" class="form-label">Final Volume (L)</label>
    <input type="number" step="0.01" min="0" name="final_volume_l" id="final_volume_l" class="form-input @error('final_volume_l') form-input-error @enderror" value="{{ old('final_volume_l', $transaction->final_volume_l ?? '') }}">
    @error('final_volume_l') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="refuel_l" class="form-label">Refuel (L)</label>
    <input type="number" step="0.001" min="0" name="refuel_l" id="refuel_l" class="form-input @error('refuel_l') form-input-error @enderror" value="{{ old('refuel_l', $transaction->refuel_l ?? '') }}">
    @error('refuel_l') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="refuel_times" class="form-label">Refuel Times</label>
    <input type="number" step="1" min="0" name="refuel_times" id="refuel_times" class="form-input @error('refuel_times') form-input-error @enderror" value="{{ old('refuel_times', $transaction->refuel_times ?? '') }}">
    @error('refuel_times') <div class="form-error">{{ $message }}</div> @enderror
  </div>
</div>

<div class="form-grid">
  <div class="form-group">
    <label for="km_per_l" class="form-label">1 Km / L</label>
    <input type="number" step="0.0001" min="0" name="km_per_l" id="km_per_l" class="form-input @error('km_per_l') form-input-error @enderror" value="{{ old('km_per_l', $transaction->km_per_l ?? '') }}">
    @error('km_per_l') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="l_per_km" class="form-label">1 L / Km</label>
    <input type="number" step="0.0001" min="0" name="l_per_km" id="l_per_km" class="form-input @error('l_per_km') form-input-error @enderror" value="{{ old('l_per_km', $transaction->l_per_km ?? '') }}">
    @error('l_per_km') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="cost_per_km" class="form-label">1 Km / Cost</label>
    <input type="number" step="0.0001" min="0" name="cost_per_km" id="cost_per_km" class="form-input @error('cost_per_km') form-input-error @enderror" value="{{ old('cost_per_km', $transaction->cost_per_km ?? '') }}">
    @error('cost_per_km') <div class="form-error">{{ $message }}</div> @enderror
  </div>
</div>

<div class="form-grid">
  <div class="form-group">
    <label for="running_duration_seconds" class="form-label">Running Duration (Seconds)</label>
    <input type="number" step="1" min="0" name="running_duration_seconds" id="running_duration_seconds" class="form-input @error('running_duration_seconds') form-input-error @enderror" value="{{ old('running_duration_seconds', $transaction->running_duration_seconds ?? '') }}">
    @error('running_duration_seconds') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="idle_duration_seconds" class="form-label">Idle Duration (Seconds)</label>
    <input type="number" step="1" min="0" name="idle_duration_seconds" id="idle_duration_seconds" class="form-input @error('idle_duration_seconds') form-input-error @enderror" value="{{ old('idle_duration_seconds', $transaction->idle_duration_seconds ?? '') }}">
    @error('idle_duration_seconds') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="stop_duration_seconds" class="form-label">Stop Duration (Seconds)</label>
    <input type="number" step="1" min="0" name="stop_duration_seconds" id="stop_duration_seconds" class="form-input @error('stop_duration_seconds') form-input-error @enderror" value="{{ old('stop_duration_seconds', $transaction->stop_duration_seconds ?? '') }}">
    @error('stop_duration_seconds') <div class="form-error">{{ $message }}</div> @enderror
  </div>
</div>
