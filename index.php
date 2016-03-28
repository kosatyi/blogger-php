<?
    require_once('src/Blogger.php');

    $blogger = new Blogger('2503794527792142782');

    $result = $blogger->getList();

    print_r($result);
