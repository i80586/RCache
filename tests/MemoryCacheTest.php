<?php

/**
 * Description of MemoryCacheTest
 *
 * @author Rasim
 */
class MemoryCacheTest extends PHPUnit_Framework_TestCase
{
    
    private $cacheHandler;
    
    public function setUp()
    {
        $this->cacheHandler = new RCache\Cache(new \RCache\MemCache());
    }
    
    public function testSet()
    {
        $this->cacheHandler->set('test-identifier', 'test-value', 120);
        
        $this->assertEquals('test-value', $this->cacheHandler->get('test-identifier'));
    }
    
    public function testDelete()
    {
        $this->cacheHandler->drop('test-value');
        
        $this->assertFalse($this->cacheHandler->has('test-value'));
    }
    
}
