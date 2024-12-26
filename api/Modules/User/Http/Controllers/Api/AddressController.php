<?php namespace Modules\User\Http\Controllers\Api;

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

        $this->middleware('auth.usr');

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
     *   path="/backend/users/{id}/address_all",
     *   summary="Get User Address All",
     *   operationId="getUserAddressAll",
     *   tags={"BackendUserAddresses"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function allAddresses($id) {
        try {
            $results = $this->model_repository->getModel()->where('user_id', $id)->orderBy('address_1')
                ->select([
                    'user__addresses.*',
                    \DB::raw('(select name from `loc__provinces` where id = province_id limit 1) as province'),
                    \DB::raw('(select name from `loc__districts` where id = district_id limit 1) as district'),
                ])
                ->get();
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
     *   path="/backend/users/{id}/addresses",
     *   summary="Create User Address",
     *   operationId="createUserAddress",
     *   tags={"BackendUserAddresses"},
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
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function storeAddresses($id) {
        try {
            // Check myself
            if ($id != $this->auth->id) return $this->errorForbidden();
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

            $model = $this->model_repository->create(array_merge($input, ['user_id' => $id]));
            $is_default = $this->request->get('is_default');
            if ($is_default && (boolean)$is_default) {
                $this->auth->address_id = $model->id;
                $this->auth->address = $model->address_1;
                $this->auth->save();
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/users/{id}/addresses/{address_id}",
     *   summary="Update Address",
     *   operationId="updateUserAddress",
     *   tags={"BackendUserAddresses"},
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
     *   @OA\Parameter(name="id", in="path", description="User Id", example="1"),
     *   @OA\Parameter(name="address_id", in="path", description="Address Id", example="1"),
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function updateAddresses($id, $address_id) {
        try {
            // Check myself
            if ($id != $this->auth->id) return $this->errorForbidden();
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
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($address_id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->model_repository->findByAttributes(['id' => $address_id, 'user_id' => $id]);
            if (!$model) return $this->errorNotFound();

            // Update Model
            $model = $this->model_repository->update($model, $input);
            $is_default = $this->request->get('is_default');
            if ($is_default && (boolean)$is_default) {
                $this->auth->address_id = $model->id;
                $this->auth->address = $model->address_1;
                $this->auth->save();
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/users/{id}/addresses/{address_id}",
     *   summary="Delete User Address",
     *   operationId="deleteUserAddress",
     *   tags={"BackendUserAddresses"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example="1"),
     *   @OA\Parameter(name="address_id", in="path", description="Address Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroyAddresses($id, $address_id) {
        try {
            // Check myself
            if ($id != $this->auth->id) return $this->errorForbidden();
            $model = $this->model_repository->findByAttributes(['id' => $address_id, 'user_id' => $id]);
            if (!$model) return $this->errorNotFound();
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
