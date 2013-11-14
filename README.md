# RFileCache
<p>Simple PHP class for caching data in files</p>


## Examples

### Data caching:

```php
include_once 'RFileCache/RFileCache.php';

$cache=new RLibrary\RFileCache(__dir__ . '/cache');

// save data in cache
$cache->set('country', array('city' => 'Baku', 'country' => 'Azerbaijan'), 3600);
// get cache by identifier
$country=$cache->get('country');
// remove cache
$cache->drop('country');
```

### Fragment caching:

```php
include_once 'RFileCache/RFileCache.php';

$cache=new RLibrary\RFileCache(__dir__ . '/cache');

...other HTML content...
<?php if($cache->start('fragment-caching', 3600)) { ?>

    ...content to be cached...

<?php $cache->end(); } ?>
...other HTML content...
```