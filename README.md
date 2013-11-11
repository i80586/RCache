<h2>RFileCache</h2>
<p>Simple PHP class for caching data in files</p>

<br/>

<h3>Usage</h3>

<p>
<h4>Simple example</h4>
<b>
$cache=new \RLibrary\RFileCache(__dir__ . '/cache');
<br/>
$cache->set('country', array('city' => 'Baku', 'country' => 'Azerbaijan'), 3600);
<br/>
$country=$cache->get('country');
<br/>
echo implode(', ', $country);
<br/>
$cache->drop('country');
<br/>
</b>
</p>