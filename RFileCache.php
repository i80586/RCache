<?php

namespace RLibrary;

/**
 * RFileCache class file
 * Simple class for file caching
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 6 November 2013
 */
class RFileCache extends \RLibrary\RCache
{
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
    private $expire = null;

    /**
     * Temporary cache identifier
     * @property string 
     */
    private $currentIdentifier;

    /**
     * Temporary cache duration 
     * @property integer 
     */
    private $currentDuration;

    /**
     * Class constructor
     * @param string $cacheFolder
     */
    public function __construct($cacheDir, $expire = null)
    {
        $this->setCacheDir($cacheDir);
        $this->expire = $expire;
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
        $this->cacheDir = ($dir[strlen($dir) - 1] != DIRECTORY_SEPARATOR) ? ($dir . DIRECTORY_SEPARATOR) : $dir;
        if (! is_dir($this->cacheDir)) {
            throw new \Exception('Cache dir is not exists', self::ERR_DIR_NOT_EXISTS);
        }
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

        $cacheDuration = (null !== $this->expire) ? $this->expire : $duration;

        if (! is_int($cacheDuration)) {
            throw new \Exception('Cache duration must be integer', self::ERR_WRANG_DURATION);
        }

        if (0 === $cacheDuration) {
            $cacheDuration = self::UNLIMITED_DURATION;
        }

        $cacheHash = $this->generateCacheHash($identifier);
        $this->writeData($this->cacheDir . $cacheHash, $data, $cacheDuration);
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

        $cacheHash = $this->generateCacheHash($identifier, $this->expire);
        $cacheData = $this->readData($this->cacheDir . $cacheHash);

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

		return $this->removeData($this->cacheDir . $this->generateCacheHash($identifier));
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

        $this->currentIdentifier = $this->generateCacheHash($identifier);
        $this->currentDuration = $duration;

        if (! is_int($this->currentDuration)) {
            throw new \Exception('Cache duration must be integer', self::ERR_WRANG_DURATION);
        }
				
		return $this->beginProcess($this->cacheDir . $this->currentIdentifier);
    }

    /**
     * End reading from buffer and save to cache
     */
    public function end()
    {
		$this->endProcess($this->cacheDir . $this->currentIdentifier, $this->currentDuration);
    }

}
