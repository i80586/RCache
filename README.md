<h2>RFileCache</h2>
<p>Simple PHP class for caching data in files</p>

<br/>

<h3>Usage</h3>

<p>
<h4>Simple example</h4>
<b>
$cache=new \RLibrary\RFileCache(__dir__ . '/cache');
</b>
<br/>
// Set
<br/>
<b>
$cache->set('country', array('city' => 'Baku', 'country' => 'Azerbaijan'), 3600);
</b>
<br/>
<b>
$country=$cache->get('country');
</b>
<br/>
<b>
echo implode(', ', $country);
</b>
<br/>
<b>
$cache->drop('country');
</b>
<br/>
</p>