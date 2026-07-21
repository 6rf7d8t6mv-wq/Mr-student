<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SOURCES = [
        'moe_government_universities' => [
            'file' => 'moe_government_universities.csv',
            'type' => 'university',
            'ownership' => 'government',
        ],
        'moe_private_universities_colleges' => [
            'file' => 'moe_private_universities_colleges.csv',
            'type' => 'college',
            'ownership' => 'private',
        ],
    ];

    public function up(): void
    {
        foreach (self::SOURCES as $source => $config) {
            $path = database_path('imports/official/'.$config['file']);
            $handle = is_readable($path) ? fopen($path, 'r') : false;

            if ($handle === false) {
                continue;
            }

            $headers = array_map('trim', fgetcsv($handle) ?: []);

            while (($values = fgetcsv($handle)) !== false) {
                $row = array_combine($headers, array_slice(array_pad($values, count($headers), null), 0, count($headers)));

                if (! is_array($row) || blank($row['official_id'] ?? null) || blank($row['name_ar'] ?? null)) {
                    continue;
                }

                DB::table('educational_institutions')->updateOrInsert(
                    [
                        'source' => $source,
                        'official_id' => trim((string) $row['official_id']),
                    ],
                    [
                        'name_ar' => trim((string) $row['name_ar']),
                        'institution_type' => trim((string) ($row['institution_type'] ?? '')) ?: $config['type'],
                        'education_stage' => 'higher_education',
                        'ownership_type' => $config['ownership'],
                        'gender_type' => 'mixed',
                        'city' => blank($row['city'] ?? null) ? null : trim((string) $row['city']),
                        'is_active' => true,
                        'last_verified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
            }

            fclose($handle);
        }
    }

    public function down(): void
    {
        DB::table('educational_institutions')
            ->whereIn('source', array_keys(self::SOURCES))
            ->delete();
    }
};
