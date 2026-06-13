<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponApiController extends Controller
{
    public function __construct(private readonly CouponService $couponService) {}

    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'order_total' => ['required', 'numeric', 'min:0'],
        ]);

        $result = $this->couponService->validate($request->code, $request->order_total);

        if (!$result['valid']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json([
            'discount' => $result['discount'],
            'coupon' => $result['coupon']->only(['code', 'name', 'type', 'value']),
        ]);
    }
}
