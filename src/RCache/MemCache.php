<?php

namespace RCache;

/**
 * MemCache class file
 * Simple class for caching in memory cache
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 26 October 2014
 */
class MemCache extends ICache
{

    /**
     * Memecache handler
     * 
     * @var Memcache 
     */
    protected $_memcacheHandler = null;

    /**
     * Class constructor
     * 
     * @param string $hostname
     * @param string $port
     * @throws \Exception
     */
    public function __construct($hostname = 'localhost', $port = '11211')
    {
        $this->_memcacheHandler = new \Memcache();
        if (!$this->_memcacheHandler->connect($hostname, $port)) {
            throw new \Exception("Could not connect to server. Connection information: {$hostname}:{$port}");
        }
    }

    /**
     * Get cache by identifier
     * 
     * @param string $identifier
     * @return mixed
     */
    public function get($identifier)
    {
        return $this->_memcacheHandler->get($identifier);
    }

    /**
     * Save data in memcache
     * 
     * @param string $identifier
     * @param mixed $data
     * @param boolean|integer $duration
     * @throws \Exception
     */
    public function set($identifier, $data, $duration = false)
    {
        if (!$duration) {
            $duration = self::UNLIMITED_DURATION;
        }

        if (!$this->_memcacheHandler->set($identifier, $data, false, $duration)) {
            throw new \Exception('Failed to save data at the server');
        }
    }

    /**
     * Delete cache from memory by identifier
     * 
     * @param string $identifier
     * @return boolean
     */
    public function drop($identifier)
    {
        return !$this->_memcacheHandler->delete($identifier);
    }

    /**
     * Get content from cache
     * 
     * @param string $identifier
     * @param string $duration
     * @return mixed
     */
    public function beginProcess($identifier, $duration)
    {
        $this->_currentIdentifier = $identifier;
        $this->_currentDuration = !$duration ? self::UNLIMITED_DURATION : $duration;

        if (false === ($cacheData = $this->get($this->_currentIdentifier))) {
            return false;
        }

        return $cacheData;
    }

    /**
     * Write catched data
     * 
     * @param string $data
     */
    public function endProcess($data)
    {
        $this->set($this->_currentIdentifier, $data, $this->_currentDuration);
    }

}
