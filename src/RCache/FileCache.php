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
     * Default cache duration (one year)
     */
    const UNLIMITED_DURATION = 31104000;

    /**
     * Error constants
     */
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
        $this->_cacheDir = ($cacheDir[strlen($cacheDir) - 1] != DIRECTORY_SEPARATOR) ? ($cacheDir . DIRECTORY_SEPARATOR) : $cacheDir;
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
        if (is_file($filename)) {
            unlink($filename);
        }

        return true;
    }

    /**
     * Save data in cache
     * 
     * @param string $identifier
     * @param mixed $data
     * @param integer $duration
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
        return $this->readData($this->_cacheDir . $this->getCacheHash($identifier));
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
     * 
     * @param string $identifier
     * @return string
     */
    protected function getCacheHash($identifier)
    {
        return sha1($identifier);
    }

}
