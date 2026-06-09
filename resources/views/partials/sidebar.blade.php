{{-- ============================================================
     SIDEBAR PARTIAL
     ============================================================ --}}
<aside class="sidebar" id="sidebar">

  {{-- Brand --}}
  <div class="sidebar-brand">
    <div class="sidebar-brand-icon">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
        <path d="M2 17l10 5 10-5"/>
        <path d="M2 12l10 5 10-5"/>
      </svg>
    </div>
    <span class="sidebar-brand-text">Agentix</span>
  </div>

  {{-- Navigation --}}
  <nav class="sidebar-nav">
    @forelse(($sidebarMenus ?? collect()) as $section => $menus)
      <div class="sidebar-nav-section">{{ $section }}</div>

      @foreach($menus as $menu)
        <x-nav-item
          :href="$menu->destinationUrl()"
          :icon="$menu->icon"
          :active="$menu->isCurrent() || $menu->children->contains(fn ($child) => $child->isCurrent())"
          :target="$menu->target"
        >
          {{ $menu->name }}
        </x-nav-item>

        @foreach($menu->children as $child)
          <x-nav-item
            :href="$child->destinationUrl()"
            :icon="$child->icon"
            :active="$child->isCurrent()"
            :target="$child->target"
            :child="true"
          >
            {{ $child->name }}
          </x-nav-item>
        @endforeach
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
