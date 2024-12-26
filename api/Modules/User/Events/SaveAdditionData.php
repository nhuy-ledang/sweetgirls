<?php namespace Modules\User\Events;

use App\Events\Event;

class SaveAdditionData extends Event{

	public $user;

	public function __construct($user){
		$this->user = $user;
	}
}