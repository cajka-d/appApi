<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteStock extends Model
{
    protected $table = 'remote_stocks';

    protected $fillable = array(
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'quantity',
        'is_supply',
        'is_realization',
        'quantity_full',
        'warehouse_name',
        'in_way_to_client',
        'in_way_from_client',
        'nm_id',
        'subject',
        'category',
        'brand',
        'sc_code',
        'price',
        'discount',
    );

    protected $casts = array(
        'date' => 'date',
        'last_change_date' => 'date',
        'barcode' => 'integer',
        'quantity' => 'integer',
        'is_supply' => 'boolean',
        'is_realization' => 'boolean',
        'quantity_full' => 'integer',
        'in_way_to_client' => 'integer',
        'in_way_from_client' => 'integer',
        'nm_id' => 'integer',
        'sc_code' => 'integer',
        'price' => 'decimal:2',
        'discount' => 'integer',
    );

    /**
	 * Upsert of a single row (by unique key: date + warehouse_name + nm_id + barcode)
     */
    public static function upsertFromApi(array $row): void
    {
        $date = isset($row['date']) ? (string) $row['date'] : '';
        $warehouse = isset($row['warehouse_name']) ? (string) $row['warehouse_name'] : '';
        $nmId = isset($row['nm_id']) ? (int) $row['nm_id'] : 0;
        $barcode = array_key_exists('barcode', $row) && $row['barcode'] !== null ? (int) $row['barcode'] : null;

        if ($date === '' || $warehouse === '' || $nmId === 0) {
            return; // there are no key fields
        }

        self::updateOrCreate(
            array(
                'date' => $date,
                'warehouse_name' => $warehouse,
                'nm_id' => $nmId,
                'barcode' => $barcode,
            ),
            array(
                'last_change_date' => $row['last_change_date'] ?? null,
                'supplier_article' => $row['supplier_article'] ?? null,
                'tech_size' => $row['tech_size'] ?? null,

                'quantity' => isset($row['quantity']) ? (int) $row['quantity'] : 0,
                'is_supply' => array_key_exists('is_supply', $row) ? ($row['is_supply'] === null ? null : (bool) $row['is_supply']) : null,
                'is_realization' => array_key_exists('is_realization', $row) ? ($row['is_realization'] === null ? null : (bool) $row['is_realization']) : null,
                'quantity_full' => isset($row['quantity_full']) ? (int) $row['quantity_full'] : null,

                'in_way_to_client' => isset($row['in_way_to_client']) ? (int) $row['in_way_to_client'] : null,
                'in_way_from_client' => isset($row['in_way_from_client']) ? (int) $row['in_way_from_client'] : null,

                'subject' => $row['subject'] ?? null,
                'category' => $row['category'] ?? null,
                'brand' => $row['brand'] ?? null,

                'sc_code' => isset($row['sc_code']) ? (int) $row['sc_code'] : null,

                'price' => isset($row['price']) ? (string) $row['price'] : '0.00',
                'discount' => isset($row['discount']) ? (int) $row['discount'] : 0,
            )
        );
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
