<?php

namespace App\Http\Controllers\Remote;

use App\Http\Controllers\Controller;
use App\Models\RemoteOrder;
use App\Services\RemoteApiService;

class OrdersController extends Controller
{
    public function index(RemoteApiService $api)
    {
        $orders = $api->ordersAll('2026-02-11', '2026-02-12');

        $saved = RemoteOrder::upsertManyFromApi($orders);

        return $saved;
    }
}
