<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Http\Controllers\Api\ApiResponseTrait;
use Modules\User\External\Repositories\Contract\ProvinceRepositoryInterface;
use Modules\User\Http\Resources\ProvinceResource;

class ProvinceController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected ProvinceRepositoryInterface $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;

        // $this->middleware('can:admins_list')->only(['index']);
    }

    public function index()
    {
        try {
            // get provinces lists
            $provinces = $this->provinceRepository->all();

            return $this->respondSuccess(
                ProvinceResource::collection($provinces)->resolve()
            );
        } catch (\Exception $e) {
            Log::error('Error fetching provinces: '.$e->getMessage());

            return $this->respondError('خطا در بازیابی لیست استان ها.', 500); // 500 Internal Server Error
        }
    }
}
