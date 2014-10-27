<?php

namespace RCache;

/**
 * FileCache class file
 * Simple class for file caching
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 6 November 2013
 */
class FileCache extends ICache
{

	/**
	 * Error constants
	 */
	const ERR_DIR_NOT_EXISTS = 0x1;
	const ERR_WRANG_DURATION = 0x2;
	const ERR_DELETE_FILE = 0x3;

	/**
	 * Cache folder
	 * @property string
	 */
	protected $_cacheDir;

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
	 * Class constructor
	 * 
	 * @param string $cacheDir
	 */
	public function __construct($cacheDir)
	{
		$this->setCacheDir($cacheDir);
	}

	/**
	 * Returns cache dir
	 * 
	 * @return string
	 */
	public function getCacheDir()
	{
		return $this->_cacheDir;
	}

	/**
	 * Sets cache dir
	 * 
	 * @param string $dir
	 * @throws Exception
	 */
	public function setCacheDir($dir)
	{
		$this->_cacheDir = ($dir[strlen($dir) - 1] != DIRECTORY_SEPARATOR) ? ($dir . DIRECTORY_SEPARATOR) : $dir;
		if (!is_dir($this->_cacheDir)) {
			throw new \Exception('Cache dir is not exists', self::ERR_DIR_NOT_EXISTS);
		}
	}

	/**
	 * Read data from file
	 * 
	 * @param string $filename
	 * @return mixed
	 */
	protected function readData($filename)
	{
		$output = false;

		if (is_file($filename)) {
			$fileContent = file_get_contents($filename);
			$expireTime = substr($fileContent, 0, 11);

			if ($expireTime > time()) {
				$output = unserialize(substr($fileContent, 10));
			} else {
				unlink($filename);
			}
		}

		return $output;
	}

	/**
	 * Write data to file
	 * 
	 * @param string $filename
	 * @param string $data
	 * @param integer $duration
	 */
	protected function writeData($filename, $data, $duration)
	{
		file_put_contents($filename, (time() + $duration) . serialize($data), LOCK_EX);
	}

	/**
	 * Remove cache data
	 * 
	 * @param string $filename
	 * @return boolean
	 */
	protected function removeData($filename)
	{
		try {
			if (is_file($filename)) {
				unlink($filename);
			}
		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * Save data in cache
	 * 
	 * @param string $identifier
	 * @param mixed $data
	 * @param boolean|integer $duration
	 * @throws \Exception
	 */
	public function set($identifier, $data, $duration = 0)
	{
		if (!$duration) {
			$duration = self::UNLIMITED_DURATION;
		}

		$this->writeData($this->_cacheDir . $this->getCacheHash($identifier), $data, $duration);
	}

	/**
	 * Load data
	 * 
	 * @param string $identifier
	 * @return mixed
	 */
	public function get($identifier)
	{
		return $this->readData($this->_cacheDir . $this->getCacheHash($identifier, $this->_expire));
	}

	/**
	 * Remove cache by identifier
	 * Return true if file deleted and false if file not exists
	 * 
	 * @param string $identifier
	 * @return boolean
	 */
	public function drop($identifier)
	{
		return $this->removeData($this->_cacheDir . $this->getCacheHash($identifier));
	}

	/**
	 * Get content from cache
	 * 
	 * @param string $identifier
	 * @param boolean|integer $duration
	 * @return mixed
	 */
	public function beginProcess($identifier, $duration)
	{
		$this->_currentIdentifier = $this->getCacheHash($identifier);
		$this->_currentDuration = !$duration ? self::UNLIMITED_DURATION : $duration;
		
		if (false === ($cacheData = $this->readData($this->_cacheDir . $this->_currentIdentifier))) {
			return false;
		}
		
		return $cacheData;
	}

	/**
	 * Write catched data
	 * 
	 * @param string $data
	 */
	public function endProcess($data)
	{
		$this->writeData($this->_cacheDir . $this->_currentIdentifier, $data, $this->_currentDuration);
	}
	
	/**
     * Generates cache hash
	 * 
     * @param string $identifier
     * @return string
     */
    protected function getCacheHash($identifier)
    {
		return sha1($identifier);
    }

}
