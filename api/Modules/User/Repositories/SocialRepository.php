<?php namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface SocialRepository extends BaseRepository {
    /**
     * @param  mixed $data
     * @return object
     */
    public function create($data);
}
