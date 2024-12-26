<?php namespace Modules\User\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\User\Repositories\AddressRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class AddressController
 *
 * @package Modules\User\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2021-06-21
 */
class AddressController extends ApiBaseModuleController {
    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    public function __construct(Request $request,
                                AddressRepository $address_repository,
                                UserRepository $user_repository) {
        $this->model_repository = $address_repository;
        $this->user_repository = $user_repository;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [];
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [];
    }

    /**
     * @OA\Get(
     *   path="/addresses_all",
     *   summary="Get User Address All",
     *   operationId="getUserAddressAll",
     *   tags={"UserAddresses"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $fields = ['user__addresses.*', 'p.name as province', 'd.name as district', 'w.name as ward', 'd.vt_province_id', 'd.vt_id as vt_district_id', 'w.vt_id as vt_ward_id'];
            $results = $this->model_repository->getModel()
                ->leftJoin('loc__districts as d', 'd.id', 'user__addresses.district_id')
                ->leftJoin('loc__provinces as p', 'p.id', 'user__addresses.province_id')
                ->leftJoin('loc__wards as w', 'w.id', 'user__addresses.ward_id')
                ->where('user__addresses.user_id', $this->auth->id)->orderBy('user__addresses.address_1')
                ->select($fields)->get();
            $output = [];
            foreach ($results as $result) {
                $result['is_default'] = $result->id == $this->auth->address_id;
                $output[] = $result;
            }

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/addresses",
     *   summary="Create User Address",
     *   operationId="createUserAddress",
     *   tags={"UserAddresses"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="first_name", type="string", example=""),
     *       @OA\Property(property="phone_number", type="string", example=""),
     *       @OA\Property(property="type", type="string", example="home"),
     *       @OA\Property(property="country_id", type="integer", example=1),
     *       @OA\Property(property="province_id", type="integer", example=1),
     *       @OA\Property(property="address_1", type="string", example="address"),
     *       @OA\Property(property="address_2", type="string", example="address"),
     *       @OA\Property(property="is_default", type="integer", example="1"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function store() {
        try {
            $input = $this->request->only(['first_name', 'phone_number', 'type', 'address_1', 'address_2']);
            $country_id = $this->request->get('country_id');
            $input['country_id'] = ($country_id && intval($country_id)) ? $country_id : null;
            $province_id = $this->request->get('province_id');
            $input['province_id'] = ($province_id && intval($province_id)) ? $province_id : null;
            $district_id = $this->request->get('district_id');
            $input['district_id'] = ($district_id && intval($district_id)) ? $district_id : null;
            $ward_id = $this->request->get('ward_id');
            $input['ward_id'] = ($ward_id && intval($ward_id)) ? $ward_id : null;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Create model
            $model = $this->model_repository->create(array_merge($input, ['user_id' => $this->auth->id]));
            $is_default = $this->request->get('is_default');
            if ($is_default && (boolean)$is_default) {
                $this->auth->address_id = $model->id;
                $this->auth->address = $model->address_1;
                $this->auth->save();
            }

            // Get name province & district
            /*$model = $model->select([
                'user__addresses.*',
                \DB::raw('(select name from `loc__provinces` where id = province_id limit 1) as province'),
                \DB::raw('(select name from `loc__districts` where id = district_id limit 1) as district'),
                \DB::raw('(select name from `loc__wards` where id = ward_id limit 1) as ward'),
            ])->where('id',$model->id)->first();*/

            $model = $this->model_repository->getModel()
                ->leftJoin('loc__provinces as p', 'p.id', 'province_id')
                ->leftJoin('loc__districts as d', 'd.id', 'district_id')
                ->leftJoin('loc__wards as w', 'w.id', 'ward_id')
                ->where('user__addresses.id', $model->id)
                ->select(['user__addresses.*', 'p.name as province', 'd.name as district', 'w.name as ward', 'd.vt_province_id', 'd.vt_id as vt_district_id', 'w.vt_id as vt_ward_id'])->first();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/addresses/{id}",
     *   summary="Update User Address",
     *   operationId="updateUserAddress",
     *   tags={"UserAddresses"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="first_name", type="string", example=""),
     *       @OA\Property(property="phone_number", type="string", example=""),
     *       @OA\Property(property="type", type="string", example="home"),
     *       @OA\Property(property="country_id", type="integer", example=1),
     *       @OA\Property(property="province_id", type="integer", example=1),
     *       @OA\Property(property="address_1", type="string", example="address"),
     *       @OA\Property(property="address_2", type="string", example="address"),
     *       @OA\Property(property="is_default", type="integer", example="1"),
     *     ),
     *   ),
     *   @OA\Parameter(name="id", in="path", description="Address Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function update($id) {
        try {
            $input = $this->request->only(['first_name', 'phone_number', 'type', 'address_1', 'address_2']);
            $country_id = $this->request->get('country_id');
            $input['country_id'] = ($country_id && intval($country_id)) ? $country_id : null;
            $province_id = $this->request->get('province_id');
            $input['province_id'] = ($province_id && intval($province_id)) ? $province_id : null;
            $district_id = $this->request->get('district_id');
            $input['district_id'] = ($district_id && intval($district_id)) ? $district_id : null;
            $ward_id = $this->request->get('ward_id');
            $input['ward_id'] = ($ward_id && intval($ward_id)) ? $ward_id : null;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->model_repository->findByAttributes(['id' => $id, 'user_id' => $this->auth->id]);
            if (!$model) return $this->errorNotFound();
            // Update model
            $model = $this->model_repository->update($model, $input);
            $is_default = $this->request->get('is_default');
            if ($is_default && (boolean)$is_default) {
                $this->auth->address_id = $model->id;
                $this->auth->address = $model->address_1;
                $this->auth->save();
            }

            $model = $this->model_repository->getModel()->leftJoin('loc__districts as d', 'd.id', 'district_id')
                ->where('user__addresses.id', $id)
                ->select(['user__addresses.*', 'd.name as district', 'd.vt_province_id', 'd.vt_id as vt_district_id'])->get();
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/addresses/{id}",
     *   summary="Delete User Address",
     *   operationId="deleteUserAddress",
     *   tags={"UserAddresses"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Address Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->findByAttributes(['id' => $id, 'user_id' => $this->auth->id]);
            if (!$model) return $this->errorNotFound();
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
