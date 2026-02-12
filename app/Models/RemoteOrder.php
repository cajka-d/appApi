<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteOrder extends Model
{
    protected $table = 'remote_orders';

    protected $fillable = array(
        'g_number',
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'total_price',
        'discount_percent',
        'warehouse_name',
        'oblast',
        'income_id',
        'odid',
        'nm_id',
        'subject',
        'category',
        'brand',
        'is_cancel',
        'cancel_dt',
    );

    protected $casts = array(
        'date' => 'datetime',
        'last_change_date' => 'date',
        'barcode' => 'integer',
        'total_price' => 'decimal:2',
        'discount_percent' => 'integer',
        'income_id' => 'integer',
        'nm_id' => 'integer',
        'is_cancel' => 'boolean',
        'cancel_dt' => 'datetime',
    );

    /**
     * Save one record
     */
    public static function upsertFromApi(array $row): void
    {
        self::updateOrCreate(
            array('g_number' => (string) ($row['g_number'] ?? '')),
            array(
                'date' => $row['date'] ?? null,
                'last_change_date' => $row['last_change_date'] ?? null,
                'supplier_article' => $row['supplier_article'] ?? null,
                'tech_size' => $row['tech_size'] ?? null,
                'barcode' => isset($row['barcode']) ? (int) $row['barcode'] : null,
                'total_price' => isset($row['total_price']) ? (string) $row['total_price'] : '0.00',
                'discount_percent' => isset($row['discount_percent']) ? (int) $row['discount_percent'] : 0,
                'warehouse_name' => $row['warehouse_name'] ?? null,
                'oblast' => $row['oblast'] ?? null,
                'income_id' => isset($row['income_id']) ? (int) $row['income_id'] : 0,
                'odid' => isset($row['odid']) ? (string) $row['odid'] : null,
                'nm_id' => isset($row['nm_id']) ? (int) $row['nm_id'] : 0,
                'subject' => $row['subject'] ?? null,
                'category' => $row['category'] ?? null,
                'brand' => $row['brand'] ?? null,
                'is_cancel' => (bool) ($row['is_cancel'] ?? false),
                'cancel_dt' => $row['cancel_dt'] ?? null,
            )
        );
    }

    /**
     * Save an array of records data[]
     */
    public static function upsertManyFromApi(array $rows): int
    {
        $saved = 0;

        foreach ($rows as $row) {
            $gNumber = (string) ($row['g_number'] ?? '');
            if ($gNumber === '') {
                continue;
            }

            self::upsertFromApi($row);
            $saved++;
        }

        return $saved;
    }
}
