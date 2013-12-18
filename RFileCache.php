<?php

namespace RLibrary;

/**
 * RFileCache class file
 * Simple class for file caching
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 6 November 2013
 */
class RFileCache extends RCache
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
	private $_cacheDir;

	/**
	 * Default expire time for all cache files
	 * As seconds
	 * @property integer 
	 */
	private $_expire = null;

	/**
	 * Temporary cache identifier
	 * @property string 
	 */
	private $_currentIdentifier;

	/**
	 * Temporary cache duration 
	 * @property integer 
	 */
	private $_currentDuration;

	/**
	 * Class constructor
	 * @param string $cacheFolder
	 */
	public function __construct($cacheDir, $expire = null)
	{
		$this->setCacheDir($cacheDir);
		$this->_expire = $expire;
	}

	/**
	 * Returns cache dir
	 * @return string
	 */
	public function getCacheDir()
	{
		return $this->_cacheDir;
	}

	/**
	 * Sets cache dir
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
	 * @param string $identifier
	 * @param mixed $data
	 * @param integer $duration
	 * @throws \Exception
	 */
	public function set($identifier, $data, $duration = 0)
	{
		if (empty($identifier)) {
			throw new \Exception('Cache identifier is not set', self::ERR_EMPTY_IDENTIFIER);
		}

		$cacheDuration = (null !== $this->_expire) ? $this->_expire : $duration;

		if (!is_int($cacheDuration)) {
			throw new \Exception('Cache duration must be integer', self::ERR_WRANG_DURATION);
		}

		if (0 === $cacheDuration) {
			$cacheDuration = self::UNLIMITED_DURATION;
		}

		$cacheHash = $this->generateCacheHash($identifier);
		$this->writeData($this->_cacheDir . $cacheHash, $data, $cacheDuration);
	}

	/**
	 * Load data
	 * @param string $identifier
	 * @return mixed
	 */
	public function get($identifier)
	{
		if (empty($identifier)) {
			throw new \Exception('Cache identifier is not set', self::ERR_EMPTY_IDENTIFIER);
		}

		$cacheHash = $this->generateCacheHash($identifier, $this->_expire);
		$cacheData = $this->readData($this->_cacheDir . $cacheHash);

		return $cacheData;
	}

	/**
	 * Remove cache by identifier
	 * Return true if file deleted and false if file not exists
	 * @param string $identifier
	 * @return boolean
	 */
	public function drop($identifier)
	{
		if (empty($identifier)) {
			throw new \Exception('Cache identifier is not set', self::ERR_EMPTY_IDENTIFIER);
		}

		return $this->removeData($this->_cacheDir . $this->generateCacheHash($identifier));
	}

	/**
	 * Begin reading data from buffer
	 * @param string $identifier
	 */
	protected function beginProcess($cacheFile)
	{
		$cacheData = $this->readData($cacheFile);

		if (false !== $cacheData) {
			echo $cacheData;
			return false;
		} else {
			return ob_start();
		}
	}

	/**
	 * End reading from buffer and write to file
	 * @param string $cacheFile
	 * @param integer $cacheDuration
	 */
	protected function endProcess($cacheFile, $cacheDuration)
	{
		$this->writeData($cacheFile, ob_get_contents(), $cacheDuration);
		ob_flush();
		ob_end_clean();
	}

	/**
	 * Start reading from buffer
	 * @param string $identifier
	 * @param integer $duration
	 * @return boolean
	 * @throws \Exception
	 */
	public function start($identifier, $duration = 0)
	{
		if (empty($identifier)) {
			throw new \Exception('Cache identifier is not set', self::ERR_EMPTY_IDENTIFIER);
		}

		$this->_currentIdentifier = $this->generateCacheHash($identifier);
		$this->_currentDuration = $duration;

		if (!is_int($this->_currentDuration)) {
			throw new \Exception('Cache duration must be integer', self::ERR_WRANG_DURATION);
		}

		return $this->beginProcess($this->_cacheDir . $this->_currentIdentifier);
	}

	/**
	 * End reading from buffer and save to cache
	 */
	public function end()
	{
		$this->endProcess($this->_cacheDir . $this->_currentIdentifier, $this->_currentDuration);
	}

}
