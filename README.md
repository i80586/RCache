<h2>RFileCache</h2>
<p>Simple PHP class for caching data in files</p>

[![Build Status](https://travis-ci.org/i80586/RFileCache.png?branch=master)](https://travis-ci.org/i80586/RFileCache)

<h3>Usage</h3>

<p>
<h4>Examples</h4>

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

include_once 'RFileCache/RFileCache.php';

$cache=new RLibrary\RFileCache(__dir__ . '/cache');

...other HTML content...
<?php if($cache->start('fragment-caching', 3600)) { ?>

    ...content to be cached...

<?php $cache->end(); } ?>
...other HTML content...