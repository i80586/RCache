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
	const ERR_EMPTY_DURATION=2;
	const ERR_EMPTY_IDENTIFIER=3;
	
	/**
	 * Cache folder
	 * @property string 
	 */
	private $cacheDir;

	/**
	 * Default expire time for all cache files
	 * As seconds
	 * @property integer 
	 */
	private $expire=null;

	/**
	 * Current cache identifier
	 * @property string 
	 */
	private $cacheIdentifier;

	/**
	 * Class constructor
	 * @param string $cacheFolder
	 */
	public function __construct($cacheDir, $expire=null)
	{
		$this->setCacheDir($cacheDir);
		$this->expire=$expire;
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
	 * Generates cache hash
	 * Format: identifier-expire
	 * @param string $identifier
	 * @param integer $expire
	 * @return string
	 */
	private function generateCacheHash($identifier, $expire)
	{
		return md5($identifier) . '-' . base64_encode($expire);
	}
	
	/**
	 * Load data from cache
	 * @param string $identifier
	 * @param mixed $data
	 * @param integer $duration
	 * @throws \Exception
	 */
	public function load($identifier, $data, $duration=null)
	{	
		if(null === $identifier)
			throw new \Exception('Cache identifier is not set', self::ERR_EMPTY_IDENTIFIER);
			
		$cacheDuration=null === $duration ? $this->expire : $duration;
		if(empty($cacheDuration))
			throw new \Exception('Cache duration is not set', self::ERR_EMPTY_DURATION);
		
		$cacheHash=$this->generateCacheHash($identifier, $cacheDuration);
				
		// ....
		// ...
	}
	
}