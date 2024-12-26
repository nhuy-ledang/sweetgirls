<?php namespace Modules\Core\Traits;

use App;

/**
 * Trait Revision
 * @package Modules\Core\Traits
 */
trait Revision {
    public static function bootRevision() {
        static::updating(function ($model) {
            $model->_saveBeforeUpdate($model);
        });
        static::created(function ($model) {
            $model->_saveWhenCreate($model);
        });
        static::deleting(function ($model) {
            $model->_saveBeforeDelete($model);
        });
    }

    protected function _saveBeforeUpdate($model) {
        return $this->saveRevision($model, 'update');
    }

    protected function _saveWhenCreate($model) {
        return $this->saveRevision($model, 'create');
    }

    protected function _saveBeforeDelete($model) {
        return $this->saveRevision($model, 'delete');
    }

    /**
     * @return \Modules\Core\Entities\Eloquent\CoreModel;
     */
    protected function getRevisionModel($model) {
        return new $model->revision_model;
    }

    protected function getRevisionAttribute($model) {
        $revision = array_only($model->getAttributes(), $this->getRevisionModel($model)->getFillable());
        return $revision;
    }

    protected function saveRevision($model = null, $event = 'save') {
        if (is_null($model)) {
            $model = $this;
        }
        $revision_attribute = $this->getRevisionAttribute($model);
        $model_revision = $this->getRevisionModel($model)->newInstance($revision_attribute);
        $model_revision->entity_id = $this->getkey();
        $model_revision->locale = App::getLocale();
        $model_revision->event = $event;
        if ($current_user = \Sentinel::getUser()) {
            $model_revision->modify_user_id = $current_user->getUserId();
        }
        $model_revision->fill($revision_attribute);
        $model_revision->save();

        return $model_revision;
    }

    public function revisions() {
        return $this->hasMany($this->revision_model, 'entity_id');
    }

    public function revertRevision($revision_id) {

    }
}