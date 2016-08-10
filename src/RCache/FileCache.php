<?php

namespace RCache;

/**
 * FileCache class file
 * Class for caching in files
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 6 November 2013
 */
class FileCache extends ICache
{
    
    /**
     * Default cache duration (one year)
     */
    const UNLIMITED_DURATION = 31104000;

    /**
     * Cache folder
     * @property string
     */
    protected $_cacheDir;

    /**
     * Class constructor
     * 
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        if ($cacheDir[mb_strlen($cacheDir, 'utf-8') - 1] != DIRECTORY_SEPARATOR) {
            $cacheDir = $cacheDir . DIRECTORY_SEPARATOR;
        }
        $this->_cacheDir = $cacheDir;
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
     * Read data from file
     * 
     * @param string $filename
     * @return mixed
     */
    protected function readData($filename)
    {
        if (is_file($filename)) {
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
        file_put_contents($filename, (time() + $duration) . serialize($data), LOCK_EX);
    }

    /**
     * Remove cache
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
     * @throws \Exception
     */
    public function set($identifier, $data, $duration = 0)
    {
        if ( ! $duration) {
            $duration = self::UNLIMITED_DURATION;
        }

        $this->writeData($this->_cacheDir . $this->getCacheHash($identifier), $data, $duration);
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
        return $this->readData($this->_cacheDir . $this->getCacheHash($identifier));
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
        $this->_currentDuration = $duration ? $duration : self::UNLIMITED_DURATION;

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
