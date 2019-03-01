<?php

namespace Kosatyi\Blogger;

use Kosatyi\DataModel\Model as DataModel;

class Model extends DataModel {
    public function __construct($data=NULL)
    {
        if( is_string($data) ) {
            $data = json_decode($data,TRUE);
        }
        if( is_array( $data ) ) {
            $this->data($data);
        }
    }
    public function each( $name ){
        $list = $this->attr($name);
        $list = is_array($list) ? $list : array();
        return new Collection( $list );
    }
    public function dump($name=NULL){
        $data = is_string($name) ? $this->attr($name) : $this->all();
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}