<?php
class UserInfo
{
    public $conn;
    public $user_table = "ahke_user.user";
    public $user_post_num_table = "ahke_user.user_post_num";
    public $user_stats_table = "ahke_user.user_stats";
    public $user_setting_table = "ahke_user.user_setting";
    public $hidden_column = ['password','being_followed_id','unico_id','status'];
    public $user_info = [];
    public $user_post = [];

    public function __construct($params)
    {
        $this->conn = $params["conn"];
        $this->user_id = $params["user_id"];
        $this->username = $params["username"];

        if(!empty($this->user_id)){
            $this->getUserInfoById();
            $this->getUserPostId();
        }else{
        	$this->getUserInfoByUsername();
            $this->getUserPostId();
        }

        echo json_encode(array_merge($this->user_info,$this->user_post));
    }

    public function getUserInfoById()
    {
        $get_user_info_sql = "SELECT a.*,c.last_post_date,d.status_email FROM {$this->user_table} a
                              INNER JOIN {$this->user_stats_table} c on c.user_id = a.id
                              INNER JOIN {$this->user_setting_table} d on d.user_id = a.id
                              WHERE a.id = {$this->user_id}";
        $get_user_info = $this->conn->query($get_user_info_sql);
        $get_user_info = $get_user_info->fetchAll(PDO::FETCH_ASSOC);
        $get_user_info = $this->remove_column($get_user_info,$this->hidden_column);
        $this->user_info = ["info" => $get_user_info[0]];
    }

    public function getUserInfoByUsername()
    {
        $get_user_info_sql = "SELECT a.*,c.last_post_date,d.status_email FROM {$this->user_table} a
                              INNER JOIN {$this->user_stats_table} c on c.user_id = a.id
                              INNER JOIN {$this->user_setting_table} d on d.user_id = a.id
                              WHERE a.username = '{$this->username}'";
        $get_user_info = $this->conn->query($get_user_info_sql);
        $get_user_info = $get_user_info->fetchAll(PDO::FETCH_ASSOC);
        $this->user_id = $get_user_info[0]['id'];
        $get_user_info = $this->remove_column($get_user_info,$this->hidden_column);
        $this->user_info = ["info" => $get_user_info[0]];
    }

    public function getUserPostId(){
       $post_id = file_get_contents("../../json-database/user/@{$this->user_id}/post-id.txt");
       $this->user_post = ["post_id" => $post_id];
    }

    public function remove_column($result,$columns){
       foreach($columns as $column){
          $result[0][$column] = null;
       }
       return $result;
    }
}
?>