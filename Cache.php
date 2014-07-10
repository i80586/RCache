<?php

namespace RCache;

/**
 * Cache handler
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 17 December 2013
 */
class Cache
{
	/**
	 * Cache handler
	 * 
	 * @var ICache
	 */
	private $_cacheHandler = null;
	
	/**
	 * Class construction
	 * 
	 * @param ICache $cacheHandler
	 */
	public function __construct(ICache $cacheHandler)
	{
		$this->setHandler($cacheHandler);
	}
	
	/**
	 * Get current cache handler
	 * 
	 * @return ICache
	 */
	public function getHandler()
	{
		return $this->_cacheHandler;
	}
	
	/**
	 * Set cache handler
	 * 
	 * @param ICache $cacheHandler
	 */
	public function setHandler(ICache $cacheHandler)
	{
		$this->_cacheHandler = $cacheHandler;
	}
	
}