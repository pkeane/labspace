<?php

include 'config.php';

$url = "http://wwwtest.utexas.edu/cola/_webservices/colaweb/people";

$valid = array();
$php_data = Dase_Json::toPhp(file_get_contents($url));
foreach ($php_data as $set) {
	$valid[] = $set['eid'];
}
file_put_contents('valid_users.json',Dase_Json::get($valid));

