<?php
/**
 * Created by PhpStorm.
 * User: nguyentantam
 * Date: 10/27/15
 * Time: 2:47 PM
 */

namespace Modules\Core\Repositories\Eloquent;


use Illuminate\Support\Collection;
use Modules\Core\Exceptions\NotFoundRevisionExceptions;
use Modules\Core\Repositories\BaseRevisionRepository;

class EloquentBaseRevisionRepository extends EloquentBaseRepository implements BaseRevisionRepository
{

	public function revert($revision_id,$attribute_revert= array())
	{
		$revision  =$this->getModel()->findOrFail($revision_id);
		$real_model = $revision->entity()->first();
		foreach($revision->toArray() as $key=>$value){
			if( in_array($key,$this->getModel()->getFillable()) && ($attribute_revert == '*' || in_array($key,$attribute_revert))){
				$real_model->$key = $value;
			}
		}
		$real_model->save();
		return $real_model;
	}

	public function compare($entity_id, $revision_first_id, $revision_second_id = null)
	{
		$result = new Collection();

		$array_find_revision = [$revision_first_id];

		if($revision_second_id){
			array_push($array_find_revision,$revision_second_id);
		}

		$revisions = $this->getModel()->whereIn('id',$array_find_revision)->get();

		$revision_first = $revisions->find($revision_first_id);

		$revision_second = $revisions->find($revision_second_id);

		if(is_null($revision_first)){
			throw new NotFoundRevisionExceptions();
		}

		$current = $revision_first->entity()->first();

		$result = $result->put('compare_key',$revision_first->getFillable());

		$result = $result->put('current',$current)->put('revision_first',$revision_first)->put('revision_second',$revision_second);

		if(is_null($revision_second)){
			$compare_version = $current;
		}else{
			$compare_version = $revision_second;
		}

		$result = $result->put('compare_version',$compare_version);
		return $result;

	}
}