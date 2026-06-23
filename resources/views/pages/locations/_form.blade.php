{{-- Location Form Partial -- follows frontend.md CRUD structure --}}
<div class="form-group">
  <label for="name" class="form-label">Name</label>
  <input type="text" name="name" id="name" class="form-input @error('name') form-input-error @enderror" value="{{ old('name', $location->name ?? '') }}" maxlength="200" required>
  @error('name') <div class="form-error">{{ $message }}</div> @enderror
</div>
