<?php

/**
 * Class RFileCache
 * Simple class for file caching
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 6 November 2013
 */
class RFileCache
{
   
    /**
     * Cache folder
     * @property string 
     */
    private $cacheFolder;
    
    /**
     * Class constructor
     * @param string $cacheFolder
     */
    public function __construct($cacheFolder)
    {
	$this->cacheFolder=$cacheFolder;
    }
}
