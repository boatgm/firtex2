<?php
include_once dirname(__FILE__).'/../FirteXHTTPClient.php';

$client = new FirteXHTTPClient('http://localhost', '19801');
$result = $client->search('BODY:2;field_select=BODY:snippet');
print_r($result);
?>