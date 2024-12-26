<?php namespace Modules\Core\Repositories\Eloquent;

use Modules\Core\Repositories\SeoUrlRepository;

class EloquentSeoUrlRepository extends EloquentBaseRepository implements SeoUrlRepository {
    public function getSeoUrlByQuery($query, $lang = 'vi') {
        return $this->getModel()->where('query', $query)->where('lang', $lang)->first();
    }

    public function getSeoUrlsByKeyword($keyword, $lang = false) {
        $results = $this->getModel()->where(function($query) use ($keyword) {
            $query->where('keyword', trim($keyword))->orWhere('keyword', seo_url($keyword));
        });
        if ($lang) $results = $results->where('lang', $lang);
        $results = $results->get();

        return $results;
    }
}
