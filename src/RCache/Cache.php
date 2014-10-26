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
		$this->_cacheHandler = $cacheHandler;
	}
	
	/**
	 * Get cache by identifier
	 * 
	 * @param string $identifier
	 * @return mixed
	 */
	public function get($identifier)
	{
		return $this->_cacheHandler->get($identifier);
	}
	
	/**
	 * Save data
	 * 
	 * @param string $identifier
	 * @param mixed $data
	 * @param integer $duration
	 */
	public function set($identifier, $data, $duration)
	{
		$this->_cacheHandler->set($identifier, $data, $duration);
	}
	
	/**
	 * Remove data from cache by identifier
	 * 
	 * @param string $identifier
	 * @return boolean
	 */
	public function drop($identifier)
	{
		return $this->_cacheHandler->drop($identifier);
	}
	
	/**
	 * Start reading from buffer
	 * 
	 * @param string $identifier
	 * @param integer $duration
	 * @return boolean
	 */
	public function start($identifier, $duration = 0)
	{
		return $this->_cacheHandler->start($identifier, $duration);
	}
	
	/**
	 * End reading from buffer and save to cache
	 */
	public function end()
	{
		$this->_cacheHandler->end();
	}
	
}