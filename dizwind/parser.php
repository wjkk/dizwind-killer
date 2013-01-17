<?php
date_default_timezone_set("Asia/Shanghai");
ini_set('xdebug.var_display_max_children', 128000 );//xdebug.var_display_max_children Type: integer, Default value: 128
ini_set('xdebug.var_display_max_data', 512000 );//Type: integer, Default value: 512
ini_set('xdebug.var_display_max_depth', 3000);//Type: integer, Default value: 3

$Parser = new Parser();
if ($_GET['a'] == 'get') {
    $Parser->get();
} else if ($_GET['a'] == 'recive') {
    $Parser->recive();
}

class Parser
{
    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'dizwind';
        $username = 'root';
        $password = 'lianshan3';
        $this->db = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
    }
    
    public function get()
    {
        $query = $this->db->prepare("SELECT `gid`, `content` FROM `dizwind` WHERE site_id='111' AND `status`=4  AND retry_time < 4 ORDER BY RAND() DESC limit 1");
        $query->execute();
        $thread = $query->fetch(); 
        $task['gid'] = $thread['gid'];
        $task['content'] = $thread['content'];
        echo serialize($task);
        flush();
        $time = date('Y-m-d h:i:s');
        file_put_contents("log/parser.log", "$time : {$_SERVER['REMOTE_ADDR']} : {$task['gid']}\r\n", FILE_APPEND);
        clearstatcache();
    }
    
    public function recive()
    {
        if (!isset($_POST['data'])) {
            echo '1001';
            die();
        }
        $data = json_decode($_POST['data']);
        if (empty($data)) {
            echo '1002';
            die();
        }
        echo $data->gid;
        $flag = 1;
        if (isset($data->pics) && !empty($data->pics)) {
            $flag = 0;
            foreach ($data->pics as $pic) {
                $param = array();
                $param['gid'] = $data->gid;
                $param['image'] = $pic->new;
                $param['original'] = $pic->old;
                $this->insert($param);
    		unset($param);
            }
        }
        
        $this->updateThreadContent($data->gid, $flag);

        die();
    }
    
    public function insert($params) {
        $keys = array_keys($params);
        $keys_str = implode(',', $keys);
        $count = count($keys);
        $values = array();
        while($count--) {
            $values[] = '?';
        }
        $values_str = implode(',', $values);
        $sql = "INSERT INTO `pics`({$keys_str}) VALUES({$values_str})";
        $stm = $this->db->prepare($sql);
        $result = false;
        if( $stm && $stm->execute(array_values($params))) {
            $result = $stm->rowCount();
        }
        return $result; 
    }
    
    private function updateThreadContent($gid, $flag)
    { 
        if ($flag == '1') {
            $sql = "UPDATE `dizwind` SET `status`={$flag}, retry_time = retry_time + 1 WHERE `gid`=?";
	} else {
	    $sql = "UPDATE `dizwind` SET `status`={$flag} WHERE `gid`=?";
	}
        
        $stm = $this->db->prepare($sql);
        $result = false;
        if( $stm && $stm->execute(array($gid))) {
            $result = $stm->rowCount();
        }
    }
    
}
