<?php
$reciver = new Reciver();
$reciver->recive();

class Reciver
{
    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'test';
        $username = 'root';
        $password = '';
        $options = 'localhost';
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
        );
    }
    
    public function recive()
    {
        if (!isset($_POST['data'])) {
            echo 1001;
            die();
        }
        $datas = json_decode($_POST['data']);
        if (!is_array($datas)) {
            echo 1002;
            die();
        }
        $datas[] = array();
        foreach ($datas as $data) {
            
            if(!isset($data->title) || strlen($data->title) <= 5) {
                continue;
            }
            
            $param['gid'] = isset($data->gid) ? $data->gid : '';
            $param['href'] = isset($data->href) ? $data->href : '';
            $param['title'] = isset($data->title) ? $data->title : '';
            $param['author'] = isset($data->author) ? $data->author : '';
            $param['action'] = isset($data->action) ? $data->action : '';
            $param['thread_id'] = isset($data->thread_id) ? intval($data->thread_id) : '';
            $param['reply_time'] = isset($data->reply_time) ? $data->reply_time : '';
            $param['site_id'] = isset($data->site_id) ? intval($data->site_id) : '';
            $param['site'] = isset($data->site_id) ? $this->sites[$data->site_id] : '';
            
    		if($this->insert($param)) {
    			    echo "0";
    		} else {
    			    echo "1003";
    		}
			unset($param);
        }
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
        $sql = "INSERT INTO `dizwind`({$keys_str}) VALUES({$values_str})";
        $stm = $this->db->prepare($sql);
        $result = false;
        if( $stm && $stm->execute(array_values($params))) {
            $result = $stm->rowCount();
        }
        return $result; 
    }
}
