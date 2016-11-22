<?php

namespace RCache;

use \Exception as Exception;

/**
 * FileCache class
 * Class for caching in files
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date November 6, 2013
 */
class FileCache extends AbstractCache
{
    /**
     * Default cache duration (one year)
     */
    const UNLIMITED_DURATION = 31104000;

    /**
     * Cache folder
     * 
     * @property string
     */
    protected $cacheDirectory;

    /**
     * Class constructor
     * 
     * @param string $cacheDirectory
     */
    public function __construct($cacheDirectory = null)
    {
        $this->setCacheDir($cacheDirectory);
    }

    /**
     * Set cache directory
     * 
     * @param string $cacheDirectory
     */
    public function setCacheDir($cacheDirectory)
    {
        if (!is_scalar($cacheDirectory)) {
            return;
        }
        
        if (substr($cacheDirectory, -1) != DIRECTORY_SEPARATOR) {
            $cacheDirectory .= DIRECTORY_SEPARATOR;
        }
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * Returns cache directory
     * 
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDirectory;
    }

    /**
     * Read data from file
     * 
     * @param string $filename
     * @return mixed
     */
    protected function readData($filename)
    {
        if (is_file($filename) && is_readable($filename)) {
            $fileContent = file_get_contents($filename);
            $expireTime = substr($fileContent, 0, 11);

            if ($expireTime > time()) {
                return unserialize(substr($fileContent, 10));
            } else {
                unlink($filename);
            }
        }
        return false;
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
        if (!is_writable($directory = pathinfo($filename, PATHINFO_DIRNAME))) {
            throw new Exception('Directory "' . $directory . '" is not exists or writeable.');
        }
        file_put_contents($filename, (time() + $duration) . serialize($data), LOCK_EX);
    }

    /**
     * Remove cache file
     * 
     * @param string $filename
     * @return boolean
     */
    protected function removeData($filename)
    {
        if (is_file($filename)) {
            return unlink($filename);
        }
        return true;
    }

    /**
     * Cache data
     * 
     * @param string $identifier
     * @param mixed $data
     * @param integer $duration
     * @throws Exception
     */
    public function set($identifier, $data, $duration = 0)
    {
        if (!$duration) {
            $duration = self::UNLIMITED_DURATION;
        }

        $this->writeData($this->getCacheDir() . $this->getCacheHash($identifier), $data, $duration);
    }

    /**
     * Get cached data
     * Returns mixed data if exist and FALSE if data are not exists
     *
     * @param string $identifier
     * @return mixed
     */
    public function get($identifier)
    {
        return $this->readData($this->getCacheDir() . $this->getCacheHash($identifier));
    }

    /**
     * Remove cache by identifier
     * Returns true if file deleted and false if file not exists
     *
     * @param string $identifier
     * @return boolean
     */
    public function drop($identifier)
    {
        return $this->removeData($this->getCacheDir() . $this->getCacheHash($identifier));
    }

    /**
     * Check if cache exists in filecache
     * 
     * @param string $identifier
     * @return boolean
     */
    public function has($identifier)
    {
        return (false !== $this->get($identifier));
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
        $this->currentIdentifier = $this->getCacheHash($identifier);
        $this->currentDuration = $duration ? $duration : self::UNLIMITED_DURATION;

        if (false === ($cacheData = $this->readData($this->getCacheDir() . $this->currentIdentifier))) {
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
        $this->writeData($this->getCacheDir() . $this->currentIdentifier, $data, $this->currentDuration);
    }

    /**
     * Generates cache hash
     * Generates hash by cache identifier
     *
     * @param string $identifier
     * @return string
     */
    protected function getCacheHash($identifier)
    {
        return sha1($identifier);
    }

}
