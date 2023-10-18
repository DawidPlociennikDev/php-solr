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
$query = $client->createSelect();

$name = !empty($_POST['name']) ? $_POST['name'] : '*';
$cat = !empty($_POST['cat']) ? $_POST['cat'] : '*';
$inStock = $_POST['inStock'];
$minPrice = $_POST['minPrice'];
$maxPrice = $_POST['maxPrice'];
$query->setQuery('name:'.$name.' AND cat:'.$cat.' AND inStock:'.$inStock.' AND price:['.$minPrice.' TO '.$maxPrice.']');

if (!empty($_POST['sort'])) {
    $sort = explode('|', $_POST['sort']);
    $query->addSort($sort[0], $sort[1]);
}

$query->setRows(!empty($_POST['rows']) ? $_POST['rows'] : 10);

$resultset = $client->select($query);
foreach ($resultset as $index => $doc) {
    $cat = (is_array($doc->cat)) ? implode(',', $doc->cat) : $doc->cat;
    echo '<li id="'.$doc->id.'" class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1" style="width: 800px">'.++$index.'. 
                        <span id="div-'.$doc->id.'" onclick="updateName('.'\''.$doc->id.'\''.')" class="title"><span id="name-'.$doc->id.'">'.$doc->name.'</span></span>
                    </h5>
                    <div class="text-right">
                        <small>Kategoria: <b>'.$cat.'</b></small>
                        <hr>
                        <p class="text-right">Cena: $'.$doc->price.'</p>
                        <div class="text-right">
                            <i class="fa fa-'.($doc->inStock ? "check" : "times").' text-'.($doc->inStock ? "success" : "danger").'"></i>
                            <button onclick="sendAjaxToDeleteById('.'\''.$doc->id.'\''.')" class="btn btn-warning ml-2" style="cursor:pointer">
                                <i class="fa fa-trash text-dark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </li>';
}

?>