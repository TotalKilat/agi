<x-crud-actions
  :edit-url="route('fleet-types.edit', $fleetType)"
  :delete-url="route('fleet-types.destroy', $fleetType)"
  record-label="fleet type"
  :record-name="$fleetType->name"
/>
