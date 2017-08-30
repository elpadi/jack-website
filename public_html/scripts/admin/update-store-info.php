<?php
use Website\Shop\Square\Store;

require(__DIR__.'/bootstrap.php');

$cache = JACK_DIR.'/cache/square/catalog.json';
out(file_exists($cache)
	? "Cache file $cache exists."
	: "Cache file $cache does not exists.");
out(file_exists($cache) && is_writable($cache)
	? "Cache file $cache is writable."
	: "Cache file $cache is not writable.");

out("Fetching catalog data...");
$catalog = Store::fetchCatalogDataFromApi();
dump($catalog);

$json = sprintf('[%s]', implode(',', $catalog));
$written = file_put_contents($cache, $json);
out($written
	? "Success. $written bytes written to cache file $cache."
	: "Error writing to cache file $cache.");

