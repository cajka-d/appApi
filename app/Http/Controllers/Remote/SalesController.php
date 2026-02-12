<?php

namespace App\Http\Controllers\Remote;

use App\Http\Controllers\Controller;

use App\Models\RemoteSale;
use App\Services\RemoteApiService;

class SalesController extends Controller
{
	public function index(RemoteApiService $api)
	{
		$sales = $api->salesAll('2026-02-10', '2026-02-12');
		$saved = RemoteSale::upsertManyFromApi($sales);

		return $saved;
	}
}
