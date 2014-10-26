# RCache
<p>Simple PHP class for caching data in files and memory</p>

### Installing via Composer

The recommended way to install RCache is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of RCache:

```bash
composer require i80586/rcache
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## Examples

### Data caching:

#### File cache (manually cache)

```php

use RCache\Cache;
use RCache\FileCache;

$cache = new Cache(new FileCache(__DIR__ . '/cache'));

// save data in cache
$cache->set('country', [
		'city' => 'Baku',
		'country' => 'Azerbaijan'
	], 3600);

// get cache by identifier
$country = $cache->get('country');

// remove cache
$cache->drop('country');
```

#### File cache (content cache)

```php

use RCache\Cache;
use RCache\FileCache;

$cache = new Cache(new FileCache(__DIR__ . '/cache'));

...other HTML content...
<?php if ($cache->start('fragment-caching', 3600)) { ?>

    ...content to be cached...

<?php $cache->end(); } ?>
...other HTML content...
```

#### Memory cache (manually cache)

```php

use RCache\Cache;
use RCache\MemCache;

$cache = new Cache(new MemCache());

// save data in cache
$cache->set('country', [
		'city' => 'Baku',
		'country' => 'Azerbaijan'
	], 3600);

// get cache by identifier
$country = $cache->get('country');

// remove cache
$cache->drop('country');
```

#### Memory cache (content cache)

```php

use RCache\Cache;
use RCache\MemCache;

$cache = new Cache(new MemCache());

...other HTML content...
<?php if ($cache->start('fragment-caching', 3600)) { ?>

    ...content to be cached...

<?php $cache->end(); } ?>
...other HTML content...
```