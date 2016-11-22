<?php

namespace RCache;

/**
 * AbstractCache class file
 * Abstrac cache class
 * 
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date November 17, 2013
 */
abstract class AbstractCache
{
    /**
     * Temporary cache identifier
     * (uses in fragment cache)
     * 
     * @property string 
     */
    protected $currentIdentifier;

    /**
     * Temporary cache duration 
     * (uses in fragment cache)
     * 
     * @property integer 
     */
    protected $currentDuration;

    /**
     * Set cache
     * 
     * @param string $identifier
     * @param mixed $data
     * @param integer $duration
     */
    abstract public function set($identifier, $data, $duration = 0);

    /**
     * Get cache
     * 
     * @param string $identifier
     */
    abstract public function get($identifier);

    /**
     * Delete cache
     * 
     * @param string $identifier
     */
    abstract public function drop($identifier);

    /**
     * Check is cache exists
     * 
     * @param string $identifier
     */
    abstract public function has($identifier);

}
