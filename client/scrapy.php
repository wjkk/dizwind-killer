<?php 
date_default_timezone_set("Asia/Shanghai");
set_time_limit(0);
require_once("simple_html_dom.php");
class Scrapy 
{
    public $data = array();
    public $list = array();
    public $xpath = null;
    public $listDom = null;
    public $isLatest = false;
    public $endKeyValue = null;
    public $newEndKeyValue = null;
    public $server = "http://localhost/dizwind";
        
    function run() {
        set_time_limit(120);
        ini_set('default_socket_timeout', 120);
        $this->task = null;
        $this->getTask();
        $this->site = $this->task['site'];
        $this->isLatest = false;
        $this->newEndKeyValue = null;
        
        if (file_exists("cursor/{$this->site}_saved.php")) {
            require_once("cursor/{$this->site}_saved.php");
            $this->scrapyed = $scrapyed;
        } else {
            $this->scrapyed = array();
        }
        
        if (!isset($this->task['url']) || empty($this->task['url'])) {
            list($href, $start, $end, $step) = $this->task['href'];
            for($i=$start; $i<=$end; $i=$i+$step) {
                $this->task['url'][] = sprintf($href, $i);
            }
        }
        
        foreach ($this->task['url'] as $url) {
            $this->scrapyUrl($url);
            if ($this->isLatest) {
                break;
            }
        }
        @file_put_contents("cursor/{$this->site}_saved.php", '<?php $scrapyed='.var_export($this->scrapyed, true) . "; ?>");
    }
    
    function scrapyUrl($url) {
        $this->url = $url;
        $this->list = array();
        $this->html = $this->getHtml();
        $this->listDom = $this->html->find($this->task['path']);
        $this->getData();                                          
        $this->getListData();
        $this->save();
        unset($this->list);
        unset($this->xpath);
        unset($this->listDom);
    }
    
    function meetLatest($item) {
        if ($this->newEndKeyValue === null) {
            $this->newEndKeyValue = $item[$this->task['endkey']];
        }
        if ($this->endKeyValue === null) {
            $this->endKeyValue = @file_get_contents("cursor/{$this->site}.lastest");
            $this->endKeyValue = strtotime($this->endKeyValue);
            if (empty($this->endKeyValue)) {
                $this->endKeyValue = strtotime(date("y-m-d")) - 3*24*3600; 
            }
        }

        if (strtotime($item[$this->task['endkey']]) <= $this->endKeyValue) {
            $this->log("meet latest!");
            $this->isLatest = true;
            @file_put_contents("cursor/{$this->site}.lastest", $this->newEndKeyValue);
        }
        return $this->isLatest;
    }
    
    function getData() {

    }
    
    function getListData() {
        $listItems = $this->task['list'];
        if ($this->listDom == null) {
            $this->log("get dom file: $this->url", "error");
            return true;
        }
        $data = array();
        if ($this->task['site'] == 'weiphone') {
            $count = 16;
            while($count--){
                array_shift($this->listDom);
            }
        }
        foreach ($this->listDom as $list) {
            $this->listItem = $list;
            $item = array();
            foreach ($this->task['list'] as $key => $para) {
                $param_value = null;
                $fun = "\$param_value = \$this->$para;";
                eval($fun);
                if (isset($this->task['convert']) && $this->task['convert'] && ($key=="title" || $key == 'author' || $key == 'action')) {
                    $param_value = mb_convert_encoding($param_value, 'UTF-8', $this->task['convert']);
                }
                $item[$key] = $param_value;
            }
            if (!isset($item['reply_time']) || empty($item['reply_time'])) {
                unset($item);
                continue;
            }
            if ($this->meetLatest($item)) {
                break;
            }
            if (isset($this->task['hook']) && !empty($this->task['hook']) && function_exists($this->task['hook'])) {
                $this->task['hook']($item, $this->task);
            }

            if (isset($this->scrapyed[$item['thread_id']])) {
                unset($item);
                continue;
            } else {
                $this->scrapyed[$item['thread_id']] = true;
            }
            $item['href'] = str_replace(array('&amp;', '&'), '&', $item['href']);
            $data[$item['thread_id']] = $item;
            unset($item);
        }
        $this->list = array_merge($this->list, $data);
        unset($data);
    }
    function getHtml() {
        $time_start = microtime(true);
        $header[0] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $header[] = "Host: www.baidu.com";
        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.2; rv:13.0) Gecko/20100101 Firefox/13.0.1";
        $header[] = "Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3";
        $header[] = "Accept-Encoding: gzip, deflate";
        $header[] = "Connection: keep-alive";
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; rv:13.0) Gecko/20100101 Firefox/13.0.1');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        //$html = curl_exec($ch);
        */
        $html = file_get_contents($this->url);
        
        if (!$html) {
            $error = "get url failded:" .$this->url . ":" . var_export($html);
            $this->log($error, 'error');
            exit;
        }
        $dom_html = str_get_html($html);
        if (!$dom_html) {
            $error = "get dom from html failed : {$this->url} ";
            $this->log($error, 'error');
            exit;
        }
        $time_end  = microtime(true);
        $time = $time_end - $time_start;
        $this->log("get: $time : $this->url", "profile");
        return $dom_html;
    }
    
    function getXpath($url) {
        $time_start = microtime(true);
        $header[0] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $header[] = "Host: www.baidu.com";
        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.2; rv:13.0) Gecko/20100101 Firefox/13.0.1";
        $header[] = "Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3";
        $header[] = "Accept-Encoding: gzip, deflate";
        $header[] = "Connection: keep-alive";
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; rv:13.0) Gecko/20100101 Firefox/13.0.1');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        //$html = curl_exec($ch);
        */
        $html  = file_get_contents($url);
        if (!$html) {
            $error = "<br />cURL error number:" .curl_errno($ch) . ":" . curl_error($ch);
            $this->log($error, 'error');
            exit;
        }
        if ($this->site == 'maxpda') {
            $html = str_replace(array('&amp;', '&'), '&amp;', $html);
        }
        $dom = new DOMDocument();
        $doc->recover = true;
        $dom->strictErrorChecking = false;
        $dom->loadHTML($html);
        $dom->normalize();
        $this->xpath = new DOMXPath($dom);
        $time_end  = microtime(true);
        $time = $time_end - $time_start;
        $this->log("get: $time : $url", "profile");
        unset($ch);
        unset($html);
        unset($dom);
        return true;
    }
    
    function log($msg, $level='log') {
        $time = date('Y-m-d h:i:s');
        $msg = json_encode($msg);
        @file_put_contents("log/{$this->site}.log", "$time $level {$msg}\r\n", FILE_APPEND);
    }
    
    function save() {
        if(empty($this->list)) {
            exit();
        }
        $url = $this->server . '/recive.php';
        $time_start = microtime(true);
        $result = $this->http_post($url, $this->list);
        var_dump($result);
        $time_end  = microtime(true);
        $time = $time_end - $time_start;
        $count = count($this->list);
        $this->log("send data: $time : $count : $url : $result ", "profile");
    }
    
    function post($url, $data) {
        /*
        $data = 'data=' . json_encode($data);
        $time_start = microtime(true);
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
        $result = curl_exec($ch);
        curl_close($ch);
        $time_end  = microtime(true);
        $time = $time_end - $time_start;
        $count = count($data);
        $this->log("send data: $time : $count : $url ", "profile");
        */
    }
    
    function http_post ($url, $data)
    {
        $send_data['data'] = json_encode($data);
        $data_url = http_build_query ($send_data);
        $data_len = strlen ($data_url);
    
        return array (
            'content' =>file_get_contents 
            (
                $url, false, stream_context_create 
                (
                    array 
                    (
                        'http'=>array(
                            'method'=>'POST',
                            'header'=>"Content-type:application/x-www-form-urlencoded\r\nConnection: close\r\nContent-Length: $data_len\r\n",
                            'content'=>$data_url
                        )
                    )
                )
            ), 
            'headers'=>$http_response_header
        );
    }
    
    function getTask() {
        $url = $this->server . '/task.php';
        $a_task = file_get_contents($url);
        $task = unserialize($a_task);
        if (!empty($task) && is_array($task)) {
            $this->task = $task;
            return true;
        } else {
            if(empty($task)) {
                $this->log("cannot get task : $url", "error");
                exit;
            }
        }
    }
    function j($path, $para, $posi = 0) {
        $a = $this->listItem->find($path, $posi);
        if (is_object($a)) {
            return $a->$para;
        } else {
            return '';
        }
    }
    
    function r($reg, $para, $posi = 1) {
        preg_match($reg, $para, $tmp);
        if (isset($tmp[$posi])) {
            return $tmp[$posi];
        } else {
            return '';
        }
    }
    
    function s() {
        $paras = func_get_args();
        $result = '';
        foreach ($paras as $para) {
            $result .= $para;
        }
        return $result;
    }
}

?>
