<?php

namespace RCache;

/**
 * Cache handler
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 17 December, 2013
 */
class Cache
{

    /**
     * @var CacheInterface
     */
    protected $cacheHandler = null;

    /**
     * @param CacheInterface $handler
     * @param array $args
     */
    public function __construct(CacheInterface $handler)
    {
        $this->cacheHandler = $handler;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->cacheHandler, $name)) {
            return call_user_func_array([$this->cacheHandler, $name], $arguments);
        }
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
