<?php

namespace App\Services\Ilan;

use App\Models\Ilan;
use Illuminate\Support\Facades\DB;

class IlanBulkService
{
    public function bulkUpdate(array $ids, array $updateData): array
    {
        $updateData = array_filter($updateData, function ($value) {
            return $value !== null && $value !== '';
        });

        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'Güncellenecek veri bulunamadı.'
            ];
        }

        $updateData['updated_at'] = now();
        $updatedCount = Ilan::whereIn('id', $ids)->update($updateData);

        return [
            'success' => true,
            'message' => $updatedCount . ' ilan başarıyla güncellendi.',
            'updated_count' => $updatedCount,
        ];
    }

    public function bulkDelete(array $ids): array
    {
        $deletedCount = Ilan::whereIn('id', $ids)->delete();

        return [
            'success' => true,
            'message' => $deletedCount . ' ilan başarıyla silindi.',
            'deleted_count' => $deletedCount,
        ];
    }

    public function bulkAction(string $action, array $ids, $value = null): array
    {
        DB::beginTransaction();

        try {
            $affected = 0;
            $message = '';

            switch ($action) {
                case 'activate':
                    $affected = Ilan::whereIn('id', $ids)->update([
                        'status' => 'active',
                        'is_published' => true,
                        'updated_at' => now()
                    ]);
                    $message = $affected . ' ilan aktif yapıldı.';
                    break;

                case 'deactivate':
                    $affected = Ilan::whereIn('id', $ids)->update([
                        'status' => 'inactive',
                        'is_published' => false,
                        'updated_at' => now()
                    ]);
                    $message = $affected . ' ilan pasif yapıldı.';
                    break;

                case 'delete':
                    $affected = Ilan::whereIn('id', $ids)->delete();
                    $message = $affected . ' ilan silindi.';
                    break;

                case 'assign_danisman':
                    if (!$value || !is_numeric($value)) {
                        return [
                            'success' => false,
                            'message' => 'Danışman seçilmedi.'
                        ];
                    }
                    $affected = Ilan::whereIn('id', $ids)->update([
                        'danisman_id' => $value,
                        'updated_at' => now()
                    ]);
                    $message = $affected . ' ilana danışman atandı.';
                    break;

                case 'add_tag':
                    if (!$value || !is_numeric($value)) {
                        return [
                            'success' => false,
                            'message' => 'Etiket seçilmedi.'
                        ];
                    }
                    foreach ($ids as $ilanId) {
                        $ilan = Ilan::find($ilanId);
                        if ($ilan) {
                            $ilan->etiketler()->syncWithoutDetaching([$value]);
                            $affected++;
                        }
                    }
                    $message = $affected . ' ilana etiket eklendi.';
                    break;

                case 'remove_tag':
                    if (!$value || !is_numeric($value)) {
                        return [
                            'success' => false,
                            'message' => 'Etiket seçilmedi.'
                        ];
                    }
                    foreach ($ids as $ilanId) {
                        $ilan = Ilan::find($ilanId);
                        if ($ilan) {
                            $ilan->etiketler()->detach([$value]);
                            $affected++;
                        }
                    }
                    $message = $affected . ' ilandan etiket kaldırıldı.';
                    break;

                default:
                    return [
                        'success' => false,
                        'message' => 'Geçersiz işlem.'
                    ];
            }

            DB::commit();

            return [
                'success' => true,
                'message' => $message,
                'affected' => $affected,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage(),
            ];
        }
    }
}


