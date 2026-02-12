<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteIncome extends Model
{
    protected $table = 'remote_incomes';

    protected $fillable = array(
        'income_id',
        'number',
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'quantity',
        'total_price',
        'date_close',
        'warehouse_name',
        'nm_id',
    );

    protected $casts = array(
        'income_id' => 'integer',
        'date' => 'date',
        'last_change_date' => 'date',
        'barcode' => 'integer',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'date_close' => 'date',
        'nm_id' => 'integer',
    );

    /**
	 * Upsert of a single row from the API using the key (income_id + nm_id + barcode + date)
     */
    public static function upsertFromApi(array $row): void
    {
        $incomeId = isset($row['income_id']) ? (int) $row['income_id'] : 0;
        $nmId     = isset($row['nm_id']) ? (int) $row['nm_id'] : 0;
        $date     = isset($row['date']) ? (string) $row['date'] : '';
        $barcode  = array_key_exists('barcode', $row) && $row['barcode'] !== null ? (int) $row['barcode'] : null;

        if ($incomeId === 0 || $date === '' || $nmId === 0) {
            return;
        }

        self::updateOrCreate(
            array(
                'income_id' => $incomeId,
                'nm_id'     => $nmId,
                'barcode'   => $barcode,
                'date'      => $date,
            ),
            array(
                'number'           => isset($row['number']) ? (string) $row['number'] : null,
                'last_change_date' => $row['last_change_date'] ?? null,
                'supplier_article' => $row['supplier_article'] ?? null,
                'tech_size'        => $row['tech_size'] ?? null,
                'quantity'         => isset($row['quantity']) ? (int) $row['quantity'] : 0,
                'total_price'      => isset($row['total_price']) ? (string) $row['total_price'] : '0.00',
                'date_close'       => $row['date_close'] ?? null,
                'warehouse_name'   => $row['warehouse_name'] ?? null,
            )
        );
    }

    public static function upsertManyFromApi(array $rows): int
    {
        $saved = 0;

        foreach ($rows as $row) {
            self::upsertFromApi($row);

			// consider "saved" if there are required fields
            if (!empty($row['income_id']) && !empty($row['date']) && array_key_exists('nm_id', $row)) {
                $saved++;
            }
        }

        return $saved;
    }
}
