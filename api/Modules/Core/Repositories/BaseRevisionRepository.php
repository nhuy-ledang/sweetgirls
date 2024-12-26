<?php
/**
 * Created by PhpStorm.
 * User: nguyentantam
 * Date: 10/27/15
 * Time: 2:46 PM
 */

namespace Modules\Core\Repositories;


interface BaseRevisionRepository extends BaseRepository
{
	public function revert($revision_id);
	public function compare($entity_id,$revision_first,$revision_second=null);
}