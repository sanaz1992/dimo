<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Modules\Core\Http\Controllers\Api\ApiResponseTrait;
use Illuminate\Routing\Controller;
use Modules\User\External\Repositories\Contract\CityRepositoryInterface;
use Modules\User\Http\Resources\CityResource;
use Modules\User\Requests\CheckProvinceIdRequests;

class CityController extends Controller
{
    use ApiResponseTrait;
    public function __construct(protected CityRepositoryInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;

        // $this->middleware('can:admins_list')->only(['index']);
    }
    public function index(CheckProvinceIdRequests $request)
    {
        $data = $request->all();
        $provinceId = $data['province_id'];
        try {
            //get cities lists
            $cities = $this->cityRepository->all(null, [], [], [
                'where' => [
                    'province_id' => ['=', $provinceId]
                ]
            ]);

            return $this->respondSuccess(
                CityResource::collection($cities)->resolve()
            );
        } catch (\Exception $e) {
            Log::error('Error fetching cities: ' . $e->getMessage());
            return $this->respondError('خطا در بازیابی لیست شهر ها.', 500); // 500 Internal Server Error
        }
    }
}
