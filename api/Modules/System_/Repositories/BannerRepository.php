<?php namespace Modules\System\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface BannerRepository extends BaseRepository {
    /**
     * Get all banner
     * @return array
     */
    public function getAll();

    /**
     * Get banner
     * @param $id
     * @return array
     */
    public function getBanners($id);
}
