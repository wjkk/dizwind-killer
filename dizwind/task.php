<?php 
$task[] = array(
    'site' => 'diypda',
    'site_id' => '101',
    'href' => array("http://www.diypda.com/forum-24-%d.html", 1, 10, 1),   
    'path' => "tbody[id^=normalthread_]",                                              
    'list' => array(
        'href' => "j('th a[class=xst]', 'href')",
        'title' => "j('th a[class=xst]', 'innertext')",                                                           
        'author' => "j('cite a', 'innertext')",
        'action' => "j('th font', 'innertext')",
        'thread_id' => 'r("/tid=([0-9]+)&/", $item["href"])',
        'reply_time' => "j('em span', 'title', -1)",   
        'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
        'site_id' => 's($this->task["site_id"])',
        'site' => 's($this->task["site"])',
    ), 
    'endkey' => 'reply_time',
    'convert' => 'GBK',
);
$task[] = array(
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
$task[] = array(
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
$task[] = array(
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
$task[] = array(
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
/*
$task[] = array(
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

$task[] = array(
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
*/
$task[] = array(
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
date_default_timezone_set("Asia/Shanghai");
$cursor = @file_get_contents("cursor/task.txt");
if (empty($cursor)) {
    $cursor = 0;
} else {
    $cursor = $cursor % count($task);
}
$a_task = serialize($task[$cursor]);
echo $a_task;
flush();
$time = date('Y-m-d h:i:s');

file_put_contents("log/task.log", "$time : {$_SERVER['REMOTE_ADDR']} : {$task[$cursor]['site']}\n", FILE_APPEND);
file_put_contents("cursor/task.txt", ++$cursor);
clearstatcache();
?>
