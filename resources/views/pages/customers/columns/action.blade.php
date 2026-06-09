<x-crud-actions
  :edit-url="route('customers.edit', $customer)"
  :delete-url="route('customers.destroy', $customer)"
  record-label="customer"
  :record-name="$customer->name"
/>
