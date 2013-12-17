# RFileCache
<p>Simple PHP class for caching data in files</p>


## Examples

### Data caching:

```php
include_once 'RCache/RCache.php';
include_once 'RCache/RFileCache.php';
include_once 'RCache/RCaching.php';

$cache = new RLibrary\RCaching(
					new RLibrary\RFileCache(__DIR__ . '/cache/')
				);

// save data in cache
$cache->getHandler()->set('country', 
							array('city' => 'Baku', 'country' => 'Azerbaijan'),
						 3600);
// get cache by identifier
$country = $cache->getHandler()->get('country');
// remove cache
$cache->getHandler()->drop('country');
```

### Fragment caching:

```php
include_once 'RCache/RCache.php';
include_once 'RCache/RFileCache.php';
include_once 'RCache/RCaching.php';

$cache = new RLibrary\RCaching(
					new RLibrary\RFileCache(__DIR__ . '/cache/')
				);

...other HTML content...
<?php if ($cache->getHandler()->start('fragment-caching', 3600)) { ?>

    ...content to be cached...

<?php $cache->getHandler()->end(); } ?>
...other HTML content...
```