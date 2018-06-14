<?php

namespace RCache;

use \Memcache as SysMemCache;
use \Memcached as SysMemCached;

/**
 * MemCache class file
 * Class for caching in memory
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 14 June, 2018
 */
class MemCache implements CacheInterface
{

    const TYPE_MEMCACHE = 1;
    const TYPE_MEMCACHED = 2;

    /**
     * Memecache\Memcached handler
     * 
     * @var \Memcache | \Memcached
     */
    protected $cache = null;
    /**
     * @var string
     */
    public $hostname;
    /**
     * @var int
     */
    public $post;
    /**
     * @var int
     */
    public $type;

    /**
     * Class constructor
     * 
     * @param string $hostname
     * @param string $port
     * @throws \Exception
     */
    public function __construct(int $type = self::TYPE_MEMCACHE, string $hostname = '127.0.0.1', int $port = 11211)
    {
        $this->type = $type;
        $this->hostname = $hostname;
        $this->port = $port;
    }

    public function connect()
    {
        $this->cache = $this->type == self::TYPE_MEMCACHE 
                        ? new SysMemCache 
                        : new SysMemCached();
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
