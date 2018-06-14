<?php

namespace RCache;

/**
 * FileCache class file
 * Class for caching in files
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 14 June, 2018
 */
class FileCache implements CacheInterface
{

    /**
     * Default cache duration (one year)
     */
    const UNLIMITED_DURATION = 31104000;

    /**
     * Cache folder
     * @property string
     */
    protected $cacheDirectory;

    /**
     * @param string|null $cacheDirectory
     */
    public function __construct($cacheDirectory = null)
    {
        # set cache directory
        if (!empty($cacheDirectory)) {
            $this->setCacheDirectory($cacheDirectory);
        }
    }

    /**
     * Set cache directory
     * 
     * @param string $cacheDirectory
     */
    public function setCacheDirectory(string $cacheDirectory)
    {
        if ($cacheDirectory[mb_strlen($cacheDirectory, 'utf-8') - 1] != DIRECTORY_SEPARATOR) {
            $cacheDirectory = $cacheDirectory . DIRECTORY_SEPARATOR;
        }
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * Returns cache directory
     * 
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->cacheDirectory;
    }

    /**
     * Read data from file
     * 
     * @param string $filename
     * @return mixed
     */
    protected function readData(string $filename)
    {        
        if (is_file($filename) && is_readable($filename)) {
            $fileContent = file_get_contents($filename);
            $expireTime = substr($fileContent, 0, 11);

            if ($expireTime > time()) {
                return unserialize(substr($fileContent, 10));
            } else {
                unlink($filename);

                return false;
            }
        }
        return false;
    }

    /**
     * Write data to file
     * 
     * @param string $filename
     * @param mixed $data
     * @param integer $duration
     */
    protected function writeData(string $filename, $data, int $duration)
    {
        if (!is_writable($directory = pathinfo($filename, PATHINFO_DIRNAME))) {
            throw new \InvalidArgumentException('Directory "' . $directory . '" does not exist or is not writeable.');
        }
        file_put_contents($filename, (time() + $duration) . serialize($data), LOCK_EX);
    }

    /**
     * Remove cache
     * 
     * @param string $filename
     * @return bool
     */
    protected function removeData(string $filename): bool
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
     * @param int $duration
     */
    public function set(string $identifier, $data, int $duration = 0)
    {
        if (!$duration) {
            $duration = self::UNLIMITED_DURATION;
        }

        $this->writeData($this->getCacheDir() . $this->getCacheHash($identifier), $data, $duration);
    }

    /**
     * Get cached data
     * Returns mixed data if exists and FALSE if does not exist
     *
     * @param string $identifier
     * @return mixed
     */
    public function get(string $identifier)
    {
        return $this->readData($this->getCacheDir() . $this->getCacheHash($identifier));
    }

    /**
     * Remove cache by identifier
     * Returns true if file deleted and false if file does not exist
     *
     * @param string $identifier
     * @return bool
     */
    public function drop(string $identifier): bool
    {
        return $this->removeData($this->getCacheDir() . $this->getCacheHash($identifier));
    }

    /**
     * Check is cache exists
     * 
     * @param string $identifier
     * @return bool
     */
    public function has(string $identifier): bool
    {
        return (false !== $this->get($identifier));
    }

    /**
     * Generates hash by cache identifier
     *
     * @param string $identifier
     * @return string
     */
    protected function getCacheHash(string $identifier): string
    {
        return sha1($identifier);
    }

}
