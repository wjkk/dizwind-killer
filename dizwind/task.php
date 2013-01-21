<?php 
date_default_timezone_set("Asia/Shanghai");

$task = new Task();
$task->task();

class Task
{
    public $contentRate = 0;
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
        $listTask = array_values($this->listTask);
        $task = $listTask[$cursor];
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
        require_once("config.inc");
        $this->db = new PDO("mysql:host={$db['host']};dbname={$db['dbname']}", $db['username'], $db['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        require_once("rules.config.inc");
        $this->listTask = isset($listTask) ? $listTask : array();
        $this->contentTask = isset($contentTask) ? $contentTask : array();
    }
}
?>
