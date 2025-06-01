<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayWayService {

	public function __construct() {
	}

	public function apiUrl() {
		return config('payway.api_url');
	}

	public function hash($hash_str) {
        $hash = base64_encode(hash_hmac('sha512', $hash_str, config('payway.api_key'), true));
        return $hash;
    }

	public function merchantId() {
		return config('payway.merchant_id');
	}

	public function lifetime() {
		return config('payway.lifetime');
	}

	public function checkTransaction($tranNo) : array
	{
		$merchantId = $this->merchantId();
		$req_time = date('YYYYmmddHis');

		$data = [
			'req_time' => $req_time,
			'merchant_id' => $merchantId,
			'tran_id' => $tranNo,
			'hash' => $this->hash(
				$req_time . $merchantId . $tranNo
			),
		];
		$res = Http::asForm()->post(config('payway.api_url') .'/payments/check-transaction-2', $data)->json();
		return $res;
    }

	// public function verifyTransaction($tranNo) : array
	// {
	// 	$response =  $this->checkTransaction($tranNo);
	// 	Log::info('VerifyAbaTopUpJob' , $response);
	// 	if ($transaction && isset($res['data']['payment_status_code'])) {
	// 		$statusCode = $res['data']['payment_status_code'];

	// 		$status = match (true) {
	// 			$statusCode === 0 => 'SUCCESS',
	// 			$statusCode < 3 => 'PENDING',
	// 			default => 'FAILED',
	// 		};

	// 		$transaction->update([
	// 			'status' => $status,
	// 			'payway_status' => $statusCode,
	// 			'payway_check_at' => Carbon::now(),
	// 			'apv' => $res['data']['apv'],
	// 		]);

	// 		if ($transaction->status === 'SUCCESS' && $transaction->status !== $status) {
	// 			$customer = $transaction->customer;
	// 			$customer->balance += $transaction->amount;
	// 			$customer->save();
	// 		}
	// 	}
	// }

	public function create(array $data): array
	{
		$url = config('payway.api_url') . config('payway.api_purchase');
		$response = Http::asForm()->post($url, $data)->json();

		$dataResponse = [
			'success' => false,
			'message' => 'Transaction failed',
			'data' => $response
		];

		if (!$response || !isset($response['status'])) {
			return $dataResponse;
		}

		$isSuccessful = isset($response['status']['code']) && $response['status']['code'] === "00" ||
						isset($response['status']['tran_id']) && $response['status']['tran_id'];

		if ($isSuccessful) {
			return [
				'success' => true,
				'message' => 'Success',
				'data' => [
					'transaction_no' => $response['status']['tran_id'],
					'qr_string' => $response['qr_string'] ?? null,
					'abapay_deeplink' => $response['abapay_deeplink'],
					'checkout_qr_url' => $response['checkout_qr_url'] ?? null,
				]
			];
		}

		return $dataResponse;
	}

	public function transactions(array $data): array
	{
		try {
			$url = config('payway.api_url') . config('payway.api_transaction');
			$response = Http::asForm()
				->post($url, $data)
				->json();

			return [
				'success' => true,
				'message' => 'Transactions retrieved successfully',
				'data' => $response ?? []
			];
		} catch (\Exception $e) {
			return [
				'success' => false,
				'message' => 'Failed to retrieve transactions: ' . $e->getMessage(),
				'data' => []
			];
		}
	}

}
