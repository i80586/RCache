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
     * Error constants
     */
    const ERR_DIR_NOT_EXISTS = 1;
    
    /**
     * Cache folder
     * @property string 
     */
    private $cacheDir;
    
    /**
     * Class constructor
     * @param string $cacheFolder
     * @throws Exception
     */
    public function __construct($cacheDir)
    {
	$this->setCacheDir($cacheDir);
	$this->removeExpiredFile();
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
     */
    public function setCacheDir($dir)
    {
	$this->cacheDir=($dir[strlen($dir) - 1] != DIRECTORY_SEPARATOR) ? ($dir . DIRECTORY_SEPARATOR) : $dir;
	if(!is_dir($this->cacheDir))
	    throw new Exception('Cache dir is not exists', self::ERR_DIR_NOT_EXISTS);
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
	    $fileLastModified=filemtime($this->cacheDir . $file);
	    
	    if($fileLastModified > $currDate)
		unlink($this->cacheDir . $file);
	}
    }
}
