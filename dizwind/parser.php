<?php
date_default_timezone_set("Asia/Shanghai");
ini_set('xdebug.var_display_max_children', 128000 );//xdebug.var_display_max_children Type: integer, Default value: 128
ini_set('xdebug.var_display_max_data', 512000 );//Type: integer, Default value: 512
ini_set('xdebug.var_display_max_depth', 3000);//Type: integer, Default value: 3

$Parser = new Parser();
$Parser->get();

class Reciver
{
    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'dizwind';
        $username = 'root';
        $password = 'lianshan3';
        $this->db = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        
        $this->sites = array(
            '101' => 'diypda',
            '102' => 'maxpda',
            '103' => 'hiapk',
            '104' => 'in189',
            '105' => 'gfan',
            '106' => 'rayi',
            '107' => 'weiphone',
            '108' => 'zoopda',
            '109' => 'weimei',
            '110' => 'oabt',
            '111' => '1lou',
        );
        $this->urls = array(
            '101' => 'diypda',
            '102' => 'maxpda',
            '103' => 'hiapk',
            '104' => 'in189',
            '105' => 'gfan',
            '106' => 'rayi',
            '107' => 'weiphone',
            '108' => 'zoopda',
            '109' => 'http://f1.avzcy.info/bbs/',
            '110' => 'http://oabt.org/',
            '111' => 'http://bbs.1lou.com/',
        );
    }
    
    public function get()
    {
        $query = $this->db->prepare("SELECT `gid`, content` FROM `dizwind` WHERE `status`=4 AND site_id=111 ORDER BY RAND() DESC limit 1");
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
        if (empty($datas)) {
            echo '1002';
            die();
        }
        
        foreach ($data['pics'] as $pic) {
            $param['gid'] = $data['gid'];
            $param['image'] = $pic;
            $this->insert($param);
			unset($param);
        }
        $this->updateThreadContent($data['gid']);
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
    
    private function updateThreadContent($gid)
    {
        $sql = "UPDATE `dizwind` SET `status`=0 WHERE `gid`=?";
        $stm = $this->db->prepare($sql);
        $result = false;
        if( $stm && $stm->execute(array($gid))) {
            $result = $stm->rowCount();
        }
    }
    
}
