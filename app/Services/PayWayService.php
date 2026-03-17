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
        return base64_encode(hash_hmac('sha512', $hash_str, config('payway.api_key'), true));
    }

	public function merchantId() {
		return config('payway.merchant_id');
	}

	public function lifetime() {
		return config('payway.lifetime', 15);
	}

    public function purchase(string $tran_id, float $amount, array $options = [])
    {
        $req_time = date('YmdHis');
        $merchant_id = $this->merchantId();
        $amount = number_format($amount, 2, '.', ''); // Ensure 2 decimal places
        
        $firstname = $options['firstname'] ?? 'Guest';
        $lastname = $options['lastname'] ?? 'User';
        $email = $options['email'] ?? 'guest@example.com';
        $phone = $options['phone'] ?? '012345678';
        $type = $options['type'] ?? 'purchase';
        $payment_option = $options['payment_option'] ?? 'abapay_khqr';
        $currency = $options['currency'] ?? 'USD';
        
        $items = $options['items'] ?? '';
        if (is_array($items)) {
            $items = base64_encode(json_encode($items));
        }
        
        $shipping = $options['shipping'] ?? '';
        $return_url = isset($options['return_url']) ? base64_encode($options['return_url']) : '';
        $cancel_url = isset($options['cancel_url']) ? base64_encode($options['cancel_url']) : '';
        $continue_success_url = $options['continue_success_url'] ?? '';
        $custom_fields = $options['custom_fields'] ?? '';

        // ABA PayWay v2 Hash Sequence:
        // merchant_id + tran_id + amount + items + shipping + firstname + lastname + email + phone + type + payment_option + return_url + cancel_url + continue_success_url + currency + custom_fields + req_time
        $hash_str = $merchant_id . $tran_id . $amount . $items . $shipping . $firstname . $lastname . $email . $phone . $type . $payment_option . $return_url . $cancel_url . $continue_success_url . $currency . $custom_fields . $req_time;
        
        $hash = $this->hash($hash_str);

        $data = [
            'req_time' => $req_time,
            'merchant_id' => $merchant_id,
            'tran_id' => $tran_id,
            'amount' => $amount,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone,
            'type' => $type,
            'payment_option' => $payment_option,
            'items' => $items,
            'shipping' => $shipping,
            'currency' => $currency,
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
            'continue_success_url' => $continue_success_url,
            'custom_fields' => $custom_fields,
            'hash' => $hash,
        ];

        return $this->create($data);
    }

	public function checkTransaction($tranNo) : array
	{
		$merchantId = $this->merchantId();
		$req_time = date('YmdHis');

        // Some versions of PayWay v2 use a query string format for check-transaction
        $params = [
            'merchant_id' => $merchantId,
            'req_time' => $req_time,
            'tran_id' => $tranNo,
        ];
        ksort($params);
        $hash_str = http_build_query($params);
        
		$data = [
			'req_time' => $req_time,
			'merchant_id' => $merchantId,
			'tran_id' => $tranNo,
			'hash' => $this->hash($hash_str),
		];
		$res = Http::asForm()->post(config('payway.api_url') .'/payments/check-transaction', $data)->json();
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
