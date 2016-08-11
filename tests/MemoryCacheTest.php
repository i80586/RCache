<?php

use RCache\Cache;
use RCache\MemCache;

/**
 * MemoryCacheTest tests class
 * Test cache for memory
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 10 August, 2016
 */
class MemoryCacheTest extends PHPUnit_Framework_TestCase
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
        $this->cache = new Cache(new MemCache());
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
