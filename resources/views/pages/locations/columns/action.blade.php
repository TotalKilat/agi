<x-crud-actions
  :edit-url="route('locations.edit', $location)"
  :delete-url="route('locations.destroy', $location)"
  record-label="location"
  :record-name="$location->name"
/>
