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
    public function __construct($blogid)
    {
        $this->blogid = $blogid;
    }
    private function getUrl($params=array(),$id='')
    {
        $url = sprintf($this->service, $this->blogid,$id);
        $params = http_build_query(array_filter(array_merge($this->params, $params)));
        $url = $url . '?' . $params;
        return $url;
    }
    private function requestUrl($url = '')
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
    public function getPageParams($page, $limit = 10){
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
    public function getList($params=array())
    {
        $params = array_merge(array('max-results' => 10), $params);
        if (isset($params['page']))
        {
            $params = array_merge($params, $this->getPageParams($params['page'], $params['max-results']));
        }
        return $this->requestUrl(
            $this->getUrl($params)
        );
    }
    public function getPost($id,$params=array())
    {
        return $this->requestUrl(
            $this->getUrl($params,$id)
        );
    }
}
