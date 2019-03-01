<?php

namespace Kosatyi\Blogger;

class Service {
    private $service = 'https://www.blogger.com/feeds/%s/posts/default/%s';
    private $id;
    private $params = array(
        'category' => '',
        'alt' => 'json',
        'prettyprint' => 'true'
    );
    public function __construct( $id = NULL )
    {
        $this->setId($id);
    }
    public function setId( $id ){
        $this->id = $id;
    }
    private function url($params = array(), $post = '')
    {
        $url = sprintf($this->service,$this->id,$post);
        $params = http_build_query(array_filter(array_merge($this->params, $params)));
        $url = $url . '?' . $params;
        return $url;
    }
    private function request($url = '')
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (isset($_SERVER['HTTP_USER_AGENT']))
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            $response = FALSE;
        }
        return $response;
    }
    public function getPageParams($page, $limit = 10)
    {
        $params = array();
        if (isset($page)) {
            $params['max-results'] = $limit;
            if ($page <= 1) {
                $params['start-index'] = 1;
            } else {
                $params['start-index'] = (max(1, $page - 1) * $params['max-results']) + 1;
            }
        }
        return ($params);
    }
    public function getList($params = array())
    {
        $params = array_merge(array('max-results' => 10, 'page' => 1), $params);
        $params = array_merge($params, $this->getPageParams($params['page'], $params['max-results']));
        $result = $this->request(
            $this->url($params)
        );
        return new Model($result);
    }
    public function getPost($id, $params = array())
    {
        $result =  $this->request(
            $this->url($params, $id)
        );
        return new Model($result);
    }
}