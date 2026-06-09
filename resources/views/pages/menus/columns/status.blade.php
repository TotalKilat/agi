<x-badge :type="$menu->is_active ? 'success' : 'neutral'">
  {{ $menu->is_active ? 'Active' : 'Inactive' }}
</x-badge>
