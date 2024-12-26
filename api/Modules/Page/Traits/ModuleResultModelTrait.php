<?php namespace Modules\Page\Traits;

use Imagy;

/**
 * Trait ModuleResultModelTrait
 *
 * @package Modules\Page\Traits
 */
trait ModuleResultModelTrait {
    /**
     * @param $model
     * @return mixed
     */
    protected function getModuleResultModel($model) {
        $table_contents = [];
        if ($model->table_contents) foreach ($model->table_contents as $code => $value) {
            $newVal = [];
            foreach ($value as $k => $v) {
                if ($k == 'description') {
                    $v = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
                } else if ($k == 'image') {
                    $newVal['thumb_url'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                } else if ($k == 'image2') {
                    $newVal['thumb_url2'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                } else if ($k == 'image3') {
                    $newVal['thumb_url3'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                } else if ($k == 'attach') {
                    $newVal['attach_url'] = $v ? media_url_file($v) : '';
                }
                $newVal[$k] = $v;
            }
            $table_contents[$code] = $newVal;
        }
        $model->table_contents = $table_contents;
        $table_images = [];
        if ($model->table_images) foreach ($model->table_images as $code => $value) {
            $newVal = [];
            foreach ($value as $k => $v) {
                if ($k == 'description') {
                    $v = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
                } else if ($k == 'image') {
                    $newVal['thumb_url'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                } else if ($k == 'image2') {
                    $newVal['thumb_url2'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                } else if ($k == 'image3') {
                    $newVal['thumb_url3'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                } else if ($k == 'attach') {
                    $newVal['attach_url'] = $v ? media_url_file($v) : '';
                }
                $newVal[$k] = $v;
            }
            $table_images[$code] = $newVal;
        }
        $model->table_images = $table_images;
        $properties = [];
        if ($model->properties) foreach ($model->properties as $k => $v) {
            if ($k == 'bgImg') {
                $properties['thumb_url'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
            }
            $properties[$k] = $v;
        }
        $model->properties = $properties;
        if (!empty($model->descs)) {
            $descs = [];
            foreach ($model->descs as $desc) {
                $table_contents = [];
                if ($desc->table_contents) foreach ($desc->table_contents as $code => $value) {
                    $newVal = [];
                    foreach ($value as $k => $v) {
                        if ($k == 'description') {
                            $v = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
                        } else if ($k == 'image') {
                            $newVal['thumb_url'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                        } else if ($k == 'image2') {
                            $newVal['thumb_url2'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                        } else if ($k == 'image3') {
                            $newVal['thumb_url3'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                        } else if ($k == 'attach') {
                            $newVal['attach_url'] = $v ? media_url_file($v) : '';
                        }
                        $newVal[$k] = $v;
                    }
                    $table_contents[$code] = $newVal;
                }
                $desc->table_contents = $table_contents;
                $table_images = [];
                if ($desc->table_images) foreach ($desc->table_images as $code => $value) {
                    $newVal = [];
                    foreach ($value as $k => $v) {
                        if ($k == 'description') {
                            $v = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
                        } else if ($k == 'image') {
                            $newVal['thumb_url'] = $v ? media_url_file(Imagy::getThumbnail($v, 'thumb')) : '';
                        }
                        $newVal[$k] = $v;
                    }
                    $table_images[$code] = $newVal;
                }
                $desc->table_images = $table_images;
                $descs[] = $desc;
            }
            $model->descs = $descs;
        }
        // Preview
        $cf_data = $model->cf_data ? $model->cf_data : [];
        $previews = [];
        $previews['layout1'] = !empty($cf_data['configs']) && !empty($cf_data['configs']['template_url']) ? $cf_data['configs']['template_url'] : '';
        if (!empty($cf_data['layouts'])) foreach ($cf_data['layouts'] as $layout) {
            if (!empty($layout['id']) && !empty($layout['preview'])) $previews[$layout['id']] = $layout['preview'];
        }
        $model->layout = $model->layout ? $model->layout : 'layout1';
        $model->preview = !empty($previews[$model->layout]) ? $previews[$model->layout] : '';

        return $model;
    }
}
