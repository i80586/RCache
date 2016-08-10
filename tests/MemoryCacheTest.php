<?php

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
    private $cacheHandler;
    
    /**
     * Init test
     */
    public function setUp()
    {
        $this->cacheHandler = new RCache\Cache(new \RCache\MemCache());
    }
    
    /**
     * Test set and get value
     */
    public function testSet()
    {
        $this->cacheHandler->set('test-identifier', 'test-value', 120);
        
        $this->assertEquals('test-value', $this->cacheHandler->get('test-identifier'));
    }
    
    /**
     * Test deleting value
     */
    public function testDelete()
    {
        $this->cacheHandler->drop('test-value');
        
        $this->assertFalse($this->cacheHandler->has('test-value'));
    }
    
}
