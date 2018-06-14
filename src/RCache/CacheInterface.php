<?php

namespace RCache;

/**
 * CacheInterface interface 
 * Template of cache realizations
 * 
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 14 June, 2018
 */
interface CacheInterface
{
    /**
     * @param string $identifier
     * @param mixed $data
     * @param integer $duration
     */
    public function set(string $identifier, $data, int $duration = 0);

    /**
     * @param string $identifier
     */
    public function get(string $identifier);

    /**
     * @param string $identifier
     */
    public function drop(string $identifier): bool;

    /**
     * @param string $identifier
     */
    public function has(string $identifier): bool;

}
