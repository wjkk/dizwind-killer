<?php 
date_default_timezone_set("Asia/Shanghai");

$task = new Task();
$task->task();

class Task
{
    public $contentRate = 90; 
    public function task()
    {
        $seed = mt_rand(1, 100);
        if ($seed <= $this->contentRate) {
            $task = $this->getContentTask();
        } else {
            $task = $this->getListTask();
        }

        echo serialize($task);
        flush();
        $time = date('Y-m-d h:i:s');
        file_put_contents("log/task.log", "$time : {$_SERVER['REMOTE_ADDR']} : {$task['site']}\r\n", FILE_APPEND);
        clearstatcache();
    }

    private function getListTask()
    {
        $cursor = @file_get_contents("cursor/list.latest");
        if (empty($cursor)) {
            $cursor = 0;
        } else {
            $cursor = $cursor % count($this->listTask);
        }
        $task = $this->listTask[$cursor];
        @file_put_contents("cursor/list.latest", ++$cursor);
        $task['type'] = 'list';
        return $task;
    }
    
    private function getContentTask()
    {
        $thread = $this->getThread();
        if (!isset($this->contentTask[$thread['site_id']])) {
            $this->disableSiteContent($thread['site_id']);
            return false;
        }
        $task = $this->contentTask[$thread['site_id']];
        $task['gid'] = $thread['gid'];
        $task['href'] = $thread['href'];
        $task['type'] = 'content';
        return $task;
    }
    
    private function getThread()
    {
        //$query = $this->db->prepare("SELECT * FROM `dizwind` WHERE `status`=1 ORDER BY `id` DESC limit 1");
        $query = $this->db->prepare("SELECT * FROM `dizwind` WHERE `status`=1 AND site_id=111 ORDER BY RAND() DESC limit 1");
        $query->execute();
        $thread = $query->fetch(); 
        return $thread;
    }
    
    private function disableSiteContent($site_id)
    {
        $sql = "UPDATE `dizwind` SET `status`=3 WHERE `site_id`={$site_id}";
        $stm = $this->db->prepare($sql);
        $result = false;
        if( $stm && $stm->execute()) {
            $result = $stm->rowCount();
        }
    }
    
    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'test';
        $username = 'root';
        $password = '';
        $this->db = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        
        $this->listTask[] = array(
            'type' => 'list',
            'site' => 'diypda',
            'site_id' => '101',
            'href' => array("http://tzsc.diypda.com/forum.php?mod=forumdisplay&fid=24&sortid=81&ortid=81&sortid=81&page=%d", 1, 10, 1),   
            'path' => "tbody[id^=normalthread_]",                                              
            'list' => array(
                'href' => "j('th a[class=xst]', 'href')",
                'title' => "j('th a[class=xst]', 'innertext')",
                'author' => "j('td cite a', 'innertext', -1)",
                'thread_id' => 'r("/thread-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('em a span', 'title')", 
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ), 
            'endkey' => 'reply_time',
            'convert' => 'GBK',
        );
        
        $this->contentTask['101'] = array(
            'type' => 'content',
            'site_id' => '101',
            'site' => 'diypda',
            'href' => '',
            'gid' => '',
            'content' => array('text' =>"j('div[class=t_msgfont]', 'innertext')"),
            'convert' => 'GBK',
        );
        
        $this->listTask[] = array(
            'site' => 'maxpda',                                                  
            'site_id' => '102',                                                    
            'href' => array("http://bbs.maxpda.com/forum-44-%d.html", 1, 10, 1),   
            'path' => "tbody[id^=normalthread_]",                                            
            'list' => array(
                'href' => "j('th a[class=xst]', 'href')",
                'title' => "j('th a[class=xst]', 'innertext')",                                                    
                'author' => "j('cite a', 'innertext')",
                'action' => "j('th em a', 'innertext')",
                'thread_id' => 'r("/thread-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('td em a', 'innertext', -1)",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            'endkey' => 'reply_time',
            'convert' => 'GBK',
        );
        
        $this->listTask[] = array(
            'site' => 'hiapk',                                                    
            'site_id' => '103',                                                    
            'href' => array("http://bbs.hiapk.com/forum-187-%d.html", 1, 10, 1),   
            'path' => "tbody[id^=normalthread_]",                                              
            'list' => array(
                
                'href' => "j('td a[class=xst]', 'href')",
                'title' => "j('td a[class=xst]', 'innertext')",                                                           
                'author' => "j('td cite a', 'innertext')",
                'action' => "j('td em a', 'innertext')",
                'thread_id' => 'r("/thread-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('em span', 'title', -1)",   
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),                                              
            'endkey' => 'reply_time',
            'convert' => 'GBK',
        );
        
        $this->listTask[] = array(
            'site' => 'in189',                                                    
            'site_id' => '104',                                                    
            'href' => array("http://www.in189.com/forum-200-%d.html", 1, 10, 1),
            'path' => "tbody[id^=normalthread_]",
            'list' => array(
                'href' => "j('th a[class=xst]', 'href')",
                'title' => "j('th a[class=xst]', 'innertext')",
                'author' => "j('cite a', 'innertext')",
                'action' => "j('th em a', 'innertext')",
                'thread_id' => 'r("/thread-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('em span', 'title', -1)",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            'endkey' => 'reply_time',
            'convert' => 'GBK',
        );
        $this->listTask[] = array(
            'site' => 'gfan',                                                 
            'site_id' => '105',                                                    
            'href' => array("http://bbs.gfan.com/forum-23-%d.html", 1, 10, 1),   
            'path' => "tbody[id^=normalthread_]",                                           
            'list' => array(
                'href' => "j('th span a', 'href')",
                'title' => "j('th span a', 'innertext')",
                'author' => "j('cite a', 'innertext')",
                'action' => "j('th em a', 'innertext')",
                'thread_id' => 'r("/android-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('em span', 'title', -1)",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            'endkey' => 'reply_time',
        );
        $this->listTask[] = array(
            'site' => 'zoopda',                                                 
            'site_id' => '108',                                                    
            'href' => array("http://bbs.zoopda.com/forum-54-%d.html", 1, 10, 1),   
            'path' => "tbody[id^=normalthread_]",
            'list' => array(
                'href' => "j('th span[class=xst] a', 'href', -1)",
                'title' => "j('th span[class=xst] a', 'innertext', -1)",                                                    
                'author' => "j('p[class=mtn xg1] a', 'innertext')",
                'action' => "j('th span[class=xst] font', 'innertext')",
                'thread_id' => 'r("/thread-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('td em span', 'title')",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            'endkey' => 'reply_time',
        );
        $this->listTask[] = array(
            'site' => 'rayi',                                                 
            'site_id' => '106',                                                    
            'href' => array("http://bbs.rayi.cn/forum-34-%d.html", 1, 10, 1),   
            'path' => "tbody[id^=normalthread_]",                                           
            'list' => array(
                'href' => "j('th a[class=xst]', 'href')",
                'title' => "j('th a[class=xst]', 'innertext')",                                                         
                'author' => "j('cite a', 'innertext')",
                'action' => "j('th em a', 'innertext')",
                'thread_id' => 'r("/thread-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('td em a', 'innertext', -1)",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            'endkey' => 'reply_time',
        );
        
        $this->listTask[] = array(
            'site' => 'weiphone',                                                 
            'site_id' => '107',                                                    
            'href' => array("http://bbs.weiphone.com/thread-htm-fid-29-page-%d.html", 1, 10, 1),   
            'path' => "tbody[id=threadlist] tr",                                           
            'list' => array(
                'href' => "j('a[name=readlink]', 'href')",
                'title' => "j('a[name=readlink]', 'innertext')",                                                         
                'author' => "j('td[class=author] a', 'innertext')",
                'action' => "j('a[class=view]', 'innertext')",
                'thread_id' => 'r("/read-htm-tid-([0-9]+)/", $item["href"])',
                'reply_time' => "j('td[class=author] a[title]', 'title', -1)",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            'endkey' => 'reply_time',
        );
        
        
        $this->listTask[] = array(
            'site' => 'weimei',                                                 
            'site_id' => '109',
            'href' => array("http://f1.avzcy.info/bbs/forum-13-%d.html", 1, 1, 1),   
            'path' => "form[name=moderate] div[class=spaceborder] table tr",
            'list' => array(
                'href' => "j('td[class=f_title] a', 'href')",
                'title' => "j('td[class=f_title] a', 'innertext')",                                                    
                //'author' => "j('td[class=f_author] a', 'innertext')",
                //'action' => "j('th span[class=xst] font', 'innertext')",
                'thread_id' => 'r("/thread-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('td[class=f_last] span a', 'innertext')",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            //'endkey' => 'reply_time',
            'convert' => 'GBK',
        );
        
        $this->contentTask['109'] = array(
            'type' => 'content',
            'site_id' => '109',
            'site' => 'diypda',
            'href' => '',
            'gid' => '',
            'content' => array('text' =>"j('div[class=t_msgfont]', 'innertext')"),
            'convert' => 'GBK',
        );
        
        $this->listTask[] = array(
            'site' => 'oabt',
            'site_id' => '110',
            'host'     => 'oabt.org',
            'compress' => 'compress.zlib://',
            'href' => array("http://oabt.org/index.php?page=%d", 1, 2, 1),
            'path' => "tbody[onmouseover]",
            'list' => array(
                'href' => "j('a[target=_blank]', 'href')",
                'title' => "j('a[target=_blank]', 'innertext')",
                'thread_id' => "j('a[target=_blank]', 'rel')",
                'mag' => "j('a[class=magDown]', 'href')",
                'ed2k' => "a('a[class=ed2kDown], a[class=ed2kNone]', 'ed2k')",
                'duration' => "j('td[class=capacity],td[class=time]', 'innertext')",
                'file_size' => "j('td[class=seed]', 'innertext')",
                'action' => "j('a[class=sbule]', 'innertext')",
                //'action_url' => "j('a[class=sbule]', 'href')",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            //'endkey' => 'reply_time'
        );
        
        
        $this->listTask[] = array(
            'site' => '1lou',
            'site_id' => '111',
            'host'     => 'http://bbs.1lou.com',
            //'compress' => 'compress.zlib://', 
            'cleanrequest' => true,
            'href' => array("http://bbs.1lou.com/forum-62-%d.html", 1, 1, 1), 
            'path' => "tbody[id^=normalthread_]",                                
            'list' => array(
                'href' => "j('th a[class=xst]', 'href')",
                'title' => "j('th a[class=xst]', 'innertext')",                                                         
                'author' => "j('td cite a', 'innertext')",
                'thread_id' => 'r("/thread-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('a span', 'title', -1)",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            //'endkey' => 'reply_time',
            'convert' => 'GBK',
        );
        
        
        $this->listTask[] = array(
            'site' => '1lou',
            'site_id' => '111',
            'host'     => 'http://bbs.1lou.com',
            //'compress' => 'compress.zlib://', 
            'cleanrequest' => true,
            'href' => array("http://bbs.1lou.com/forum-558-%d.html", 1, 1, 1),
            'path' => "tbody[id^=normalthread_]",                                
            'list' => array(
                'href' => "j('th a[class=xst]', 'href')",
                'title' => "j('th a[class=xst]', 'innertext')",                                                         
                'author' => "j('td cite a', 'innertext')",
                'thread_id' => 'r("/thread-([0-9]+)-/", $item["href"])',
                'reply_time' => "j('a span', 'title', -1)",
                'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
                'site_id' => 's($this->task["site_id"])',
                'site' => 's($this->task["site"])',
            ),
            //'endkey' => 'reply_time',
            'convert' => 'GBK',
        );
        
        $this->contentTask['111'] = array(
            'type' => 'content',
            'site_id' => '111',
            'site' => '1lou',
            'cleanrequest' => true,
            'href' => '',
            'gid' => '',
            'content' => array('text' =>"j('td[class=t_f]', 'innertext')"),
            'convert' => 'GBK',
        );
    }
}
?>
