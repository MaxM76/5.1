<?php

require __DIR__ . "/vendor/autoload.php";

$i = 0;

$curLatitude = 55.76;
$curLongitude = 37.64;
$curAddress = '';

session_start();

$api = new \Yandex\Geo\Api();

if (isset($_GET['query'])) {
    $query = (string)$_GET['query'];
    $api->setQuery($query);
    $api->load();
    $response = $api->getResponse();
//    $response->getFoundCount(); // кол-во найденных адресов
    $collection = $response->getList();
//    $query = $response->getQuery();
/*    print_r($query);
    echo '<br>//////////////////////<br>';
    print_r($response);
    echo '<br>**********************<br>';
*/
    if (count($collection) > 0) {
        $curLatitude = $collection[0]->getLatitude();
        $curLongitude = $collection[0]->getLongitude();
    }
    $_SESSION['response'] = $response;
} else {
    $collection = [];
    $query ='';
}

if (isset($_SESSION['response'])) {
    $response  = $_SESSION['response'];
    $collection = $response->getList();
    $query = $response->getQuery();

    if (isset($_GET['item'])) {
        $curIndex = (int)$_GET['item'];
        $curLatitude = $collection[$curIndex]->getLatitude();
        $curLongitude = $collection[$curIndex]->getLongitude();
        $curAddress = $collection[$curIndex]->getAddress();
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <title>Домашнее задание к лекции 5.1 «Менеджер зависимостей Composer»</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript">
    </script>

    <script type="text/javascript">
        ymaps.ready(init);

        function init(){
            var myMap = new ymaps.Map("map", {
                center: [<?=$curLatitude?>, <?=$curLongitude?>],
                zoom: 7
            });

<?php
if (count($collection) > 0) {
   if (isset($_GET['item'])) {
?>

            var myPlacemark = new ymaps.Placemark([<?=$curLatitude?>, <?=$curLongitude?>], {
                hintContent: '<?= $curAddress ?>',
                balloonContent: '<?= $curAddress ?>'
            });
            myMap.geoObjects.add(myPlacemark);
<?php
    } else {
        foreach ($collection as $item) :
            $address = $item->getAddress(); // вернет адрес
            $latitude = $item->getLatitude(); // широта
            $longitude = $item->getLongitude(); // долгота
?>

            var myPlacemark = new ymaps.Placemark([<?=$latitude?>, <?=$longitude?>], {
                hintContent: '<?= $address ?>',
                balloonContent: '<?= $address ?>'
            });
            myMap.geoObjects.add(myPlacemark);
<?php
        endforeach;
    }
}
?>
        }
    </script>
  </head>

  <body>
    <div>
      <h1>Домашнее задание к лекции 5.1 «Менеджер зависимостей Composer»</h1>

      <form method="GET">
        <label>Введите адрес: </label>
        <input type="text" name="query" value="" placeholder="Введите адрес">
        <button type="submit">Найти</button>
      </form>

    </div>

    <div id="list">
      <h2>Результаты поиска для запроса "<?= $query ?>"</h2>

      <ul>

<?php
    foreach ($collection as $item) : ?>
        <li>
        <?php
        $address = $item->getAddress(); // вернет адрес
        $latitude = $item->getLatitude(); // широта
        $longitude = $item->getLongitude(); // долгота
        ?>
          <a href="index.php?query=<?= $query ?>&item=<?=$i++?>">
            <div>
              <h2><?= $address ?></h2>
              <p>[<?= $latitude ?>; <?= $longitude ?>]</p>

            </div>
          </a>
        </li>
<?php
    endforeach;
?>
      </ul>
    </div>

    <div id="map" style="width: 600px; height: 400px"></div>
  </body>
</html>