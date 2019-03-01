<?php

namespace Kosatyi\Blogger;

class Collection extends \ArrayIterator{

    public function __construct(Array $data){
        parent::__construct($data);
    }

    public function current() {
        $item = parent::current();
        return new Model( $item );
    }

}