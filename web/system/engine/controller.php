<?php
/**
 * @package		MotilaCore
 * @author		HuyD
 * @copyright	Copyright (c) 2018 - 2020
 * @link		https://motila.vn
 */

/**
 * Controller class
 *
 * @property Document document
 * @property Loader load
 * @property Request request
 * @property Language language
 * @property Session session
 * @property Response response
 * @property Url url
 * @property Config config
 */
abstract class Controller {
	protected $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
}