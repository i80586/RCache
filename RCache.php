<?php

namespace RLibrary;

/**
 * RCache class file
 * Abstraction for cache realize
 * 
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 17 November 2013
 */
abstract class RCache
{

	/**
	 * Error constants
	 */
	const ERR_DIR_NOT_EXISTS = 0x1;
	const ERR_WRANG_DURATION = 0x2;
	const ERR_DELETE_FILE = 0x3;

	/**
	 * Default cache duration (one year)
	 */
	const UNLIMITED_DURATION = 31104000;

	/**
	 * Save data in cache
	 * @param string $identifier
	 * @param mixed $data
	 * @param integer $duration
	 */
	abstract public function set($identifier, $data, $duration = 0);

	/**
	 * Get cache
	 * @param string $identifier
	 */
	abstract public function get($identifier);

	/**
	 * Delete cache
	 * @param string $identifier
	 */
	abstract public function drop($identifier);

	/**
	 * Generates cache hash
	 * @param string $identifier
	 * @return string
	 */
	protected function generateCacheHash($identifier)
	{
		return sha1($identifier);
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

}
