@props([
    'active',
    'activeLabel' => 'Active',
    'inactiveLabel' => 'Inactive',
])

<x-badge :type="$active ? 'success' : 'neutral'">
  {{ $active ? $activeLabel : $inactiveLabel }}
</x-badge>
