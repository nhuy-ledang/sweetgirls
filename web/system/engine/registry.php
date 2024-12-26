<?php
/**
 * @package		MotilaCore
 * @author		HuyD
 * @copyright	Copyright (c) 2018 - 2020
 * @link		https://motila.vn
 */

/**
* Registry class
*/
final class Registry {
	private $data = array();

	/**
     * 
     *
     * @param	string	$key
	 * 
	 * @return	mixed
     */
	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

    /**
     * 
     *
     * @param	string	$key
	 * @param	string	$value
     */	
	public function set($key, $value) {
		$this->data[$key] = $value;
	}
	
    /**
     * 
     *
     * @param	string	$key
	 *
	 * @return	bool
     */
	public function has($key) {
		return isset($this->data[$key]);
	}
}