<?php

namespace RCache;

use \Exception as Exception;

/**
 * Cache handler
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date December 17, 2013
 */
class Cache
{
    /**
     * Cache handler
     * 
     * @property AbstractCache
     */
    protected $cacheHandler = null;

    /**
     * Available cache types
     * 
     * @property array 
     */
    static protected $availableTypes = [
            'file' => 'FileCache',
            'memory' => 'MemCache'
        ];

    /**
     * Class construction
     *
     * @param AbstractCache|string $cacheType
     * @param array $args
     */
    public function __construct($cacheType, array $args = [])
    {
        $this->cacheHandler = ($cacheType instanceof AbstractCache) 
                                ? $cacheType 
        						: self::getInstance($cacheType, $args);
    }

    /**
     * Get cache class by type
     * 
     * @param string $type
     * @param array $args
     * @return AbstractCache
     * @throws Exception
     */
    static protected function getInstance($type, array $args = [])
    {
        if (!array_key_exists($type, self::$availableTypes)) {
            throw new Exception('Cache type not found');
        }

        $className = __NAMESPACE__ . '\\' . self::$availableTypes[$type];
        return (new \ReflectionClass($className))->newInstanceArgs($args);
    }

    /**
     * Get cache by identifier
     * 
     * @param string $identifier
     * @return mixed
     */
    public function get($identifier)
    {
        return $this->cacheHandler->get($identifier);
    }

    /**
     * Save data in cache
     *
     * @param string $identifier
     * @param mixed $data
     * @param integer $duration
     */
    public function set($identifier, $data, $duration = 0)
    {
        $this->cacheHandler->set($identifier, $data, $duration);
    }

    /**
     * Remove data from cache by identifier
     * 
     * @param string $identifier
     * @return boolean
     */
    public function drop($identifier)
    {
        return $this->cacheHandler->drop($identifier);
    }

    /**
     * Check if cache exists
     *
     * @param string $identifier
     * @return boolean
     */
    public function has($identifier)
    {
        return $this->cacheHandler->has($identifier);
    }

    /**
     * Start reading from buffer
     * 
     * @param string $identifier
     * @param boolean|integer $duration
     * @return boolean
     */
    public function start($identifier, $duration = false)
    {
        if (false === ($cacheData = $this->cacheHandler->beginProcess($identifier, $duration))) {
            return ob_start();
        } else {
            echo $cacheData;
            return false;
        }
    }

    /**
     * Cache catched content
     */
    public function end()
    {
        $this->cacheHandler->endProcess(ob_get_contents());

        ob_flush();
        ob_end_clean();
    }

}
