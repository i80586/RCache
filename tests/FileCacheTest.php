<?php

use RCache\Cache;
use RCache\FileCache;

/**
 * FileCacheTest tests class
 * Test cache for file
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 11 August, 2016
 */
class FileCacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RCache\Cache 
     */
    private $cache;
    
    /**
     * Init test
     */
    public function setUp()
    {
        # create temporary directory for cache
        if ( ! is_dir($directory = __DIR__ . '/cache')) {
            mkdir($directory);
        }
        
        $this->cache = new Cache(new FileCache($directory));
    }
    
    /**
     * Test set and get value
     */
    public function testManually()
    {
        $this->cache->set('test-identifier', 'test-value', 120);
        
        # assert existing cache and equal value
        $this->assertEquals('test-value', $this->cache->get('test-identifier'));
    }
    
    /**
     * Test fragment cache
     */
    public function testFragment()
    {
        # write content to cache
        if ($this->cache->start('test-fragment', 120)) {
            
            echo 'test-fragment-content';
            
            $this->cache->end(); }
        
        # test fragment cache
        if ($this->cache->start('test-fragment', 120)) {
            
            $this->assertTrue(false);
            
            $this->cache->end(); }
    }
    
    /**
     * Test cache expire / duration
     */
    public function testCacheExpire()
    {
        $this->cache->set('test-expire', 'test-value', 2);
        
        # assert existing cache
        $this->assertTrue($this->cache->has('test-expire'));
        
        sleep(3);
        
        # assert for expire cache
        $this->assertFalse($this->cache->has('test-expire'));
    }
    
    /**
     * Test deleting cache
     */
    public function testDelete()
    {
        foreach (['test-identifier', 'test-fragment'] as $identifier) {
            $this->cache->drop($identifier);
            $this->assertFalse($this->cache->has($identifier));
        }        
    }
    
}
