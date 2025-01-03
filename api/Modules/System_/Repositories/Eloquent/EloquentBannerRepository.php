<?php namespace Modules\System\Repositories\Eloquent;

use Imagy;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\System\Repositories\BannerRepository;

class EloquentBannerRepository extends EloquentBaseRepository implements BannerRepository {
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
        Cache::forget("media_banners.$model->id");
        return parent::update($model, $data);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     * @throws \Exception
     */
    public function destroy($model) {
        Cache::forget("media_banners.all");
        Cache::forget("media_banners.$model->id");
        return parent::destroy($model);
    }

    /**
     * Get all banner
     *
     * @return array
     */
    public function getAll() {
        //return Cache::remember("media_banners.all", $this->cacheExpireMax, function() {
            $data = [];
            $results = $this->getModel()->leftJoin('media__banner_images as bi', 'media__banners.id', '=', 'bi.banner_id')
                ->where('media__banners.status', 1)
                ->selectRaw('`media__banners`.*, `bi`.`title`, `bi`.`caption`, `bi`.`link`, `bi`.`image`')
                ->orderBy('bi.sort_order', 'asc')
                ->get();
            foreach ($results as $result) {
                if (!isset($data[$result['id']])) {
                    $data[$result['id']] = ['id' => (int)$result['id'], 'name' => $result['name'], 'banners' => []];
                }
                $data[$result['id']]['banners'][] = [
                    'title'     => $result->title,
                    'caption'   => $result->caption,
                    'link'      => $result->link,
                    'raw_url'   => media_url_file($result->image),
                    'large_url' => media_url_file(Imagy::getThumbnail($result->image, 'large')),
                    'thumb_url' => media_url_file(Imagy::getThumbnail($result->image, 'thumb')),
                ];
            }
            $data = array_values($data);
            $sort_order = [];
            foreach ($data as $key => $value) {
                $sort_order[$key] = $value['id'];
            }

            array_multisort($sort_order, SORT_ASC, $data);

            return $data;
        //});
    }

    /**
     * Get banner
     *
     * @param $id
     * @return array
     */
    public function getBanners($id) {
        //return Cache::remember("media_banners.$id", $this->cacheExpireMax, function() use ($id) {
            $data = [];
            $results = $this->getModel()->leftJoin('media__banner_images as bi', 'media__banners.id', '=', 'bi.banner_id')
                ->where('media__banners.id', $id)
                ->where('media__banners.status', 1)
                ->selectRaw('`bi`.*')
                ->orderBy('bi.sort_order', 'asc')->get();
            foreach ($results as $result) {
                $data[] = [
                    'title'     => $result->title,
                    'caption'   => $result->caption,
                    'link'      => $result->link,
                    'raw_url'   => media_url_file($result->image),
                    'large_url' => media_url_file(Imagy::getThumbnail($result->image, 'large')),
                    'thumb_url' => media_url_file(Imagy::getThumbnail($result->image, 'thumb')),
                ];
            }

            return $data;
        //});
    }
}
