<?php namespace Modules\Media\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Media\Repositories\FolderRepository;

class EloquentFolderRepository extends EloquentBaseRepository implements FolderRepository {
    /**
     * @param $id
     * @return string
     */
    public function getPath($id) {
        $model = $this->getModel()->where('id', $id)->select(['id','parent_id'])->first();
        if ($model) {
            if ($model->parent_id) {
                return $this->getPath($model->parent_id) . '_' . $model->id;
            } else {
                return $model->id;
            }
        } else {
            return '';
        }
    }
}
