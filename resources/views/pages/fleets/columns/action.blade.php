<x-crud-actions
  :edit-url="route('fleets.edit', $fleet)"
  :delete-url="route('fleets.destroy', $fleet)"
  record-label="fleet"
  :record-name="$fleet->vehicle_name"
/>
