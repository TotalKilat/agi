@php
  $selectedParent = old('parent_id', $menu->parent_id);
  $selectedIcon = old('icon', $menu->icon);
  $selectedTarget = old('target', $menu->target ?: '_self');
  $isActive = old('is_active', $menu->exists ? $menu->is_active : true);
@endphp

<div class="form-grid">
  <div class="form-group">
    <label for="name" class="form-label">Menu Name</label>
    <input id="name" type="text" name="name" class="form-input @error('name') form-input-error @enderror" value="{{ old('name', $menu->name) }}" maxlength="100" required>
    @error('name') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="section" class="form-label">Section</label>
    <input id="section" type="text" name="section" class="form-input @error('section') form-input-error @enderror" value="{{ old('section', $menu->section) }}" placeholder="Main Menu" maxlength="100" required>
    @error('section') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="parent_id" class="form-label">Parent Menu</label>
    <select id="parent_id" name="parent_id" class="form-select js-select2 @error('parent_id') form-input-error @enderror" data-placeholder="None (top-level menu)">
      <option value="">None (top-level menu)</option>
      @foreach($parentOptions as $parent)
        <option value="{{ $parent->id }}" @selected($selectedParent === $parent->id)>
          {{ $parent->section }} / {{ $parent->name }}
        </option>
      @endforeach
    </select>
    <div class="form-hint">Only one child level is supported.</div>
    @error('parent_id') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="icon" class="form-label">Icon</label>
    <select id="icon" name="icon" class="form-select js-select2 @error('icon') form-input-error @enderror" required>
      @foreach($icons as $icon)
        <option value="{{ $icon }}" @selected($selectedIcon === $icon)>{{ ucfirst($icon) }}</option>
      @endforeach
    </select>
    @error('icon') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="route_name" class="form-label">Route Name</label>
    <input id="route_name" type="text" name="route_name" class="form-input @error('route_name') form-input-error @enderror" value="{{ old('route_name', $menu->route_name) }}" placeholder="e.g. dashboard or menus.index" maxlength="150">
    <div class="form-hint">Preferred for internal Laravel pages.</div>
    @error('route_name') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="url" class="form-label">Custom URL</label>
    <input id="url" type="text" name="url" class="form-input @error('url') form-input-error @enderror" value="{{ old('url', $menu->url) }}" placeholder="/documentation or https://example.com" maxlength="2048">
    <div class="form-hint">Required only when Route Name is empty.</div>
    @error('url') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="active_pattern" class="form-label">Active Route Pattern</label>
    <input id="active_pattern" type="text" name="active_pattern" class="form-input @error('active_pattern') form-input-error @enderror" value="{{ old('active_pattern', $menu->active_pattern) }}" placeholder="e.g. menus.*" maxlength="150">
    <div class="form-hint">Supports Laravel route wildcards.</div>
    @error('active_pattern') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="sort_order" class="form-label">Sort Order</label>
    <input id="sort_order" type="number" name="sort_order" class="form-input @error('sort_order') form-input-error @enderror" value="{{ old('sort_order', $menu->sort_order ?? 0) }}" min="0" required>
    @error('sort_order') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label for="target" class="form-label">Link Target</label>
    <select id="target" name="target" class="form-select js-select2 @error('target') form-input-error @enderror" required>
      <option value="_self" @selected($selectedTarget === '_self')>Same tab</option>
      <option value="_blank" @selected($selectedTarget === '_blank')>New tab</option>
    </select>
    @error('target') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group form-group-switch">
    <label class="form-label">Visibility</label>
    <label class="check-control">
      <input type="checkbox" name="is_active" value="1" @checked($isActive)>
      <span>
        <strong>Active</strong>
        <small>Show this menu in the sidebar.</small>
      </span>
    </label>
    @error('is_active') <div class="form-error">{{ $message }}</div> @enderror
  </div>
</div>
