<?php

define('CLASS_NAMESPACE', 'RCache\\');

spl_autoload_register(function ($className) {
	if (is_file($filePath = __DIR__ . str_replace(CLASS_NAMESPACE, '/', $className) . '.php')) {
		require $filePath;
	}
});

/**
 * Tests of RCache\FileCache class
 *
 * @author Rasim Ashurov <rasim.ashurov@gmail.com>
 * @date 11 July 2014
 */
class ClassTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * Class handler
	 * 
	 * @var ICache 
	 */
	private $_classHandler = null;

	/**
	 * Setup tests
	 */
	protected function setUp()
	{
		if (!is_dir($cacheDir = __DIR__ . '/../cache')) {
			mkdir($cacheDir);
		}
		$this->_classHandler = new RCache\FileCache($cacheDir);
	}
	
	/**
	 * Tests of methods
	 */
	public function testMethods()
	{
		/* @assert: check cache dir */
		$this->assertInternalType('string', $this->_classHandler->getCacheDir());
		
		$cacheKey = md5('test');
		$cacheData = 'Baku, Azerbaijan';
		
		$this->_classHandler->set($cacheKey, $cacheData, 2);
		/* @assert: check cache existing */
		$this->assertTrue($this->_classHandler->get($cacheKey) === $cacheData);
		
		// delete cache auto
		sleep(3);
		
		/* @assert: is cache deleted ? */
		$this->assertFalse($this->_classHandler->get($cacheKey));
		
		$this->_classHandler->set($cacheKey, $cacheData);
		
		// delete cache manually
		$this->_classHandler->drop($cacheKey);
		
		/* @assert: is cache deleted ? */
		$this->assertFalse($this->_classHandler->get($cacheKey));
		
		// get content from buffer
		if ($this->_classHandler->start($cacheKey, 2)) {
			echo $cacheData;
		$this->_classHandler->end(); }
		
		/* @assert: check cache existing */
		$this->assertTrue($this->_classHandler->get($cacheKey) === $cacheData);
		
		// delete cache manually
		$this->_classHandler->drop($cacheKey);
		
		/* @assert: is cache deleted ? */
		$this->assertFalse($this->_classHandler->get($cacheKey));
	}
	
}
