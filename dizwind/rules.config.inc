<?php
$listTask['101'] = array(
    'type' => 'list',
    'site' => 'diypda',
    'site_id' => '101',
    'site_url' => 'http://tzsc.diypda.com/',
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
        
$contentTask['101'] = array(
    'type' => 'content',
    'site_id' => '101',
    'site' => 'diypda',
    'href' => '',
    'gid' => '',
    'content' => array('text' =>"j('div[class=t_msgfont]', 'innertext')"),
    'convert' => 'GBK',
);
        
$listTask['102'] = array(
    'site' => 'maxpda',                  
    'site_id' => '102',   
    'site_url' => 'http://bbs.maxpda.com/',                 
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
        
$listTask['103'] = array(
    'site' => 'hiapk',                    
    'site_id' => '103',    
    'site_url' => 'http://bbs.maxpda.com/',                
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
        
$listTask['104'] = array(
    'site' => 'in189',                    
    'site_id' => '104',     
    'site_url' => 'http://www.in189.com/',               
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

$listTask['105'] = array(
    'site' => 'gfan',                 
    'site_id' => '105',     
    'site_url' => 'http://bbs.gfan.com/',               
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

$listTask['106'] = array(
    'site' => 'rayi',                 
    'site_id' => '106',   
    'site_url' => 'http://bbs.rayi.cn/',                 
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
        
$listTask['107'] = array(
    'site' => 'weiphone',                 
    'site_id' => '107',     
    'site_url' => 'http://bbs.weiphone.com/',               
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

$listTask['108'] = array(
    'site' => 'zoopda',                 
    'site_id' => '108',    
    'site_url' => 'http://bbs.zoopda.com/',                
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
        
$listTask['109'] = array(
    'site' => 'weimei',                 
    'site_id' => '109',
    'site_url' => 'http://f1.avzcy.info/bbs/',
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
        
$contentTask['109'] = array(
    'type' => 'content',
    'site_id' => '109',
    'site' => 'diypda',
    'href' => '',
    'gid' => '',
    'content' => array('text' =>"j('div[class=t_msgfont]', 'innertext')"),
    'convert' => 'GBK',
);
        
$listTask['110'] = array(
    'site' => 'oabt',
    'site_id' => '110',
    'site_url' => 'http://oabt.org/',
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

$listTask['111'] = array(
    'site' => '1lou',
    'site_id' => '111',
    'site_url' => 'http://bbs.1lou.com/',
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
        
$contentTask['111'] = array(
    'type' => 'content',
    'site_id' => '111',
    'site' => '1lou',
    'cleanrequest' => true,
    'href' => '',
    'gid' => '',
    'content' => array('text' =>"j('td[class=t_f]', 'innertext')"),
    'convert' => 'GBK',
);

$listTask['112'] = array(
    'site' => 'zhishu',                 
    'site_id' => '112',
    'site_url' => 'http://top.baidu.com/category/',
    'hrefs' => array(
        '349' => "http://top.baidu.com/buzz?b=349",
        '350' => "http://top.baidu.com/buzz?b=350",
        '351' => "http://top.baidu.com/buzz?b=351",
        '448' => "http://top.baidu.com/buzz?b=448",
        '452' => "http://top.baidu.com/buzz?b=452",
        '453' => "http://top.baidu.com/buzz?b=453",
        '439' => "http://top.baidu.com/buzz?b=439",
        '440' => "http://top.baidu.com/buzz?b=440",
        '441' => "http://top.baidu.com/buzz?b=441",
        '368' => "http://top.baidu.com/buzz?b=368",
        '369' => "http://top.baidu.com/buzz?b=369",
        '369' => "http://top.baidu.com/buzz?b=442",
        '369' => "http://top.baidu.com/buzz?b=443",
        '369' => "http://top.baidu.com/buzz?b=444",
        '369' => "http://top.baidu.com/buzz?b=445",
        '369' => "http://top.baidu.com/buzz?b=446",
        '369' => "http://top.baidu.com/buzz?b=447",
    ), 
    'path' => "table[class=list-table] tr",
    'cleanrequest' => true,
    'nocache' => true,
    'list' => array(
        'href' => "j('a[class=list-title]', 'href')",
        'title' => "j('a[class=list-title]', 'innertext')",
        'total' => "j('span', 'innertext', -1)",
        'numtop' => "j('span', 'innertext', 0)",
        'thread_id' => 's($item["numtop"], $item["total"])',
        'gid' => 's($this->task["site_id"], "-", round((microtime(true)*10000)))',
        'site_id' => 's($this->task["site_id"])',
        'site' => 's($this->task["site"])',
    ),
    //'endkey' => 'reply_time',
    'convert' => 'GBK',
    'beforeSave' => "saveBaiduZhishu"
);

function saveBaiduZhishu($param)
{
    $param['title'] = str_replace('第', ' 第', $param['title']);
    $param['title'] = str_replace('  第', ' 第', $param['title']);
    $param['title'] = str_replace('   第', ' 第', $param['title']);
    $param['title'] = str_replace('    第', ' 第', $param['title']);
    $param['title'] = str_replace('     第', ' 第', $param['title']);
    $param['title'] = str_replace('      第', ' 第', $param['title']);
    $param['title'] = str_replace('       第', ' 第', $param['title']);
    $param['title'] = str_replace('        第', ' 第', $param['title']);
    return $param;
}

$maxPromoDay = 15;
$dbPromoDays = array();
for ($i = 0; $i <= $maxPromoDay; $i++) {
    $day = date('Ymd', time()+$i*24*3600);
    $dbPromoDays[$day] = "http://movie.douban.com/tv/calendar/{$day}/";
}

$listTask['113'] = array(
    'site'     => 'douban',                 
    'site_id'  => '113',
    'site_url' => 'http://movie.douban.com/tv/calendar/',
    'hrefs'    => $dbPromoDays,
    'path'     => "table[class=series_list] tbody tr",
    'cleanrequest' => true,
    'nocache'  => true,
    'list'     => array(
        'href' => "j('a', 'href', 0)",
        'title' => "j('a', 'innertext', 0)",
        'serie_title' => "j('td[class=gray]', 'innertext')",
        'thread_id' => 'r("/\/([0-9]+)\//", $item["href"])', 
        'gid' => 's($this->task["site_id"], "-", round((microtime(true)*10000)))',
        'site_id' => 's($this->task["site_id"])',
        'site' => 's($this->task["site"])',
    ),
    //'endkey' => 'reply_time',
    //'convert' => 'GBK',
    'beforeSave' => "saveDoubanPromo"
);

function saveDoubanPromo($param)
{
    $param['title'] = str_replace('第', ' 第', $param['title']);
    $param['title'] = str_replace('  第', ' 第', $param['title']);
    $param['title'] = str_replace('   第', ' 第', $param['title']);
    $param['title'] = str_replace('    第', ' 第', $param['title']);
    $param['title'] = str_replace('     第', ' 第', $param['title']);
    $param['title'] = str_replace('      第', ' 第', $param['title']);
    $param['title'] = str_replace('       第', ' 第', $param['title']);
    $param['title'] = str_replace('        第', ' 第', $param['title']);
    $param['title'] = str_replace('         第', ' 第', $param['title']);
    return $param;
}



$channels = array('cctv1', 'cctv4', 'cctv8', 'cctv11', 'cctv14', 'anhui', 'btv1', 'chongqing', 'shanghai', 'dongnan', 'guangdong', 'guangxi', 'gansu', 'guizhou', 'hebei', 'henan', 'heilongjiang', 'hubei', 'hunan', 'jilin', 'jiangsu', 'jiangxi', 'liaoning', 'luyou', 'neimenggu', 'ningxia', 'qinghai', 'shandong', 'shandongjiaoyu', 'shenzhenweishi', 'shanxi2', 'shanxi1', 'sichuan', 'tianjin', 'xizang', 'xiamenweishi', 'xinjiang', 'yanbiantv', 'yunnan', 'zhejiang', 'btv4', 'tianjin1', 'guangxiz', 'xinjiang');
$maxPromoDay = 6;

$channels = array('cctv1');
$maxPromoDay = 1;

$cntvPromoDays = array();
for ($i = 0; $i <= $maxPromoDay; $i++) {
    $day = date('Y-m-d', time() + $i*24*3600);
    foreach ($channels as $channel) {
        $cntvPromoDays[$day][$channel] = "http://tv.cntv.cn/index.php?action=epg-list&date={$day}&channel={$channel}";
    }
}

$listTask['114'] = array(
    'site'     => 'cntv',                 
    'site_id'  => '114',
    'site_url' => 'http://tv.cntv.cn/epg/',
    'hrefs' => $cntvPromoDays,
    'path' => "div[class=content_c] dl dd",
    'compress' => 'compress.zlib://',
    'cleanrequest' => false, 
    'nocache' => true,
    'list' => array(
        'href' => array("j('a', 'href', -1)", ""),
        'title' => array("j('a', 'innertext', -1)", "j('', 'innertext')"),
        'thread_id' => 's(round((microtime(true)*10000)))', 
        'gid' => 's($this->task["site_id"], "-", $item["thread_id"])',
        'site_id' => 's($this->task["site_id"])',
        'site' => 's($this->task["site"])',
    ),
    'host' => '	tv.cntv.cn',
    //'endkey' => 'reply_time',
    //'convert' => 'GBK',
    'beforeSave' => "saveCntv"
);

function saveCntv($param)
{
    if (strpos($param['title'], '电视剧') === false) {
        return array();
    }
    $string = $param['title'];
    preg_match("/([0-9:]+) (.*)：(.*)/", $string, $tmp);
    if (isset($tmp[1]) && isset($tmp[3])) {
        $param['title'] = $tmp[3];
        $param['forum'] = $param['forum'] . " " .$tmp[1];
    } else {
        return array();
    }
    return $param;
}
?>