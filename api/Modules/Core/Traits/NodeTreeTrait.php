<?php namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Collection;

/**
 * Trait NodeTreeTrait
 * @package Modules\Core\Traits
 */
trait NodeTreeTrait {

    public function totalChildrenOfNode($node) {
        return ($node->rgt - $node->lft - 1) / 2;
    }

    public function updateParentNodeWhenInsert(Model $node) {
        $this->createModel()->newQueryWithoutScopes()->where('rgt', '>=', $node->rgt)->increment('rgt', 2);
        $this->createModel()->newQueryWithoutScopes()->where('lft', '>', $node->rgt)->increment('lft', 2);
    }

    public function updateParentNodeWhenDelete(Model $node) {
        $no_of_childs = $this->totalChildrenOfNode($node);
        $decrement = ($no_of_childs + 1) * 2;
        $this->createModel()->newQueryWithoutScopes()->where('rgt', '>', $node->rgt)->decrement('rgt', $decrement);
        $this->createModel()->newQueryWithoutScopes()->where('lft', '>', $node->lft)->decrement('lft', $decrement);
    }


    public function addNode(Model $object, $parent_node = null) {
        DB::beginTransaction();
        if (!$parent_node instanceof Model) {
            $parent_node = $this->getNode($parent_node);
        }

        $this->updateParentNodeWhenInsert($parent_node);

        $object_node = $object;

        $object_node->parent_id = $parent_node->getKey();
        $object_node->level = $parent_node->level + 1;
        $object_node->lft = $parent_node->rgt;
        $object_node->rgt = $parent_node->rgt + 1;
        $object_node->save();

        DB::commit();
        return $object_node;

    }

    public function getNode($node) {
        if (is_null($node)) {
            $node = $this->getRootNode();
        } else if ($node instanceof Model) {
            //$node = $this->$node($node->id);
        } else {
            $node = $this->createModel()->findOrFail($node);
        }
        return $node;
    }

    public function removeNode($id) {
        //if ($id == 1) {
        //	throw new \UnexpectedValueException(trans('exceptions.can not update this record'));
        //}
        $node = $this->createModel()->find($id);

        if ($this->totalChildrenOfNode($node) > 0) {
            throw new \InvalidArgumentException('This node has children. Can not move this node');
        }
        $result = false;
        DB::beginTransaction();
        $this->createModel()->newQueryWithoutScopes()->whereBetween('lft', [$node->lft, $node->rgt])->delete();
        $this->updateParentNodeWhenDelete($node);
        $result = true;
        DB::commit();
        return $result;
    }

    public function moveNode(Model $current_node, $parent_node) {
        if ($this->totalChildrenOfNode($current_node) == 0) {
            DB::beginTransaction();
            $this->updateParentNodeWhenDelete($current_node);
            $this->addNode($current_node, $parent_node);
            DB::commit();
        } else {
            throw new \InvalidArgumentException('This node has children. Can not move this node');
        }
    }

    public function getRootNode() {
        $root = $this->createModel()->firstOrNew(['level' => 0]);
        if (!$root->exists) {
            $root->lft = 1;
            $root->rgt = 2;
            $root->save();
        }
        return $root;
    }


    /**
     * Return a new instance of the Eloquent Model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel() {
        return $this->newInstance();
    }

    public function getChildrenNodes($parent_nodes = null) {
        $query = $this->createModel()->newQuery();

        if (!is_null($parent_nodes)) {

            $parent_nodes = is_array($parent_nodes) || $parent_nodes instanceof Collection ? $parent_nodes : [$parent_nodes];

            foreach ($parent_nodes as $node) {
                $query = $query->orWhere(function ($q) use ($node) {
                    $q->whereBetween('lft', [$node->lft, $node->rgt]);
                });
            }

        }
        return $query->orderBy('lft', 'asc')->get();
    }
}