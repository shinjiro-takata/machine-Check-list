<?php

namespace App\Support;

class InspectionResultMapper
{
    /**
     * 既知の点検項目だけを、データベース保存用の結果配列へ変換する。
     *
     * @param array<string, array{status?: 'ok'|'ng'|null, pressure?: int|float|string|null}> $submitted
     * @return array<string, array{status: 'ok'|'ng'}|array{pressure: numeric-string}>
     */
    public function map(array $submitted): array
    {
        $results = [];

        foreach (EquipmentChecklist::items() as $id => $item) {
            $value = $submitted[$id] ?? [];

            if (($item['type'] ?? null) === 'pressure') {
                if (isset($value['pressure']) && $value['pressure'] !== '') {
                    $results[$id] = ['pressure' => (string) $value['pressure']];
                }
            } elseif (isset($value['status']) && in_array($value['status'], ['ok', 'ng'], true)) {
                $results[$id] = ['status' => $value['status']];
            }
        }

        return $results;
    }
}
