<?php

namespace App\Services;

use App\Models\InspectionRecord;
use App\Support\EquipmentChecklist;
use Illuminate\Support\Facades\DB;

class InspectionRecordService
{
    /**
     * @param array{
     *     inspection_date: string,
     *     inspector: string,
     *     note?: string|null,
     *     results: array<string, array{status: 'ok'|'ng'}|array{pressure: numeric-string}>
     * } $data
     */
    public function create(array $data): InspectionRecord
    {
        return InspectionRecord::create([
            'inspection_date' => $data['inspection_date'],
            'inspector' => $data['inspector'],
            'note' => $data['note'] ?? null,
            'results' => $data['results'],
        ]);
    }

    /**
     * 点検記録と編集履歴を同時に保存し、片方だけ更新される状態を防ぐ。
     *
     * @param array{
     *     inspector: string,
     *     editor_name: string,
     *     note?: string|null,
     *     results: array<string, array{status: 'ok'|'ng'}|array{pressure: numeric-string}>
     * } $data
     */
    public function update(InspectionRecord $inspection, array $data): InspectionRecord
    {
        return DB::transaction(function () use ($inspection, $data): InspectionRecord {
            $record = InspectionRecord::whereKey($inspection->getKey())->lockForUpdate()->firstOrFail();
            $newValues = [
                'inspector' => $data['inspector'],
                'note' => $data['note'] ?? null,
                'results' => $data['results'],
            ];
            $changes = $this->changedFields($record, $newValues);
            $editor = trim($data['editor_name']);

            $record->fill($newValues);
            $record->last_edited_by = $editor;
            $record->save();

            $record->editLogs()->create([
                'editor_name' => $editor,
                'changes' => $changes,
                'created_at' => now(),
            ]);

            return $record;
        });
    }

    /**
     * 記録項目の差分だけを残し、閲覧時に何が変わったか確認できるようにする。
     *
     * @param array{
     *     inspector: string,
     *     note: string|null,
     *     results: array<string, array{status: 'ok'|'ng'}|array{pressure: numeric-string}>
     * } $newValues
     * @return array<string, mixed>
     */
    private function changedFields(InspectionRecord $record, array $newValues): array
    {
        $changes = [];

        foreach ($newValues as $field => $newValue) {
            $oldValue = $record->{$field};
            if ($field === 'results') {
                $oldValue = $oldValue ?: [];
                if ($oldValue != $newValue) {
                    $changes[$field] = $this->resultChanges($oldValue, $newValue);
                }
            } elseif ($oldValue !== $newValue) {
                $changes[$field] = ['変更前' => $oldValue, '変更後' => $newValue];
            }
        }

        return $changes;
    }

    /**
     * 点検項目ごとの旧値と新値を、人が読める編集履歴に変換する。
     *
     * @param array<string, array{status: 'ok'|'ng'}|array{pressure: numeric-string}> $oldResults
     * @param array<string, array{status: 'ok'|'ng'}|array{pressure: numeric-string}> $newResults
     * @return array<string, array{変更前: string, 変更後: string}>
     */
    private function resultChanges(array $oldResults, array $newResults): array
    {
        $changes = [];
        foreach (EquipmentChecklist::items() as $id => $item) {
            $oldValue = $oldResults[$id] ?? null;
            $newValue = $newResults[$id] ?? null;
            if ($oldValue != $newValue) {
                $changes[$item['group'] . ' / ' . $item['location'] . ' / ' . $item['item']] = [
                    '変更前' => $this->describeResult($oldValue),
                    '変更後' => $this->describeResult($newValue),
                ];
            }
        }

        return $changes;
    }

    /**
     * 点検結果を、編集履歴に表示するための文字列に整える。
     *
     * @param array{status: 'ok'|'ng'}|array{pressure: numeric-string}|null $result
     */
    private function describeResult(?array $result): string
    {
        if (!$result) {
            return '未入力';
        }

        if (isset($result['pressure'])) {
            return $result['pressure'] . ' MPa';
        }

        return ($result['status'] ?? null) === 'ok' ? '良好' : '要対応';
    }
}
