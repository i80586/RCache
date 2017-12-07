<?php

namespace RCache;

/**
 * MemCache class file
 * Class for caching in memory
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 26 October, 2014
 */
class MemCache extends ICache
{

    /**
     * Memecache\Memcached handler
     * 
     * @var \Memcache 
     */
    protected $cache = null;
    /**
     * @var string
     */
    public $hostname;
    /**
     * @var integer
     */
    public $post;
    /**
     * @var boolean
     */
    public $useMemcached = false;

    /**
     * Class constructor
     * 
     * @param boolean $useMemcached
     * @param string $hostname
     * @param string $port
     * @throws \Exception
     */
    public function __construct($useMemcached = false, $hostname = '127.0.0.1', $port = '11211')
    {
        $this->useMemcached = $useMemcached;
        $this->hostname = $hostname;
        $this->port = $port;

        $this->connect();
    }

    public function connect()
    {
        $this->cache = ($this->useMemcached) ? new \Memcached() : new \Memcache();
        if (!$this->cache->addserver($this->hostname, $this->port)) {
            throw new \Exception("Could not connect to server. Connection information: {$this->hostname}:{$this->port}");
        }

        $this->setCompressOptions();
    }

    public function setCompressOptions()
    {
        if ($this->useMemcached) {
            $this->cache->setOption(\Memcached::OPT_COMPRESSION, true);
        } else {
            $this->cache->setCompressThreshold(20000, 1);
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
        return $this->cache->get($identifier);
    }

    /**
     * Save data in memcache
     * 
     * @param string $identifier
     * @param mixed $data
     * @param boolean|integer $duration
     * @throws \Exception
     */
    public function set($identifier, $data, $duration = 0)
    {
        $savedSuccessfully = $this->useMemcached 
                                ? $this->cache->set($identifier, $data, $duration) 
                                : $this->cache->set($identifier, $data, 0, $duration);
        if (!$savedSuccessfully) {
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
        return $this->cache->delete($identifier);
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
     * @param string $duration
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
