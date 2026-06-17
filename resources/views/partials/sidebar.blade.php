{{-- ============================================================
     SIDEBAR PARTIAL
     ============================================================ --}}
<aside class="sidebar" id="sidebar">

  {{-- Brand --}}
  <div class="sidebar-brand">
    <div class="sidebar-brand-icon">
      @php
        $logoUrl = app(\App\Services\SettingService::class)->url('app_logo');
      @endphp
      @if ($logoUrl)
        <img src="{{ $logoUrl }}" alt="{{ $appSettings['app_name'] ?? 'Agentix' }}" style="width:20px; height:20px; object-fit:contain;">
      @else
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2L2 7l10 5 10-5-10-5z"/>
          <path d="M2 17l10 5 10-5"/>
          <path d="M2 12l10 5 10-5"/>
        </svg>
      @endif
    </div>
    <span class="sidebar-brand-text">{{ $appSettings['app_name'] ?? 'Agentix' }}</span>
  </div>

  {{-- Navigation --}}
  <nav class="sidebar-nav">
    @forelse(($sidebarMenus ?? collect()) as $section => $menus)
      <div class="sidebar-nav-section">{{ $section }}</div>

      @foreach($menus as $menu)
        <x-nav-item
          :href="$menu->destinationUrl()"
          :icon="$menu->icon"
          :active="$menu->isCurrent()"
          :target="$menu->target"
          :disabled="$menu->destinationUrl() === '#'"
        >
          {{ $menu->name }}
        </x-nav-item>
      @endforeach
    @empty
      <div class="sidebar-empty">
        No active menu is configured.
      </div>
    @endforelse
  </nav>

  {{-- Sidebar Footer --}}
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-user-avatar">
        {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'AK' }}
      </div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name">{{ Auth::user()->name ?? 'Alex Kim' }}</div>
        <div class="sidebar-user-role">{{ Auth::user()->role ?? 'Administrator' }}</div>
      </div>
    </div>
  </div>
</aside>
