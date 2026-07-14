<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Modules\Core\Http\Controllers\Api\ApiResponseTrait;
use Illuminate\Routing\Controller;
use Modules\User\Entities\CustomerLevel;
use Modules\User\Filters\CustomerLevelFilter;
use Modules\User\Http\Resources\CustomerLevelResource;
use Modules\User\Requests\StoreCustomerLevelRequests;
use Modules\User\Services\CustomerLevelService;

class CustomerLevelController extends Controller
{
    use ApiResponseTrait;
    public function __construct(protected CustomerLevelService $customerLevelService)
    {
        // $this->middleware('can:customer_level_list')->only(['index']);
        $this->middleware('can:customer_level_create')->only(['store']);
    }
    public function index(CustomerLevelFilter $filter)
    {
        try {
            $customerLevels = $this->customerLevelService->list(filter: $filter);

            return $this->respondSuccess(
                CustomerLevelResource::collection($customerLevels)->resolve()
            );
        } catch (\Exception $e) {
            Log::error('Error fetching customer levels: ' . $e->getMessage());
            return $this->respondError('خطا در بازیابی لیست وضعیت های مشتری.', 500); // 500 Internal Server Error
        }
    }

    public function store(StoreCustomerLevelRequests $request)
    {
        $data = $request->all();
        $customerLevel = $this->customerLevelService->create($data);
        return $this->respondSuccess(
            (new CustomerLevelResource($customerLevel))->resolve()
        );
    }

    public function show(CustomerLevel $customerLevel)
    {
        $customerLevel->load('leads');
        return $this->respondSuccess(
            (new CustomerLevelResource($customerLevel))->resolve()
        );
    }
    public function update(StoreCustomerLevelRequests $request, CustomerLevel $customerLevel)
    {
        $customerLevel = $this->customerLevelService->update($customerLevel, $request->validated());
        return $this->respondSuccess(
            new CustomerLevelResource($customerLevel),
            'وضعیت با موفقیت بروزرسانی شد.'
        );
    }

    public function destroy(CustomerLevel $customerLevel)
    {
        try {
            $this->customerLevelService->delete($customerLevel);

            return $this->respondSuccess(
                'وضعیت با موفقیت حذف شد.'
            );
        } catch (\Exception $e) {
            // مدیریت خطای مربوط به وابستگی‌ها (Event Listener ها)
            return $this->respondError(
                $e->getMessage(),
                400
            );
        }
    }
}
