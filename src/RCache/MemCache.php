<?php

namespace RCache;

use \Exception as Exception;
use \Memcache as PeclMemcache;

/**
 * MemCache class
 * Class for caching in memory
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date October 26, 2014
 */
class MemCache extends AbstractCache
{
    /**
     * Memecache handler
     * 
     * @property PeclMemcache 
     */
    protected $memcacheHandler = null;

    /**
     * Class constructor
     * 
     * @param string $hostname
     * @param string $port
     * @throws Exception
     */
    public function __construct($hostname = '127.0.0.1', $port = '11211')
    {
        $this->memcacheHandler = new PeclMemcache();
        if ( ! $this->memcacheHandler->connect($hostname, $port)) {
            throw new Exception("Could not connect to server. Connection information: {$hostname}:{$port}");
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
        return $this->memcacheHandler->get($identifier);
    }

    /**
     * Save data in memcache
     * 
     * @param string $identifier
     * @param mixed $data
     * @param boolean|integer $duration
     * @throws Exception
     */
    public function set($identifier, $data, $duration = 0)
    {
        $compress = (is_bool($data) || is_int($data) || is_float($data)) ? false : MEMCACHE_COMPRESSED;

        if (!$this->memcacheHandler->set($identifier, $data, $compress, $duration)) {
            throw new Exception('Failed to save data at the server');
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
        return $this->memcacheHandler->delete($identifier);
    }

    /**
     * Check if cache exists in memcache
     * 
     * @param string $identifier
     * @return boolean
     */
    public function has($identifier)
    {
        return false !== $this->get($identifier);
    }

    /**
     * Get content from cache
     * 
     * @param string $identifier
     * @param integer $duration
     * @return mixed
     */
    public function beginProcess($identifier, $duration = 0)
    {
        $this->currentIdentifier = $identifier;
        $this->currentDuration = $duration;

        if (false === ($cacheData = $this->get($this->currentIdentifier))) {
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
        $this->set($this->currentIdentifier, $data, $this->currentDuration);
    }

}
