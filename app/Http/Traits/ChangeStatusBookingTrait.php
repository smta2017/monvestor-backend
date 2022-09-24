<?php

namespace App\Http\Traits;

use App\Http\Resources\Provider\BookingDetailsResource;
use App\Models\User;
use App\Repositories\Eloquent\User\TransactionRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\BookingLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait ChangeStatusBookingTrait
{
    use ApiResponseTrait, FCMTrait;

    /**
     * @var BookingLog
     */
    protected $bookingLogModel;

    /**
     * @var TransactionRepository
     */
    protected $transaction;

    /**
     * BookingRepository constructor.
     * @param BookingLog $bookingLog
     * @param TransactionRepository $transaction
     */
    public function __construct(BookingLog $bookingLog, TransactionRepository $transaction)
    {
        $this->bookingLogModel = $bookingLog;
    }

    /**
     * @param $request
     * @param $booking
     * @param $zone
     * @return JsonResponse
     */
    private function changeInstants($request, $booking, $zone): JsonResponse
    {
        //  5 => Expired => Or Ended
        if ($request->status == '5' && $booking->status == ' 2') {
            $endBooking = Carbon::parse($booking->to_hour);

            $hours = Carbon::now()->diffInHours($endBooking);

            $extra_hours = $hours >= 0 ? $hours + 1 : 0;

            $price_extra_hours = $zone->price_per_hour * $extra_hours;

            $total_cost = $booking->price + $price_extra_hours;

            $booking->update([
                'status' => $request->status,
                'extra_hours' => $extra_hours,
                'total_cost' => $total_cost
            ]);

            $this->firebase($booking->id, $request->status);
            $this->bookingLog('2', $booking->zone_id, $booking->user_id, $extra_hours, $total_cost);
            $user = Auth::user();
            $userBooking = User::find($booking->user_id);
            $this->sendTo($user['device_token'], 'test', 'test2', new BookingDetailsResource($booking));
            $this->sendTo($userBooking['device_token'], 'test', 'test2', new BookingDetailsResource($booking));
            return $this->apiResponse(__('lang.general.successMsg.successfully'), new BookingDetailsResource($booking));
        }

        //  2 => Accept
        if ($request->status == '2' && $booking->status == '1') {
            $booking->update(['status' => $request->status]);

            $this->firebase($booking->id, $request->status);

            $this->bookingLog('1', $booking->zone_id, $booking->user_id);
            $user = Auth::user();
            $userBooking = User::find($booking->user_id);
            $this->sendTo($user['device_token'], 'test', 'test2', new BookingDetailsResource($booking));
            $this->sendTo($userBooking['device_token'], 'test', 'test2', new BookingDetailsResource($booking));
            return $this->apiResponse(__('lang.general.successMsg.successfully'), new BookingDetailsResource($booking));
        }

        //  3 => Reject
        if ($request->status == '3' && $booking->status == '1') {
            $booking->update(['status' => $request->status]);

            $this->firebase($booking->id, $request->status);

            // $this->bookingLog($booking->status, $booking->zone_id, $booking->user_id);
            $user = Auth::user();
            $userBooking = User::find($booking->user_id);
            $this->sendTo($user['device_token'], 'test', 'test2', new BookingDetailsResource($booking));
            $this->sendTo($userBooking['device_token'], 'test', 'test2', new BookingDetailsResource($booking));
            return $this->apiResponse(__('lang.general.successMsg.successfully'), new BookingDetailsResource($booking));
        }
        return $this->apiResponse(__('lang.wallet.errorMsg.canNotCharge'), null, 401,['result'=>__('lang.wallet.errorMsg.canNotCharge')]);
    }

    /**
     * @param $request
     * @param $booking
     * @param $zone
     * @return JsonResponse
     */
    private function changeSub($request, $booking, $zone): JsonResponse
    {
        if ($request->status == '2' && $booking->status == '1') {
            $booking->update(['status' => $request->status]);

            $this->firebase($booking->id, $request->status);

            $this->bookingLog('1', $booking->zone_id, $booking->user_id);

            return $this->apiResponse(__('lang.general.successMsg.successfully'), new BookingDetailsResource($booking));
        }

        //  5 => Expired => Ended
        if ($request->status == '5' && $booking->status == '2') {
            $endBooking = Carbon::parse($booking->to_day);

            $today = Carbon::today();

            if ($endBooking < $today) return $this->forceGetOut($request->status, $booking, $zone);

            $user = $this->bookingLogModel->whereUserIdAndZoneId($booking->user_id, $booking->zone_id)->latest()->first();

            $status = 1;

            if ($user) $user->status == 1 ? $status = 2 : $status = 1;

            $this->firebase($booking->id, $request->status);

            $this->bookingLog($status, $booking->zone_id, $booking->user_id);

            return $this->apiResponse(__('lang.general.successMsg.successfully'), new BookingDetailsResource($booking));
        }
        //  3 => Reject
        if ($request->status == '3' && $booking->status == '1') {
            $booking->update(['status' => $request->status]);

            $this->firebase($booking->id, $request->status);

            return $this->apiResponse(__('lang.general.successMsg.successfully'), new BookingDetailsResource($booking));
        }
        return $this->apiResponse(__('lang.wallet.errorMsg.canNotCharge'), null, 401,['result'=>__('lang.wallet.errorMsg.canNotCharge')]);
    }

    /**
     * @param $status
     * @param $zone_id
     * @param $user_id
     * @param null $extra_hours
     * @param null $total_cost
     */
    private function bookingLog($status, $zone_id, $user_id, $extra_hours = null, $total_cost = null)
    {
        $array = [
            'status' => $status,
            'zone_id' => $zone_id,
            'user_id' => $user_id,
            'provider_id' => Auth::id()
        ];

        if ($extra_hours) $array['extra_hours'] = $extra_hours;

        if ($total_cost) $array['total_cost'] = $total_cost;

        $this->bookingLogModel->create($array);
    }

    /**
     * @param int $booking_id
     * @param int $status
     */
    private function firebase(int $booking_id, int $status)
    {
        $database = app('firebase.database');

        $database->getReference('booking/' . $booking_id)
            ->set(['status' => $status]);
    }

    /**
     * @param $status
     * @param $booking
     * @param $zone
     * @return JsonResponse
     */
    private function forceGetOut($status, $booking, $zone): JsonResponse
    {
        $endBooking = Carbon::parse($booking->to_day);

        $extra_days = $this->extraDays($endBooking);

        $price_extra_days = $zone->price_per_day * $extra_days;

        $total_cost = $booking->price + $price_extra_days;

        $booking->update([
            'status' => 5,
            'extra_days' => $extra_days,
            'total_cost' => $total_cost
        ]);

        $this->firebase($booking->id, $status);

        if ($booking->payment_method == 'cash') {
            $this->transaction->refundPenaltyTransaction($total_cost, $booking->user_id, $booking->zone_id, 'penalty');
        }

        $this->bookingLog('2', $booking->zone_id, $booking->user_id, $extra_days, $total_cost);

        return $this->apiResponse(__('lang.general.successMsg.successfully'), new BookingDetailsResource($booking));
    }

    /**
     * @param $endBooking
     * @return int
     */
    private function extraDays($endBooking): int
    {
        $days = Carbon::today()->diffInDays($endBooking);

        return $days >= 0 ? $days + 1 : 0;
    }
}
