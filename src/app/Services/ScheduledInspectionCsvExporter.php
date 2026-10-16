<?php

namespace App\Services;

use App\Models\InspectionRecord;
use App\Support\InspectionTypes;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScheduledInspectionCsvExporter
{
    /**
     * 指定した点検種別の保存済み結果を、Excelで開けるCSVとしてダウンロードする。
     */
    public function download(string $type): StreamedResponse
    {
        $records = InspectionRecord::ofType($type)->orderBy('inspection_date')->get();
        $items = InspectionTypes::itemsFor($type);

        return response()->streamDownload(function () use ($records, $items): void {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \RuntimeException('CSV出力先を開けませんでした。');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                '点検日',
                '点検担当者',
                '点検名称',
                '点検箇所',
                '点検項目',
                '方法',
                '結果',
                '測定値',
                '単位',
                '全体メモ',
                '最終編集者',
            ]);

            foreach ($records as $record) {
                foreach ($items as $id => $item) {
                    $result = $record->results[$id] ?? [];
                    $status = ($result['status'] ?? null) === 'ok'
                        ? '良好'
                        : (($result['status'] ?? null) === 'ng' ? '要対応' : (isset($result['value']) ? '数値記録' : '未点検'));
                    $row = [
                        $record->inspection_date->format('Y-m-d'),
                        $record->inspector,
                        $item['group'],
                        $item['location'],
                        $item['item'],
                        $item['method'],
                        $status,
                        $result['value'] ?? '',
                        isset($result['value']) ? ($item['unit'] ?? '') : '',
                        $record->note ?? '',
                        $record->last_edited_by ?? '',
                    ];

                    fputcsv(
                        $output,
                        array_map(fn(string $value): string => $this->sanitizeForSpreadsheets($value), $row)
                    );
                }
            }

            fclose($output);
        }, $type . '-inspections-' . now()->format('Ymd') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Excelで計算式として誤認識される先頭文字をエスケープする。
     */
    private function sanitizeForSpreadsheets(string $value): string
    {
        return preg_match('/^[=\+\-@\t\r]/', $value) === 1
            ? "'" . $value
            : $value;
    }
}
