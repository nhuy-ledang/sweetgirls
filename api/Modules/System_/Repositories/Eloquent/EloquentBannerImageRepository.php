<?php namespace Modules\System\Repositories\Eloquent;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\System\Repositories\BannerImageRepository;

class EloquentBannerImageRepository extends EloquentBaseRepository implements BannerImageRepository {
    /**
     * @param  mixed $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($data) {
        Cache::forget("media_banners.all");

        return parent::create($data);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param  array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($model, $data) {
        Cache::forget("media_banners.all");
        Cache::forget("media_banners.$model->banner_id");

        return parent::update($model, $data);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     * @throws \Exception
     */
    public function destroy($model) {
        Cache::forget("media_banners.all");
        Cache::forget("media_banners.$model->banner_id");

        return parent::destroy($model);
    }
}
