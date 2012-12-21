<?php

class IndexController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('index', 'view', 'recive', 'clean', 'go', 'send'),
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    
    public function actionRecive()
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
        $sites = array();
        $websites = Website::model()->findAll();
        foreach ($websites as $website) {
            $sites[$website->site_id] = $website->site_cname;
        }
        foreach ($datas as $data) {
            if(strlen($data->title) <= 5) {
                continue;
            }
            $model=new Wuzeren;
            $model->gid = $data->gid;
            $model->href = $data->href;
            $model->title = $data->title;
            $model->author = $data->author;
            $model->action = $data->action;
            $model->thread_id = intval($data->thread_id);
            $model->reply_time = $data->reply_time;
            $model->site_id = intval($data->site_id);
            $model->site = $sites[$data->site_id];
            try {
    			if($model->save()) {
    			    echo "0";
    			} else {
    			    echo "1003";
    			}
            } catch (Exception $e) {
                if ($e->errorInfo[0] != 23000) {
                    var_dump($e->getMessage());
                }
                continue;
            }
			unset($model);
        }
        die();
    }
    
    public function actionView($id)
    {
        $wuzeren = $this->loadModel($id);
        $wuzeren->site_id;
        $wuzeren['href'] = trim($wuzeren['href']);
        if (strpos($wuzeren['href'], 'htt') ===0 ) {
            $url = "{$wuzeren['href']}";
        } else {
            $site = Website::model()->findByPk($wuzeren->site_id);
            $url = "{$site['site_url']}{$wuzeren['href']}";
        }
        header("Location: {$url}");
    }

    public function actionIndex()
    {
        $model=new Wuzeren('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Wuzeren'])) {
            $model->attributes=$_GET['Wuzeren'];
        }
        $this->render('index',array(
            'model'=>$model,
        ));
    }

    public function actionClean()
    {
        $date = date("Y-m-d H:i:s", time()-3*24*3600);
        Wuzeren::model()->findBySql("DELETE FROM `{{wuzeren}}` WHERE create_time < '{$date}';");
    }
    
    public function actionGo()
    {
        $time = date("Y-m-d H:i:s");
        $tasks = Spawn::model()->findAllBySql("SELECT id, search_key, email, last_time, next_time, create_time FROM `{{spawn}}` WHERE status=1 AND next_time <= '{$time}'");
        if (empty($tasks)) {
            return ;
        }
        $saved_content = array();
        foreach ($tasks as $key => $task) {
            if (empty($task['last_time'])) {
                $start_time = $task['create_time'];
            } else {
                $start_time = $task['last_time'];
            }
            if (empty($task['next_time'])) {
                $end_time = $time;
            } else {
                $end_time = $task['next_time'];
            }
            $key = md5($start_time . $end_time . $task['search_key']);
            if (isset($saved_content[$key])) {
                $content = $saved_content[$key]; 
            } else {
                $datas = Wuzeren::model()->findAllBySql("SELECT * FROM `{{wuzeren}}` WHERE title like '%{$task['search_key']}%' AND reply_time between '{$start_time}' AND '{$end_time}' ORDER BY reply_time DESC limit 50");
                if (empty($datas)) {
                    //没有符合条件的帖子
                    continue;
                }
                $content = '<table>';
                foreach ($datas as $data) {
                    $url = 'http://www.wuzeren.com/'.Yii::app()->createUrl("/index/view", array("id"=>$data["id"]));
                    $content .= "<tr><td>{$data['site']}</td><td><a href='{$url}'>{$data['title']}</a></td><td>{$data['author']}</td><td>{$data['reply_time']}</tr>";
                }
                $content .= '</table>';
                $saved_content[$key] = $content; 
            }
            $mail = new MailTask();
            $mail->send_to = $task['email'];
            $mail->title = "{$task['search_key']}|从{$start_time}到 {$end_time}|无责任收集";
            $mail->content = $content;
            $mail->create_time = date("Y-m-d H:i:s");
            $mail->save();
            $spawn = Spawn::model()->findByPk($task['id']);
            $spawn->last_time = $end_time;
            $spawn->next_time = date('Y-m-d H:i:s', time() + $task['rate'] * 60);
            $spawn->save();
        }
    }
    
    public function actionSend()
    {
        $send_mails = MailTask::model()->findAllBySql("SELECT * FROM `{{mail_task}}` WHERE retry_time<5 AND is_success=0");
        foreach ($send_mails as $send_mail) {
            Yii::app()->mailer->AddAddress($send_mail['send_to']);
            Yii::app()->mailer->Subject = $send_mail['title'];
            Yii::app()->mailer->MsgHTML($send_mail['content']);
            try {
                Yii::app()->mailer->Send();
                $send_mail->is_success = 1;
            } catch (Exception $e) {
                $send_mail->retry_time = $send_mail->retry_time + 1;
                $send_mail->err_msg = $e->getCode().'|'.$e->getMessage();
            }
            $send_mail->send_time = date('Y-m-d H:i:s');
            $send_mail->save();
        }
    }
    
    public function actionSubscribe()
    {
        
        if (!Yii::app()->user->getId()) {
            $email = $_POST['email'];
            $user=new User;
            $user->username = $email;
            $user->salt = md5(time());
            $user->email = $email;
            $user->password = '';
            $user->save();
        } else {
            $email = Yii::app()->user->getName();
        }
        $spawn = new Spawn;
        $spawn->email = $email;
        $spawn->search_key = $_POST['search_key'];
        $spawn->rate = $_POST['rate'];
        $spawn->next_time = date("Y-m-d H:i:s", time()+$spawn->rate*60);
        $spawn->status = 1;
        $spawn->create_time = date("Y-m-d H:i:s");
        $spawn->save();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=Wuzeren::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='wuzeren-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
