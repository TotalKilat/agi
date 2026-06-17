@php
    $logoUrl = app(\App\Services\SettingService::class)->url('app_logo');
    $faviconUrl = app(\App\Services\SettingService::class)->url('app_favicon');
@endphp

{{-- Branding Section --}}
<div class="settings-section">
    <h3 class="settings-section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:-3px; margin-right:6px;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        Branding
    </h3>
    <div class="settings-card">

        {{-- App Name --}}
        <div class="form-group">
            <label for="app_name" class="form-label">Application Name</label>
            <input
                type="text"
                name="app_name"
                id="app_name"
                class="form-input @error('app_name') form-input-error @enderror"
                value="{{ old('app_name', $settings['app_name'] ?? 'Agentix') }}"
                maxlength="100"
                required
            >
            @error('app_name')
                <div class="form-error">{{ $message }}</div>
            @enderror
            <div class="form-hint">Displayed in the sidebar, browser tab, and notification emails.</div>
        </div>

        {{-- App Description --}}
        <div class="form-group">
            <label for="app_description" class="form-label">Tagline / Description</label>
            <input
                type="text"
                name="app_description"
                id="app_description"
                class="form-input @error('app_description') form-input-error @enderror"
                value="{{ old('app_description', $settings['app_description'] ?? '') }}"
                maxlength="255"
            >
            @error('app_description')
                <div class="form-error">{{ $message }}</div>
            @enderror
            <div class="form-hint">A short tagline shown below the application name.</div>
        </div>
    </div>
</div>

{{-- Logo & Favicon Section --}}
<div class="settings-section">
    <h3 class="settings-section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:-3px; margin-right:6px;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        Logo &amp; Icons
    </h3>
    <div class="settings-card">

        {{-- App Logo --}}
        <div class="form-group">
            <label for="app_logo" class="form-label">Application Logo</label>
            <div style="display:flex; align-items:flex-start; gap:20px; flex-wrap:wrap;">
                <div class="logo-preview-box" id="logoPreviewBox">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo Preview" class="logo-preview-img">
                    @else
                        <div class="logo-preview-placeholder">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#B0A098" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span>No logo</span>
                        </div>
                    @endif
                </div>
                <div style="flex:1; min-width:200px;">
                    <input
                        type="file"
                        name="app_logo"
                        id="app_logo"
                        class="form-input @error('app_logo') form-input-error @enderror"
                        accept="image/png,image/jpeg,image/svg+xml,image/webp"
                        onchange="previewImage(this, 'logoPreviewBox')"
                    >
                    @error('app_logo')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">Recommended: PNG or SVG, max 2 MB. Used in the sidebar header.</div>
                    @if ($logoUrl)
                        <label style="margin-top:8px; display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                            <input type="checkbox" name="remove_logo" value="1" style="accent-color:var(--color-danger);">
                            <span style="color:var(--color-danger);">Remove current logo</span>
                        </label>
                    @endif
                </div>
            </div>
        </div>

        {{-- Favicon --}}
        <div class="form-group">
            <label for="app_favicon" class="form-label">Favicon</label>
            <div style="display:flex; align-items:flex-start; gap:20px; flex-wrap:wrap;">
                <div class="favicon-preview-box" id="faviconPreviewBox">
                    @if ($faviconUrl)
                        <img src="{{ $faviconUrl }}" alt="Favicon Preview" class="favicon-preview-img">
                    @else
                        <div class="favicon-preview-placeholder">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#B0A098" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                            <span>No favicon</span>
                        </div>
                    @endif
                </div>
                <div style="flex:1; min-width:200px;">
                    <input
                        type="file"
                        name="app_favicon"
                        id="app_favicon"
                        class="form-input @error('app_favicon') form-input-error @enderror"
                        accept="image/png,image/x-icon,image/svg+xml"
                        onchange="previewImage(this, 'faviconPreviewBox')"
                    >
                    @error('app_favicon')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">Shown in browser tabs. ICO, PNG, or SVG, max 512 KB.</div>
                    @if ($faviconUrl)
                        <label style="margin-top:8px; display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                            <input type="checkbox" name="remove_favicon" value="1" style="accent-color:var(--color-danger);">
                            <span style="color:var(--color-danger);">Remove current favicon</span>
                        </label>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input, boxId) {
        const box = document.getElementById(boxId);
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            box.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="logo-preview-img">';
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush
