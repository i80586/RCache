<?php

namespace RCache;

/**
 * ICache class file
 * Abstraction of cache classes
 * 
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 17 November 2013
 */
abstract class ICache
{

    /**
     * Default cache duration (one year)
     */
    const UNLIMITED_DURATION = 31104000;

    /**
     * Temporary cache identifier
     * 
     * @property string 
     */
    protected $_currentIdentifier;

    /**
     * Temporary cache duration 
     * 
     * @property integer 
     */
    protected $_currentDuration;

    /**
     * Save data in cache
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
    
}
