<?php

namespace App\Http\Controllers\Remote;

use App\Http\Controllers\Controller;
use App\Models\RemoteStock;

use Illuminate\Http\Request;
use App\Services\RemoteApiService;

class StocksController extends Controller
{
	public function index(RemoteApiService $api)
	{
		$dateFrom = now()->format('Y-m-d');
		$stocks = $api->stocksAll($dateFrom);
		$saved = RemoteStock::upsertManyFromApi($stocks);

		return $saved;
	}
}
