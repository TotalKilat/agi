<?php

namespace App\Services;

use App\Models\Fleet;
use App\Models\FleetTransaction;
use Carbon\CarbonImmutable;
use DOMDocument;
use DOMElement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FleetTransactionService
{
    public function getDataTableQuery(): Builder
    {
        return FleetTransaction::query()
            ->with(['fleet.customer'])
            ->select('fleet_transactions.*');
    }

    public function create(array $data): FleetTransaction
    {
        $fleet = Fleet::query()->findOrFail($data['fleet_id']);

        return DB::transaction(fn () => FleetTransaction::query()->create([
            ...$data,
            'vehicle_name_snapshot' => $fleet->vehicle_name,
        ]));
    }

    public function update(FleetTransaction $transaction, array $data): FleetTransaction
    {
        $fleet = Fleet::query()->findOrFail($data['fleet_id']);

        return DB::transaction(function () use ($transaction, $data, $fleet): FleetTransaction {
            $transaction->update([
                ...$data,
                'vehicle_name_snapshot' => $fleet->vehicle_name,
            ]);

            return $transaction->fresh(['fleet']);
        });
    }

    public function delete(FleetTransaction $transaction): void
    {
        DB::transaction(fn () => $transaction->delete());
    }

    /**
     * Import a Total Kilat GPS daily performance HTML/XLS export.
     *
     * @return array{created: int, updated: int, unchanged: int, skipped: int}
     */
    public function import(UploadedFile $file): array
    {
        $rows = $this->parseUploadedRows($file);

        if ($rows === []) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file does not contain transaction rows.',
            ]);
        }

        $matchedRows = $this->matchRowsToFleets($rows);
        $now = now();

        return DB::transaction(function () use ($matchedRows, $file, $now): array {
            $summary = [
                'created' => 0,
                'updated' => 0,
                'unchanged' => 0,
                'skipped' => 0,
            ];

            foreach ($matchedRows as $row) {
                $transaction = FleetTransaction::query()
                    ->withTrashed()
                    ->where('fleet_id', $row['fleet_id'])
                    ->whereDate('transaction_date', $row['transaction_date'])
                    ->first();

                $attributes = [
                    ...$row,
                ];
                $auditAttributes = [
                    'source_filename' => $file->getClientOriginalName(),
                    'imported_at' => $now,
                ];

                if (! $transaction) {
                    FleetTransaction::query()->create([
                        ...$attributes,
                        ...$auditAttributes,
                    ]);
                    $summary['created']++;

                    continue;
                }

                if ($transaction->trashed()) {
                    $transaction->restore();
                }

                $transaction->fill($attributes);

                if (! $transaction->isDirty()) {
                    $summary['unchanged']++;

                    continue;
                }

                $transaction->fill($auditAttributes);
                $transaction->save();
                $summary['updated']++;
            }

            return $summary;
        });
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function parseUploadedRows(UploadedFile $file): array
    {
        $contents = (string) file_get_contents($file->getRealPath());
        $tables = $this->extractHtmlTables($contents);
        $dataTable = $tables[1] ?? null;

        if (! $dataTable || count($dataTable) < 2) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file must contain the Daily Performance Analysis table.',
            ]);
        }

        $headers = array_map(fn (string $value): string => $this->normalizeHeader($value), $dataTable[0]);
        $rows = [];

        foreach (array_slice($dataTable, 1) as $line) {
            $record = [];

            foreach ($headers as $index => $header) {
                $record[$header] = $line[$index] ?? null;
            }

            $vehicleName = $this->cleanText($record['device name'] ?? '');
            $dateTime = $this->cleanText($record['date & time'] ?? '');

            if ($vehicleName === '' || $dateTime === '') {
                continue;
            }

            $rows[] = [
                'transaction_date' => CarbonImmutable::parse($dateTime)->toDateString(),
                'vehicle_name_snapshot' => $vehicleName,
                'odometer_km' => $this->parseNumber($record['odometer(km)'] ?? null) ?? 0,
                'initial_volume_l' => $this->parseNumber($record['initial volume(l)'] ?? null),
                'final_volume_l' => $this->parseNumber($record['final volume(l)'] ?? null),
                'usage_l' => $this->parseNumber($record['usage (l)'] ?? null) ?? 0,
                'cost_rp' => $this->parseNumber($record['cost (rp)'] ?? null) ?? 0,
                'idle_usage_l' => $this->parseNumber($record['idle usage (l)'] ?? null),
                'km_per_l' => $this->parseNumber($record['1 km /l'] ?? null),
                'l_per_km' => $this->parseNumber($record['1 l /km'] ?? null),
                'cost_per_km' => $this->parseNumber($record['1 km /cost'] ?? null),
                'refuel_l' => $this->parseNumber($record['refuel (l)'] ?? null),
                'refuel_times' => $this->parseInteger($record['refuel (times)'] ?? null),
                'running_duration_seconds' => $this->parseDuration($record['running (hh:mm:ss)'] ?? null),
                'idle_duration_seconds' => $this->parseDuration($record['idle (hh:mm:ss)'] ?? null),
                'stop_duration_seconds' => $this->parseDuration($record['stop (hh:mm:ss)'] ?? null),
            ];
        }

        return $rows;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function matchRowsToFleets(array $rows): array
    {
        $fleetGroups = Fleet::query()
            ->orderBy('vehicle_name')
            ->get(['id', 'vehicle_name'])
            ->groupBy(fn (Fleet $fleet): string => $this->normalizeVehicleName($fleet->vehicle_name));

        $unmatched = [];
        $ambiguous = [];
        $matched = [];

        foreach ($rows as $row) {
            $key = $this->normalizeVehicleName($row['vehicle_name_snapshot']);
            /** @var Collection<int, Fleet>|null $fleets */
            $fleets = $fleetGroups->get($key);

            if (! $fleets || $fleets->isEmpty()) {
                $unmatched[] = $row['vehicle_name_snapshot'];

                continue;
            }

            if ($fleets->count() > 1) {
                $ambiguous[] = $row['vehicle_name_snapshot'];

                continue;
            }

            $matched[] = [
                ...$row,
                'fleet_id' => $fleets->first()->id,
            ];
        }

        if ($unmatched !== [] || $ambiguous !== []) {
            $messages = [];

            if ($unmatched !== []) {
                $messages[] = 'Vehicle not found in fleet master: '.implode(', ', array_values(array_unique($unmatched))).'.';
            }

            if ($ambiguous !== []) {
                $messages[] = 'Vehicle name is duplicated in fleet master: '.implode(', ', array_values(array_unique($ambiguous))).'.';
            }

            throw ValidationException::withMessages([
                'file' => implode(' ', $messages),
            ]);
        }

        return $matched;
    }

    /**
     * @return list<list<string>>
     */
    private function extractHtmlTables(string $contents): array
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$contents);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $tables = [];

        foreach ($document->getElementsByTagName('table') as $table) {
            $rows = [];

            foreach ($table->getElementsByTagName('tr') as $tr) {
                if (! $tr instanceof DOMElement) {
                    continue;
                }

                $cells = [];

                foreach ($tr->childNodes as $cell) {
                    if (! $cell instanceof DOMElement || ! in_array($cell->tagName, ['td', 'th'], true)) {
                        continue;
                    }

                    $cells[] = $this->cleanText($cell->textContent);
                }

                if ($cells !== []) {
                    $rows[] = $cells;
                }
            }

            $tables[] = $rows;
        }

        return $tables;
    }

    private function normalizeHeader(string $value): string
    {
        return str($this->cleanText($value))->lower()->squish()->toString();
    }

    private function normalizeVehicleName(string $value): string
    {
        return str($this->cleanText($value))->upper()->squish()->toString();
    }

    private function cleanText(?string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', str_replace("\u{00A0}", ' ', (string) $value)));
    }

    private function parseNumber(mixed $value): ?float
    {
        $clean = str_replace(',', '', $this->cleanText((string) $value));

        if ($clean === '' || in_array(strtolower($clean), ['nan', 'inf', 'infinity'], true)) {
            return null;
        }

        return is_numeric($clean) ? (float) $clean : null;
    }

    private function parseInteger(mixed $value): ?int
    {
        $number = $this->parseNumber($value);

        return $number === null ? null : (int) $number;
    }

    private function parseDuration(mixed $value): ?int
    {
        $clean = $this->cleanText((string) $value);

        if ($clean === '') {
            return null;
        }

        $parts = explode(':', $clean);

        if (count($parts) !== 3) {
            return null;
        }

        return ((int) $parts[0] * 3600) + ((int) $parts[1] * 60) + (int) $parts[2];
    }
}
