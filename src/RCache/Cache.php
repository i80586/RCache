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
    protected $_cacheHandler = null;

    /**
     * Available cache types
     * 
     * @var array 
     */
    static protected $_availableTypes = [
        'file' => 'FileCache',
        'memory' => 'MemCache'
    ];

    /**
     * Class construction
     * 
     * @param ICache|string $cacheType
     * @param array $args
     */
    public function __construct($cacheType, array $args = [])
    {
        $this->_cacheHandler = ($cacheType instanceof ICache) ? $cacheType : self::getInstance($cacheType, $args);
    }

    /**
     * Get cache class by type
     * 
     * @param string $type
     * @param array $args
     * @return ICache
     * @throws \Exception
     */
    static protected function getInstance($type, array $args = [])
    {
        if ( ! array_key_exists($type, self::$_availableTypes)) {
            throw new \Exception('Cache type not found');
        }

        $className = __NAMESPACE__ . '\\' . self::$_availableTypes[$type];
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
     * @param boolean|integer $duration
     * @return boolean
     */
    public function start($identifier, $duration = false)
    {
        if (false === ($cacheData = $this->_cacheHandler->beginProcess($identifier, $duration))) {
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
        $this->_cacheHandler->endProcess(ob_get_contents());

        ob_flush();
        ob_end_clean();
    }

}
