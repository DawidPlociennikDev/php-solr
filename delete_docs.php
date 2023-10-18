<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require 'vendor/autoload.php';
$adapter = new Solarium\Core\Client\Adapter\Curl();
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();

$config = array(
    'endpoint' => array(
        'localhost' => array(
            'host' => 'localhost',
            'port' => 8983,
            'path' => 'solr',
            'core' => 'techproducts',
        )
    )
);

$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$update = $client->createUpdate();
$update->addDeleteQuery('*:*');
$update->addCommit();
$result = $client->update($update);
