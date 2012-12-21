<?php
ini_set('xdebug.var_display_max_children', 128000 );//xdebug.var_display_max_children Type: integer, Default value: 128
ini_set('xdebug.var_display_max_data', 512000 );//Type: integer, Default value: 512
ini_set('xdebug.var_display_max_depth', 3000);//Type: integer, Default value: 3

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
            '109' => 'weimei',
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
        );
    }
    
    public function recive()
    {
        if (!isset($_POST['data'])) {
            echo '1001';
            die();
        }
        $datas = json_decode($_POST['data']);
        if (empty($datas)) {
            echo '1002';
            die();
        }
        
        $urls = array();
        if (isset($datas->type) && $datas->type == 'content') {
            if($this->updateThreadContent($datas->gid, $datas->data->text)) {
                echo "0";
            } else {
                echo "1005";
            }
            die();
        }
        
        foreach ($datas->data as $data) {
            //if(!isset($data->title) || strlen($data->title) <= 3) {
            //    continue;
            //}
            
            $param['gid'] = isset($data->gid) ? $data->gid : '';
            $param['href'] = isset($data->href) ? $data->href : '';
            $param['title'] = isset($data->title) ? $data->title : '';
            $param['author'] = isset($data->author) ? $data->author : '';
            $param['action'] = isset($data->action) ? $data->action : '';
            $param['thread_id'] = isset($data->thread_id) ? intval($data->thread_id) : '';
            $param['reply_time'] = isset($data->reply_time) ? $data->reply_time : '';
            $param['site_id'] = isset($data->site_id) ? intval($data->site_id) : '';
            $param['site'] = isset($data->site_id) ? $this->sites[$data->site_id] : '';
            if (strpos($param['href'], 'htt') !==0 ) {
                $param['href'] = "{$this->urls[$param['site_id']]}{$param['href']}";
            }
            
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
    
    private function updateThreadContent($gid, $content)
    {
        $sql = "UPDATE `dizwind` SET `content`=?, `status`=0 WHERE `gid`=?";
        $stm = $this->db->prepare($sql);
        $result = false;
        if( $stm && $stm->execute(array($content, $gid))) {
            $result = $stm->rowCount();
        }
    }
    
}
