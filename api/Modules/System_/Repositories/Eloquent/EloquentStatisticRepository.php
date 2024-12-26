<?php namespace Modules\System\Repositories\Eloquent;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Place\Entities\Media;
use Modules\Place\Entities\Place;
use Modules\Place\Entities\Review;
use Modules\System\Repositories\StatisticRepository;
use Modules\User\Entities\Sentinel\User;

class EloquentStatisticRepository extends EloquentBaseRepository implements StatisticRepository {
    public function all() {
        return Cache::remember("statistic.all", $this->cacheExpire, function() {
            $data = new \stdClass();

            foreach (parent::all() as $row) {
                $data->{$row->code} = (float)$row->value;
            }

            return $data;
        });
    }

    /**
     * Create or update
     * @param $code
     * @param $value
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createOrUpdate($code, $value) {
        $model = $this->findByAttributes(['code' => $code]);
        if (!$model) {
            $this->create(['code' => $code, 'value' => $value]);
        } else {
            $this->update($model, ['value' => $value]);
        }

        return $model;
    }

    public function indexData() {
        Cache::forget('statistic.all');

        $data = [
            'places'   => Place::count(),
            'reviews'  => Review::count(),
            'pictures' => Media::count(),
            'members'  => User::count(),
            //'lists'    => 21104843,
            //'checkins' => 503280,
        ];

        foreach ($data as $code => $value) {
            $this->createOrUpdate($code, $value);
        }
    }
}
