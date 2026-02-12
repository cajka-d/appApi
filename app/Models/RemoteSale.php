<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteSale extends Model
{
    protected $table = 'remote_sales';

    protected $fillable = array(
        'g_number',
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'total_price',
        'discount_percent',
        'is_supply',
        'is_realization',
        'promo_code_discount',
        'warehouse_name',
        'country_name',
        'oblast_okrug_name',
        'region_name',
        'income_id',
        'sale_id',
        'odid',
        'spp',
        'for_pay',
        'finished_price',
        'price_with_disc',
        'nm_id',
        'subject',
        'category',
        'brand',
        'is_storno',
    );

    protected $casts = array(
        'date' => 'date',
        'last_change_date' => 'date',
        'barcode' => 'integer',
        'total_price' => 'decimal:2',
        'discount_percent' => 'integer',
        'is_supply' => 'boolean',
        'is_realization' => 'boolean',
        'promo_code_discount' => 'decimal:2',
        'income_id' => 'integer',
        'spp' => 'integer',
        'for_pay' => 'decimal:2',
        'finished_price' => 'decimal:2',
        'price_with_disc' => 'decimal:2',
        'nm_id' => 'integer',
        'is_storno' => 'boolean',
    );

    /**
     * Upsert one line
     * Key: sale_id (if available), otherwise g_number.
     */
    public static function upsertFromApi(array $row): void
    {
        $key = array();
        if (!empty($row['sale_id'])) {
            $key = array('sale_id' => (string) $row['sale_id']);
        } else {
            $key = array('g_number' => (string) ($row['g_number'] ?? ''));
        }

        if (empty($key[array_key_first($key)])) {
            return; // нет ключа — нечего сохранять
        }

        self::updateOrCreate($key, array(
            'g_number' => (string) ($row['g_number'] ?? ''),
            'date' => $row['date'] ?? null,
            'last_change_date' => $row['last_change_date'] ?? null,
            'supplier_article' => $row['supplier_article'] ?? null,
            'tech_size' => $row['tech_size'] ?? null,
            'barcode' => isset($row['barcode']) ? (int) $row['barcode'] : null,

            'total_price' => isset($row['total_price']) ? (string) $row['total_price'] : '0.00',
            'discount_percent' => isset($row['discount_percent']) ? (int) $row['discount_percent'] : 0,

            'is_supply' => (bool) ($row['is_supply'] ?? false),
            'is_realization' => (bool) ($row['is_realization'] ?? false),

            'promo_code_discount' => isset($row['promo_code_discount']) && $row['promo_code_discount'] !== null
                ? (string) $row['promo_code_discount']
                : null,

            'warehouse_name' => $row['warehouse_name'] ?? null,
            'country_name' => $row['country_name'] ?? null,
            'oblast_okrug_name' => $row['oblast_okrug_name'] ?? null,
            'region_name' => $row['region_name'] ?? null,

            'income_id' => isset($row['income_id']) ? (int) $row['income_id'] : 0,
            'odid' => isset($row['odid']) && $row['odid'] !== null ? (string) $row['odid'] : null,

            'spp' => isset($row['spp']) ? (int) $row['spp'] : 0,
            'for_pay' => isset($row['for_pay']) ? (string) $row['for_pay'] : '0.00',
            'finished_price' => isset($row['finished_price']) ? (string) $row['finished_price'] : '0.00',
            'price_with_disc' => isset($row['price_with_disc']) ? (string) $row['price_with_disc'] : '0.00',

            'nm_id' => isset($row['nm_id']) ? (int) $row['nm_id'] : 0,

            'subject' => $row['subject'] ?? null,
            'category' => $row['category'] ?? null,
            'brand' => $row['brand'] ?? null,

            'is_storno' => array_key_exists('is_storno', $row) ? ($row['is_storno'] === null ? null : (bool) $row['is_storno']) : null,
        ));
    }

    public static function upsertManyFromApi(array $rows): int
    {
        $saved = 0;
		
        foreach ($rows as $row) {
            self::upsertFromApi($row);
            $saved++;
        }
        return $saved;
    }
}
