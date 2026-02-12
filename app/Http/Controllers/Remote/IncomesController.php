<?php

namespace App\Http\Controllers\Remote;

use App\Http\Controllers\Controller;
use App\Models\RemoteIncome;

use Illuminate\Http\Request;
use App\Services\RemoteApiService;

class IncomesController extends Controller
{
	public function index(RemoteApiService $api)
	{
		$incomes = $api->incomesAll('2026-02-11', '2026-02-12');
		$saved = RemoteIncome::upsertManyFromApi($incomes);

		return $saved;
	}
}
