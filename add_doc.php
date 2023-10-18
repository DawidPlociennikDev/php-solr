<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require 'vendor/autoload.php';
use Ramsey\Uuid\Uuid;

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
$doc = $update->createDocument();

$uuid = Uuid::uuid4();
$uuidString = $uuid->toString();
$doc->addField('id', $uuidString);
$doc->addField('name', 'Produkt '.$uuidString);
$doc->addField('cat', randCategories());
$doc->addField('price', randPrice());
$doc->addField('inStock', (bool)rand(0, 1));

$update->addDocument($doc);
$update->addCommit();
return $client->update($update);

function randPrice() : float {
    $integerPart = rand(0, 100);
    $decimalPart = mt_rand(0, 99) / 100;
    $randomNumber = $integerPart + $decimalPart;
    return number_format($randomNumber, 2);
}

function randCategories() : array {
    $array = ["electronics", "graphics card", "electronics and computer1", "currency", "memory", "music", "book", "software", "printer"];
    $countCategories = rand(1,3);
    $selectedStrings = [];
    for ($i = 0; $i < $countCategories; $i++) {
        $randomIndex = array_rand($array);
        $selectedStrings[] = $array[$randomIndex];
    }
    return array_unique($selectedStrings);
}