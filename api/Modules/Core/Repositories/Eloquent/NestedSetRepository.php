<?php namespace Modules\Core\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Content\Entities\GlobalScope\CategoryGlobalScope;
use Modules\Core\Traits\NodeTreeTrait;

/**
 * Class NestedSetRepository
 * @package Modules\Core\Repositories\Eloquent
 * @property \Eloquent $model;
 */
trait NestedSetRepository
{
    use NodeTreeTrait;
    /**
     * Get a query builder of list node
     * @param  $root The id of node
     * @return Query Builder
     */
    public function getQueryBuilder($root = 1)
    {
        $table_name  = $this->model->getTable();
        $pa          = $this->model->newQueryWithoutScopes()->select($table_name . '.lft', $table_name . '.rgt')->where($table_name . '.id', $root)->first();
        if(!$pa){
            if($root == 1){
                // check it root exist or not then create it
                $pa = $this->model->firstOrCreate(['lft'=>1,'rgt'=>2,'id'=>$root]);
                if(isset($pa->name) && is_null($pa->name)){
                    $pa->name = "Root";
                    $pa->save();
                }
            }else{
                throw new ModelNotFoundException(trans('core:exceptions.not found your node'));
            }

        }
        $descandants = $this->model->whereBetween($table_name . '.lft', [$pa->lft, $pa->rgt])->orderBy($table_name . '.lft', 'asc');
        return $descandants;
    }

    /**
     * @param int $root
     * @return mixed
     */
    public function all($root = 1)
    {
        return $this->getQueryBuilder($root)->get();
    }

    /**
     * Get parent node
     * @param  $node
     * @param  string $option [all: get all ancestor nodes, 1: get the direct parent node]
     * @return mixed
     */
    public function get_childs_node($node)
    {
        $builder = $this->model->newQueryWithoutScopes()->where('lft', '>', $node->lft)->where('rgt', '<', $node->rgt);
        $path = $builder->orderBy('lft', 'asc')->get();
        return $path;
    }
    public function path_to_node($node, $option = 'all')
    {
        $builder = $this->model->newQueryWithoutScopes()->where('lft', '<', $node->lft)->where('rgt', '>', $node->rgt);
        // ->orderBy('lft','asc');

        if ($option == 'all') {
            $path = $builder->orderBy('lft', 'asc')->get();
        } elseif ($option == 1) {
            $path = $builder->orderBy('lft', 'desc')->first();
        } else {
            throw new \UnexpectedValueException(trans('option value are not valid'));
        }

        return $path;
    }

    public function no_of_childs($node)
    {
        return ($node->rgt - $node->lft - 1) / 2;
    }

    public function updateParentNodeWhenInsert($node)
    {
        $this->model->newQueryWithoutScopes()->where('rgt', '>=', $node->rgt)->increment('rgt', 2);
        $this->model->newQueryWithoutScopes()->where('lft', '>', $node->rgt)->increment('lft', 2);
    }

    public function updateParentNodeWhenDelete($node)
    {
        $no_of_childs = $this->no_of_childs($node);
        $decrement    = ($no_of_childs + 1) * 2;
        $this->model->newQueryWithoutScopes()->where('rgt', '>', $node->rgt)->decrement('rgt', $decrement);
        $this->model->newQueryWithoutScopes()->where('lft', '>', $node->lft)->decrement('lft', $decrement);
    }

    /**
     * Create a model resource
     * @return mixed
     */
    public function create($data)
    {
        $data['parent_id'] = array_get($data, 'parent_id', 1);
        $parent_node       = $this->model->newQueryWithoutScopes()->find($data['parent_id']);
        $data['level']     = $parent_node->level + 1;
        $data['lft']       = $parent_node->rgt;
        $data['rgt']       = $parent_node->rgt + 1;

        DB::beginTransaction();

        $this->updateParentNodeWhenInsert($parent_node);
        $model = $this->model->create($data);

        DB::commit();

        return $model;
    }

    /**
     * Delete a model
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if ($id == 1) {
            throw new \UnexpectedValueException(trans('exceptions.can not update this record'));
        }
        $node   = $this->model->find($id);
        $result = false;
        DB::beginTransaction();

        try {
            $this->model->newQueryWithoutScopes()->whereBetween('lft', [$node->lft, $node->rgt])->delete();
            $this->updateParentNodeWhenDelete($node);
            $result = true;
        } catch (\Exception $e) {
            $result = false;
            DB::rollback();
        }

        DB::commit();

        return $result;
    }





}