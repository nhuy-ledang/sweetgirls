<?php namespace Modules\Core\Common;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait EloquentHelperTrait{
    protected $fields_not_shown = ['id', 'created_at', 'updated_at', 'user_id'];

	public function fields(){
    	$ret = [];
    	$class_name = class_basename($this);
    	$config = implode('.', ['relations', $class_name]);
    	#i: Relation method resolver
        if (\Config::has($config)) {
            $relations = \Config::get($config);
        	
        	foreach ($relations as $rel) {
                if($rel instanceof Model || $rel instanceof Builder || $rel instanceof \Closure){
            		$property_ext = $rel($this)->first();
                    if(!empty($property_ext)){
                		foreach ($property_ext['original'] as $k => $v) {
                            if(!in_array($k, $this->fields_not_shown)){
                                $ret[$k] = $v;
                            }
                		}
                    }
                }
        	}
        }
    	
    	return (object) $ret;
    }

    public function field($ext){
        $ret = [];
        $class_name = class_basename($this);
        $config = implode('.', ['relations', $class_name, $ext]);
        #i: Relation method resolver
        if (\Config::has($config)) {
            $function = \Config::get($config);
            if($function instanceof Model || $function instanceof Builder || $function instanceof \Closure){
                $property_ext = $function($this)->first();
                if(!empty($property_ext)){
                    foreach ($property_ext['original'] as $k => $v) {
                        if(!in_array($k, $this->fields_not_shown)){
                            $ret[$k] = $v;
                        }
                    }
                }
            }
        }
        
        return (object) $ret;
    }
}