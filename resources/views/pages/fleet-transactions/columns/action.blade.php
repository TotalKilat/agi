<x-crud-actions
  :edit-url="route('fleet-transactions.edit', $transaction)"
  :delete-url="route('fleet-transactions.destroy', $transaction)"
  record-label="transaction"
  :record-name="$transaction->vehicle_name_snapshot.' - '.$transaction->transaction_date?->toDateString()"
/>
