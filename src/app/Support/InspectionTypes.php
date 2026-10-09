<?php

namespace App\Support;

/**
 * ホーム画面に表示する点検種別の定義。
 */
class InspectionTypes
{
    /**
     * 点検種別を表示順に返す。
     *
     * @return array<string, array{key: string, name: string, description: string, route: string, available: bool}>
     */
    public static function all(): array
    {
        return [
            'daily' => [
                'key' => 'daily',
                'name' => '日常点検',
                'description' => '毎日行う設備点検を記録します',
                'route' => 'inspections.create',
                'available' => true,
            ],
            'weekly' => [
                'key' => 'weekly',
                'name' => '週次点検',
                'description' => '週末に行う設備点検を記録します',
                'route' => 'inspections.weekly',
                'available' => false,
            ],
            'monthly' => [
                'key' => 'monthly',
                'name' => '月次点検',
                'description' => '毎月第一週末に行う設備点検を記録します',
                'route' => 'inspections.monthly',
                'available' => false,
            ],
            'periodic' => [
                'key' => 'periodic',
                'name' => '定期点検',
                'description' => '6月、12月の決算時に行う設備点検を記録します',
                'route' => 'inspections.periodic',
                'available' => false,
            ],
        ];
    }

    /**
     * 指定した種別の定義を返す。存在しない場合は404にする。
     *
     * @return array{key: string, name: string, description: string, route: string, available: bool}
     */
    public static function find(string $key): array
    {
        return self::all()[$key] ?? abort(404);
    }
}
