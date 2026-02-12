<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RemoteApiService
{
    /** @var string */
    private $baseUrl;

    /** @var string */
    private $key;

    /** @var int */
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('remote-api.base_url', 'http://109.73.206.144:6969'), '/');
        $this->key     = (string) config('remote-api.key', 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie');
        $this->timeout = (int) config('remote-api.timeout', 30);
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->acceptJson()
            ->asJson()
            ->retry(2, 300);
    }

    /**
     * @throws RequestException
     */
    private function get(string $path, array $query = array()): array
    {
        $query = array_filter($query, function ($v) {
            return $v !== null && $v !== '';
        });

        $query['key'] = $this->key;

        $response = $this->client()->get($path, $query)->throw();

        $json = $response->json();
		
        return is_array($json) ? $json : array();
    }

    private function fmtDate($date): string
    {
        if ($date instanceof CarbonInterface) {
            return $date->format('Y-m-d');
        }
        return (string) $date;
    }

    /**
     * @throws RequestException
     */
	private function getAllPages(string $path, array $query = array(), int $limit = 500, int $startPage = 1): array
	{
		$page = $startPage;
		$all  = array();

		while (true) {
			$payload = $this->get($path, array_merge($query, array(
				'page'  => $page,
				'limit' => $limit,
			)));

			$items = (isset($payload['data']) && is_array($payload['data'])) ? $payload['data'] : array();			
			$all = array_merge($all, $items);

			if (isset($payload['meta']) && is_array($payload['meta'])) {
				$current = isset($payload['meta']['current_page']) ? (int) $payload['meta']['current_page'] : $page;
				$last    = isset($payload['meta']['last_page']) ? (int) $payload['meta']['last_page'] : $current;

				if ($current >= $last) {
					break;
				}

				$page = $current + 1;
				continue;
			}

			if (count($items) === 0 || count($items) < $limit) {
				break;
			}

			$page++;
		}

		return $all;
	}

    // ===== Endpoints =====

    public function ordersAll($dateFrom, $dateTo, int $limit = 500): array
    {
        return $this->getAllPages('/api/orders', array(
            'dateFrom' => $this->fmtDate($dateFrom),
            'dateTo'   => $this->fmtDate($dateTo),
        ), $limit);
    }

    public function orders($dateFrom, $dateTo, int $limit = 500, int $page = 1): array
    {
        return $this->get('/api/orders', array(
            'dateFrom' => $this->fmtDate($dateFrom),
            'dateTo'   => $this->fmtDate($dateTo),
            'page'     => $page,
            'limit'    => $limit,
        ));
    }

    public function salesAll($dateFrom, $dateTo, int $limit = 500): array
    {
        return $this->getAllPages('/api/sales', array(
            'dateFrom' => $this->fmtDate($dateFrom),
            'dateTo'   => $this->fmtDate($dateTo),
        ), $limit);
    }

	public function stocksAll($dateFrom): array
	{
		$payload = $this->get('/api/stocks', array(
			'dateFrom' => $this->fmtDate($dateFrom),
		));

		return (isset($payload['data']) && is_array($payload['data']))
			? $payload['data']
			: $payload;
	}

    public function incomesAll($dateFrom, $dateTo, int $limit = 500): array
    {
        return $this->getAllPages('/api/incomes', array(
            'dateFrom' => $this->fmtDate($dateFrom),
            'dateTo'   => $this->fmtDate($dateTo),
        ), $limit);
    }
}
