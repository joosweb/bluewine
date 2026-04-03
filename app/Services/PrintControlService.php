<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrintControlService
{
    const ACTION_ORIGINAL = 'ORIGINAL';
    const ACTION_REPRINT = 'REPRINT';
    const ACTION_BLOCKED = 'BLOCKED';

    public function validateSaleOwnership($saleId, $shopId)
    {
        return DB::table('sales')
            ->where('id', $saleId)
            ->where('fk_shop_id', $shopId)
            ->first();
    }

    public function canPrintOriginal($sale)
    {
        return empty($sale->printed_original_at) && (int) $sale->print_original_count === 0;
    }

    public function maxReprintsAllowed()
    {
        $max = env('PRINT_MAX_REPRINTS', 1);

        return is_numeric($max) ? max((int) $max, 0) : 1;
    }

    public function registerPrintLog(array $data)
    {
        DB::table('voucher_print_logs')->insert([
            'sale_id' => $data['sale_id'],
            'shop_id' => $data['shop_id'],
            'user_id' => $data['user_id'],
            'approved_by_user_id' => $data['approved_by_user_id'] ?? null,
            'action' => $data['action'],
            'reason' => $data['reason'] ?? null,
            'copy_number' => $data['copy_number'] ?? 0,
            'source' => $data['source'] ?? 'API',
            'ip' => $data['ip'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function executeOriginal($saleId, $user, $shopId, $source, $ip, $userAgent)
    {
        return DB::transaction(function () use ($saleId, $user, $shopId, $source, $ip, $userAgent) {
            $sale = DB::table('sales')
                ->where('id', $saleId)
                ->where('fk_shop_id', $shopId)
                ->lockForUpdate()
                ->first();

            if (!$sale) {
                return [
                    'allowed' => false,
                    'status' => 404,
                    'message' => 'Venta no encontrada para la tienda actual.',
                ];
            }

            if (!$this->canPrintOriginal($sale)) {
                $this->registerPrintLog([
                    'sale_id' => $sale->id,
                    'shop_id' => $shopId,
                    'user_id' => $user->id,
                    'action' => self::ACTION_BLOCKED,
                    'reason' => 'Intento de segunda impresion original',
                    'source' => $source,
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                ]);

                return [
                    'allowed' => false,
                    'status' => 403,
                    'message' => 'La impresion original ya fue ejecutada para esta venta.',
                ];
            }

            $now = Carbon::now();

            DB::table('sales')
                ->where('id', $sale->id)
                ->update([
                    'printed_original_at' => $now,
                    'print_original_count' => (int) $sale->print_original_count + 1,
                    'print_locked' => true,
                    'print_last_user_id' => $user->id,
                    'print_last_at' => $now,
                ]);

            $this->registerPrintLog([
                'sale_id' => $sale->id,
                'shop_id' => $shopId,
                'user_id' => $user->id,
                'action' => self::ACTION_ORIGINAL,
                'copy_number' => 0,
                'source' => $source,
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return [
                'allowed' => true,
                'status' => 200,
                'sale' => $sale,
                'copy_number' => 0,
                'is_reprint' => false,
            ];
        });
    }

    public function executeReprint($saleId, $user, $shopId, $reason, $approvedByUserId, $source, $ip, $userAgent)
    {
        return DB::transaction(function () use ($saleId, $user, $shopId, $reason, $approvedByUserId, $source, $ip, $userAgent) {
            $sale = DB::table('sales')
                ->where('id', $saleId)
                ->where('fk_shop_id', $shopId)
                ->lockForUpdate()
                ->first();

            if (!$sale) {
                return [
                    'allowed' => false,
                    'status' => 404,
                    'message' => 'Venta no encontrada para la tienda actual.',
                ];
            }

            if (!$reason) {
                $this->registerPrintLog([
                    'sale_id' => $sale->id,
                    'shop_id' => $shopId,
                    'user_id' => $user->id,
                    'action' => self::ACTION_BLOCKED,
                    'reason' => 'Reimpresion sin motivo',
                    'source' => $source,
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                ]);

                return [
                    'allowed' => false,
                    'status' => 422,
                    'message' => 'El motivo de reimpresion es obligatorio.',
                ];
            }

            $maxReprints = $this->maxReprintsAllowed();
            if ((int) $sale->reprint_count >= $maxReprints) {
                $this->registerPrintLog([
                    'sale_id' => $sale->id,
                    'shop_id' => $shopId,
                    'user_id' => $user->id,
                    'action' => self::ACTION_BLOCKED,
                    'reason' => 'Supero maximo de reimpresiones',
                    'source' => $source,
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                ]);

                return [
                    'allowed' => false,
                    'status' => 403,
                    'message' => 'La venta alcanzo el maximo de reimpresiones permitidas.',
                ];
            }

            $copyNumber = (int) $sale->reprint_count + 1;
            $now = Carbon::now();

            DB::table('sales')
                ->where('id', $sale->id)
                ->update([
                    'reprint_count' => $copyNumber,
                    'print_last_user_id' => $user->id,
                    'print_last_at' => $now,
                    'print_locked' => true,
                ]);

            $this->registerPrintLog([
                'sale_id' => $sale->id,
                'shop_id' => $shopId,
                'user_id' => $user->id,
                'approved_by_user_id' => $approvedByUserId,
                'action' => self::ACTION_REPRINT,
                'reason' => $reason,
                'copy_number' => $copyNumber,
                'source' => $source,
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            return [
                'allowed' => true,
                'status' => 200,
                'sale' => $sale,
                'copy_number' => $copyNumber,
                'is_reprint' => true,
                'reason' => $reason,
            ];
        });
    }

    public function getSaleLogs($saleId, $shopId)
    {
        return DB::table('voucher_print_logs')
            ->leftJoin('users as u', 'voucher_print_logs.user_id', '=', 'u.id')
            ->leftJoin('users as a', 'voucher_print_logs.approved_by_user_id', '=', 'a.id')
            ->where('sale_id', $saleId)
            ->where('shop_id', $shopId)
            ->select(
                'voucher_print_logs.*',
                DB::raw("CONCAT(COALESCE(u.name, ''), ' ', COALESCE(u.last_name, '')) as user_name"),
                DB::raw("CONCAT(COALESCE(a.name, ''), ' ', COALESCE(a.last_name, '')) as approved_by_name")
            )
            ->orderBy('id', 'DESC')
            ->get();
    }
}
