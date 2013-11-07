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
	 * Catch undefined property and
	 * set current cache identifier
	 * @param string $name
	 */
	public function __get($name)
	{
		$this->cacheIdentifier=$name;
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
	
}