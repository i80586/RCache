<?php

namespace RLibrary;

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
     * Error constants
     */
    const ERR_DIR_NOT_EXISTS=1;
    
    /**
     * Default expire time (one hour)
     */
    const DEFAULT_EXPIRE_TIME=3600;
    
    /**
     * Cache folder
     * @property string 
     */
    private $cacheDir;
    
    /**
     * Class constructor
     * @param string $cacheFolder
     */
    public function __construct($cacheDir)
    {
	$this->setCacheDir($cacheDir);
	$this->removeExpiredFiles();
    }
    
    /**
     * Returns cache dir
     * @return string
     */
    public function getCacheDir()
    {
	return $this->cacheDir;
    }
    
    /**
     * Sets cache dir
     * @param string $dir
     * @throws Exception
     */
    public function setCacheDir($dir)
    {
	$this->cacheDir=($dir[strlen($dir) - 1] != DIRECTORY_SEPARATOR) ? ($dir . DIRECTORY_SEPARATOR) : $dir;
	if(!is_dir($this->cacheDir))
	    throw new \Exception('Cache dir is not exists', self::ERR_DIR_NOT_EXISTS);
    }
        
    /**
     * Remove expired cache files
     */
    private function removeExpiredFiles()
    {
	$currDate=time();
	
	$dirHandler=opendir($this->cacheDir);
	while(($file=readdir($dirHandler)) !== false)
	{
	    if(is_file($this->cacheDir . $file))
	    {
		$fileLastModified=filemtime($this->cacheDir . $file);

		if(($currDate - $fileLastModified) >= self::DEFAULT_EXPIRE_TIME)
		    unlink($this->cacheDir . $file);
	    }
	}
    }
}
