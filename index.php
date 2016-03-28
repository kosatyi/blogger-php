<?
    require_once('src/Blogger.php');
    $blogger = new Blogger('2503794527792142782');
    $model = $blogger->getList();
    $list  = $model->each('feed.entry');
    foreach($list as $item){
        $item->dump();
    }

