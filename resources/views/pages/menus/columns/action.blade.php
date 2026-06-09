<x-crud-actions
  :edit-url="route('menus.edit', $menu)"
  :delete-url="route('menus.destroy', $menu)"
  record-label="menu"
  :record-name="$menu->name"
/>
