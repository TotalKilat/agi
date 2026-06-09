{{-- Sidebar Navigation Item --}}
@props([
    'href' => '#',
    'icon' => 'dashboard',
    'active' => false,
    'badge' => null,
    'target' => '_self',
    'child' => false,
])

<a href="{{ $href }}"
   target="{{ $target }}"
   @if($target === '_blank') rel="noopener noreferrer" @endif
   class="sidebar-nav-item @if($active) active @endif @if($child) sidebar-nav-child @endif">
  <span class="sidebar-nav-icon">
    <x-menu-icon :name="$icon" />
  </span>
  <span class="sidebar-nav-label">{{ $slot }}</span>
  @if($badge !== null)
    <span class="sidebar-nav-badge">{{ $badge }}</span>
  @endif
</a>
