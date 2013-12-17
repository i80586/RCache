<?php

namespace RLibrary;

/**
 * Cache handler
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 17 December 2013
 */
class RCaching
{
	/**
	 * Error constants
	 */
	const ERR_EMPTY_CACHE_HANDLER = 0x4;
	
	/**
	 * Currently cache handler
	 * @var cache handler
	 */
	private $_cacheHandler = null;
	
	/**
	 * Class construction
	 * @param object $cacheHandler
	 */
	public function __construct($cacheHandler)
	{
		$this->setHandler($cacheHandler);
	}
	
	/**
	 * Get current cache handler
	 * @return object
	 */
	public function getHandler()
	{
		return $this->_cacheHandler;
	}
	
	/*
	 * Check for valid handler
	 */
	private function checkInstance($instance)
	{
		return ($instance instanceof RFileCache);
	}
	
	/**
	 * Set cache handler
	 * @param type $cacheHandler
	 * @throws \Exception
	 */
	public function setHandler($cacheHandler)
	{
		if (! $this->checkInstance($cacheHandler)) {
			throw new \Exception('Cache handler is not set', self::ERR_EMPTY_CACHE_HANDLER);
		}
		
		$this->_cacheHandler = $cacheHandler;
	}
	
}