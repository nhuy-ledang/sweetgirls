<?php namespace Modules\User\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\User\Repositories\SocialRepository;

class EloquentSocialRepository extends EloquentBaseRepository implements SocialRepository {
    /**
     * @param  mixed $data
     * @return object
     */
    public function create($data) {
        // Delete other provider
        $this->getModel()->where('provider', $data['provider'])->where('user_id', $data['user_id'])->delete();

        return parent::create($data);
    }
}
