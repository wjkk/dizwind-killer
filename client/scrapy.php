<?php 
date_default_timezone_set("Asia/Shanghai");
//set_time_limit(0);
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
    public $forum = 0;
    public $channel = 0;
    public $server = "http://www.faniao.com/dizwind";
        
    function parser() {
        set_time_limit(0);
        ini_set('default_socket_timeout', 120);
        $this->task = null;
        $this->getParserTask();
        $dom_html = str_get_html($this->task['content']);
        $images = $dom_html->find("img[onload]");
        foreach ($images as $img) {
            if (isset($img->src)) {
                $src = $img->src;
                $tmp = explode ('/', $src);
                $filename = array_pop($tmp);
                $file_path = "pic/" . $this->task['gid'] . '-' . md5($src) . '-' . $filename;
                file_put_contents($file_path, @file_get_contents($src));
                $image['new'] = $file_path;
                $image['old'] = $src;
                $this->data['pics'][] = $image;
            }
        }
        $this->sendParser();
    }
    
    function run() {
        set_time_limit(0);
        ini_set('default_socket_timeout', 120);
        $this->task = null;
        $this->getTask();
        $this->site = $this->task['site'];
        $this->isLatest = false;
        $this->newEndKeyValue = null;
        
        if ( !$this->task['nocache'] && file_exists("cursor/{$this->site}_saved.php")) {
            require_once("cursor/{$this->site}_saved.php");
            $this->scrapyed = $scrapyed;
        } else {
            $this->scrapyed = array();
        }
        
        //by comment
        if ($this->task['type'] == 'list') {
            if (isset($this->task["hrefs"]) && is_array($this->task["hrefs"]) && !empty($this->task["hrefs"])) {
                 $this->task['url'] = $this->task["hrefs"];
            } elseif (!isset($this->task['url']) || empty($this->task['url'])) {
                list($href, $start, $end, $step) = $this->task['href'];
                for($i=$start; $i<=$end; $i=$i+$step) {
                    $this->task['url'][] = sprintf($href, $i);
                }
            }
        } else {
            $this->task['url'][] = $this->task['href'];
        }
        
        foreach ($this->task['url'] as $forum => $url) {
            $this->forum = $forum;
            if (is_array($url)) {
                foreach ($url as $channel => $herf) {
                    $this->channel = $channel;
                    $this->scrapyUrl($herf);
                }
            } else {
                $this->scrapyUrl($url);
                if ($this->isLatest) {
                    break;
                }
            }
        }
        if ( !$this->task['nocache'] ) {
            @file_put_contents("cursor/{$this->site}_saved.php", '<?php $scrapyed='.var_export($this->scrapyed, true) . "; ?>");
        }
    }
    
    function scrapyUrl($url) {
        $this->url = $url;
        $this->list = array();
        $this->html = $this->getHtml();
        
        //by comment
        if ($this->task['type'] == 'list') {
            $this->listDom = $this->html->find($this->task['path']);
            $this->getListData();
        } elseif ($this->task['type'] == 'content') {
            $this->listItem = $this->html;
            $this->getContentData();
        }
    
        $this->save();
        unset($this->list);
        unset($this->xpath);
        unset($this->listDom);
    }
    
    function meetLatest($item) {
        if (!isset($this->task['endkey'])) {
            return false;
        }
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
    
    function getListData() {
        $this->list['data'] = array();
        $listItems = $this->task['list'];
        if ($this->listDom == null) {
            $this->log("get dom file: $this->url", "error");
            return true;
        }
        $data = array();
        /*
        if ($this->task['site'] == 'weiphone') {
            $count = 16;
            while($count--){
                array_shift($this->listDom);
            }
        }
        */

        foreach ($this->listDom as $list) {
            $this->listItem = $list;
            $item = array();
            foreach ($this->task['list'] as $key => $para) {
                $param_value = null;
                if (is_array($para)) {
                    while ($par = array_shift($para)) {
                        $fun = "\$param_value = \$this->$par;";
                        eval($fun);
                        if ($param_value) {
                            break;
                        }
                    }
                } else {
                    $fun = "\$param_value = \$this->$para;";
                    eval($fun);
                }
                if (isset($this->task['convert']) && $this->task['convert'] && ($key=="title" || $key == 'author' || $key == 'action')) {
                    $param_value = mb_convert_encoding($param_value, 'UTF-8', $this->task['convert']);
                }
                $item[$key] = trim($param_value);
            }
            if (isset($item['title']) && empty($item['title'])) {
                continue;
            }

            if (!isset($item['reply_time']) || empty($item['reply_time'])) {
                //$this->log("no reply_time, did not unset", "error");
                //unset($item);
                //continue;
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
            $item['forum'] = $this->forum;
            $item['channel'] = $this->channel;
            $data[$item['thread_id']] = $item;
            unset($item);
        }
        $this->list['type'] = $this->task['type'];
        $this->list['data'] = array_merge($this->list['data'], $data);
        unset($data);
    }
    
    function getContentData() {
        if (!isset($this->task['content'])) {
            return ;
        }
        foreach ($this->task['content'] as $param => $method) {
            $param_value = null;
            $fun = "\$param_value = \$this->$method;";
            eval($fun);
            if (isset($this->task['convert']) && $this->task['convert']) {
                $param_value = mb_convert_encoding($param_value, 'UTF-8', $this->task['convert']);
            }
            $item[$param] = $param_value;
        }
        $this->list['gid'] = $this->task['gid'];
        $this->list['type'] = $this->task['type'];
        $this->list['data'] = $item;
        unset($item);
    }
    
    function getHtml() {
        $time_start = microtime(true);
        
        if (isset($this->task['cleanrequest']) && $this->task['cleanrequest']) {
            $html = file_get_contents($this->url);
        } else {
            $header = '';
            $header .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
            $header .= "Accept-Encoding: gzip, deflate\r\n";
            $header .= "Accept-Language: en-us,en;q=0.5\r\n";
            $header .= "Connection: keep-alive\r\n";
            if (isset($this->task['host']) && $this->task['host']) {
                $header .= "Host: {$this->task['host']}\r\n";
            } else {
                $header .= "Host: www.baidu.com\r\n";
            }
            $header .= "Pragma: no-cache\r\n";
            $header .= "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0\r\n";
            if (isset($this->task['compress']) && $this->task['compress']) {
                $this->url = "compress.zlib://{$this->url}";
            }
            
            $html = file_get_contents($this->url, false, stream_context_create(
                        array 
                        (
                            'http'=>array(
                                'protocol_version'=>'1.1',
                                'method' => "GET", 
                                'header' => $header,
                            )
                        )
                   )
            );
            
        }
        
        //$header[0] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        /*
        $header[] = "Host: www.baidu.com";
        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.2; rv:13.0) Gecko/20100101 Firefox/13.0.1";
        $header[] = "Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3";
        $header[] = "Accept-Encoding: gzip, deflate";
        $header[] = "Connection: keep-alive";
        $html = file_get_contents("{$this->url}");
        */

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
    
        //by comment
        //$header = '';
        //$header .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
        /*
        $header .= "Accept-Encoding: gzip, deflate\r\n";
        $header .= "Accept-Language: en-us,en;q=0.5\r\n";
        $header .= "Connection: keep-alive\r\n";
        $header .= "Host: oabt.org\r\n";
        $header .= "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:15.0) Gecko/20100101 Firefox/15.0.1\r\n";
        $html = file_get_contents("compress.zlib://http://oabt.org", false, stream_context_create(
                    array 
                    (
                        'http'=>array(
                            'protocol_version'=>'1.1',
                            'method' => "GET", 
                            'header' => $header,
                        )
                    )
               )
        );
        */
        
        $header[0] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $header[] = "Host: www.baidu.com";
        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.2; rv:13.0) Gecko/20100101 Firefox/13.0.1";
        $header[] = "Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3";
        $header[] = "Accept-Encoding: gzip, deflate";
        $header[] = "Connection: keep-alive";

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
        if (empty($this->site)) {
            $file = 'error';
        } else {
            $file = $this->site;
        }
        @file_put_contents("log/{$file}.log", "$time $level {$msg}\r\n", FILE_APPEND);
    }
    
    function save() {
        if(empty($this->list)) {
            exit();
        }
        $url = $this->server . '/recive.php';
        $time_start = microtime(true);
        $result = $this->http_post($url, $this->list);
        var_dump($result);
        $result = json_encode($result);
        $time_end  = microtime(true);
        $time = $time_end - $time_start;
        $count = count($this->list['data']);
        $this->log("send data: $time : $count : $url : $result ", "profile");
    }
    
    function sendParser() {
        $this->data['gid'] = $this->task['gid'];
        if(empty($this->data)) {
            exit();
        }
        $url = $this->server . '/parser.php?a=recive';
        $time_start = microtime(true);
        $result = $this->http_post($url, $this->data);
        var_dump($result);
        $result = json_encode($result);
        $time_end  = microtime(true);
        $time = $time_end - $time_start;
        //$count = count($this->data['pics']);
        $this->log("send data: $time : $url : $result ", "profile");
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
    
    function getParserTask() {
        $url = $this->server . '/parser.php?a=get';
        $a_task = @file_get_contents($url);
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
        $this->data['gid'] = $this->task['gid'];
    }
    
    function getTask() {
        $url = $this->server . '/task.php';
        $a_task = @file_get_contents($url);
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

    function a($path, $para, $posi = 0) {
        $a = $this->listItem->find($path, $posi);
        if (is_object($a)) {
            return $a->getAttribute($para);
        } else {
            return '';
        }
    }

    function j($path, $para, $posi = 0) {
        if ($path) {
            $a = $this->listItem->find($path, $posi);
            if (is_object($a)) {
                return $a->$para;
            } else {
                return '';
            }
        } else {
            $a = $this->listItem;
            if (is_object($a)) {
                return $a->$para;
            } else {
                return '';
            }
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
