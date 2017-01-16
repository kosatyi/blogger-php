<?

class Blogger
{
    public $service = 'https://www.blogger.com/feeds/%s/posts/default/%s';
    public $blogid;
    public $params = array(
        'category' => '',
        'alt' => 'json',
        'prettyprint' => 'true'
    );

    public function __construct($blogid=NULL)
    {
        $this->setBlogId($blogid);
    }

    public function setBlogId($blogid){
        $this->blogid = $blogid;
    }

    private function url($params = array(), $id = '')
    {
        $url = sprintf($this->service, $this->blogid, $id);
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
        } catch (Exception $e) {
            $response = FALSE;
        }
        return $response;
    }
    /**
     * @param $page
     * @param int $limit
     * @return array
     */
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
        return new BloggerModel($result);
    }

    public function getPost($id, $params = array())
    {
        $result =  $this->requestUrl(
            $this->url($params, $id)
        );
        return new BloggerModel($result);
    }
}


class BloggerModel {
    private $data = NULL;
    public function __construct($data=NULL)
    {
        if(is_string($data)) $data = json_decode($data,TRUE);
        $this->data = $data;
    }
    public function attr($attr='',$value=NULL,$default=FALSE)
    {
        $attr    = explode('.', $attr);
        $arglen  = func_num_args();
        $count   = count($attr);
        if(isset($this->data[$attr[0]]) {
            $result =& $this->data[$attr[0]];
        } else {
            return NULL;
        }
        for ($i = 1; $i < $count; $i++) {
            if (is_object($result)) {
                $result =& $result->$attr[$i];
            } else if (is_array($result)) {
                $result =& $result[$attr[$i]];
            } else {
                return NULL;
            }
        }
        if($arglen==1||$arglen==3)
        {
            return $default && empty($result) ? $value : $result;
        }
        $result = $value;
        return $this;
    }
    public function each( $name ){
        return new BloggerList( $this->attr($name,array(),TRUE) );
    }
    public function getData(){
        return $this->data;
    }
    public function dump($name=NULL){
        $data = is_string($name) ? $this->attr($name) : $this->data;
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

class BloggerList extends ArrayIterator{
    public function __construct(Array $data){
        parent::__construct($data);
    }
    public function current() {
        $item = parent::current();
        return new BloggerModel( $item );
    }
}