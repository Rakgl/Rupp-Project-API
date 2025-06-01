<?php

namespace App\Helpers;

use App\Models\BonusSetting;
use App\Models\Customer;
use App\Models\CustomerPoint;
use App\Models\CustomerPointTransaction;
use App\Models\Membership;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\TopUpTransaction;
use Illuminate\Support\Str;


class CustomerHelper
{
    /**
     * Update customer points and membership level if applicable.
     *
     * @param  Customer  $customer
     * @param  int  $points
     * @return void
     */
    public static function updateCustomerMembership(String $customerId): void
    {
		$customer = Customer::find($customerId);
        $newMembership = Membership::where('required_points', '<=', $customer->points)
                                   ->orderBy('required_points', 'desc')
                                   ->first();

        if ($newMembership && $customer->membership_id !== $newMembership->id) {
            $customer->membership_id = $newMembership->id;
            $customer->save();
        }
    }

	public static function customerPoint(String $customerId, $topUpTransaction) : void{
		$customer = Customer::find($customerId);
		$membership = $customer->membership;

		$topUpAmount = $topUpTransaction->amount;
		if ($membership) {
			if ($topUpAmount >= $membership->amount) {
				$point = self::calculateCustomerPoint($topUpAmount, $membership->amount, $membership->point_earned);

				if($point > 0) {
					$customer->points = $customer->points + $point;
					$customer->save();

					$customerPoint = CustomerPointTransaction::create([
						'customer_id' => $customer->id,
						'points' => $point,
						'type' => 'TOP_UP', 
						'reference_no' => $topUpTransaction->transaction_no,
					]);

					if ($customerPoint) {
						self::updateCustomerMembership($customer->id);
					}
				}
			}
		}
	}

	public static function customerPointWithdrawal(String $customerId, $transaction) : void 
	{
		$customer = Customer::find($customerId);
		$membership = $customer->membership;

		$topUpAmount = $transaction->amount;
		if ($membership) {
			$point = self::calculateCustomerPoint($topUpAmount, $membership->amount, $membership->point_earned);

			if($point > 0) {
				$customer->points = $customer->points - $point;
				$customer->save();

				$point = $point * -1;
				CustomerPointTransaction::create([
					'customer_id' => $customer->id,
					'points' => $point,
					'type' => 'WITHDRAWAL', 
					'reference_no' => $transaction->transaction_no,
				]);
			}
		}
	}
	public static function calculateCustomerPoint($topUpAmount , $membershipAmount, $pointEarned) {
		$point = 0;
		if ($membershipAmount > 0 && $pointEarned > 0) {
			$point = $topUpAmount / $membershipAmount * $pointEarned;
		}
		return $point;
	}

	public static function getBonusBalance(Customer $customer) : string
	{
		$bonusKey = Setting::where('setting_key', 'first_register_get_amount')->first();
		$bonus = $bonusKey ? $bonusKey->setting_value : 0;
		if ($bonus && $bonus > 0) {
			$paymentMethod = PaymentMethod::active()->first();
			$topUp = TopUpTransaction::create([
				'transaction_no' => (string) Str::uuid(),
				'customer_id' => $customer->id,
				'amount' => $bonus,
				'payment_method_id' => $paymentMethod ? $paymentMethod->id : null,
				'description' => 'First register get ' . number_format($bonus, 0, '.', '') . 'KHR as a welcome gift',
				'status' => 'SUCCESS',
				'type' => 'BONUS',
			]);

			if ($topUp) {
				$customer->update([
					'balance' => $customer->balance + $topUp->amount,
					'has_received_bonus' => true
				]);

				return 'Congratulation, we’ve added ' . number_format($topUp->amount, 0, '.', '') . 'KHR to your wallet as a welcome gift. Use it to explore and enjoy charging!';
			}
		}
		return '';
	}
}
