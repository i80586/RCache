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
	private $expire=0;

	/**
	 * Class constructor
	 * @param string $cacheFolder
	 */
	public function __construct($cacheDir, $expire=0)
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
	 * Save data in cache
	 * @param string $identifier
	 * @param mixed $data
	 * @param integer $duration
	 * @throws \Exception
	 */
	public function set($identifier, $data)
	{
		if(empty($identifier))
			throw new \Exception('Cache identifier is not set', self::ERR_EMPTY_IDENTIFIER);

		$cacheHash=$this->generateCacheHash($identifier, $this->expire);
		$this->writeData($this->cacheDir . $cacheHash, serialize($data));
	}

	/**
	 * Load data
	 * @param string $identifier
	 * @return mixed
	 */
	public function get($identifier)
	{
		if(empty($identifier))
			throw new \Exception('Cache identifier is not set', self::ERR_EMPTY_IDENTIFIER);

		$cacheHash=$this->generateCacheHash($identifier, $this->expire);
		$cacheData=unserialize($this->readData($this->cacheDir . $cacheHash));

		return $cacheData;
	}

	/**
	 * Write data to file
	 * @param string $data
	 */
	private function writeData($filename, $data)
	{
		$fileHandler=fopen($filename, 'w');
		fwrite($fileHandler, $data);
		fclose($fileHandler);
	}

	/**
	 * Read data from file
	 * @param string $filename
	 * @return mixed
	 */
	private function readData($filename)
	{
		$output=false;

		if(is_file($filename))
		{
			$filelastModified=filemtime($filename);
			$explodedInfo=explode('-', basename($filename));
			$cacheDuration=base64_decode($explodedInfo[1]);

			if($cacheDuration > 0 && (time() - $filelastModified) >= $cacheDuration)
				unlink($filename);
			else
			{
				$fileHandler=fopen($filename, 'r');
				$output=fgets($fileHandler);
				fclose($fileHandler);
			}
		}

		return $output;
	}
	
}