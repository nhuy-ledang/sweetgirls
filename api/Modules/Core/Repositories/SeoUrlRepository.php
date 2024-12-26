<?php namespace Modules\Core\Repositories;

interface SeoUrlRepository extends BaseRepository {
    public function getSeoUrlByQuery($query, $lang = 'vi');

    public function getSeoUrlsByKeyword($keyword, $lang = false);
}
