<?php
include("../../../api-setting.php");
include("../../../include/config.php");
include("../../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$category_table = "ahke_category.quotes";
function natural_text($input_text) {
    $text = $input_text;
    $text = preg_replace('/\s0s0\s/', "'", $text);
    $text = preg_replace('/\s0d0\s/', '"', $text);
    return $text;
}
if(isset($_GET["id"])){
  $post_id = local_filter_input($_GET["id"]);
  $get_post_sql = "SELECT a.*,b.username,b.name,b.profile_image
                   FROM {$category_table} a 
                   INNER JOIN ahke_user.user b on a.user_id = b.id
                   WHERE a.id = {$post_id}";
  $get_post = $conn->query($get_post_sql);
  if($get_post->rowCount()){
     $post = $get_post->fetch(PDO::FETCH_ASSOC);
     $added_date = $post["added_date"];
     $added_date = explode(" ", $added_date);
     $added_date = explode("-", $added_date[0]);
     $added_year = $added_date[0];
     $added_month = $added_date[1];
     $added_day = $added_date[2];

     if($post["status"] == 1){
        $post_dom = '
             <div class="item" id="'.$post["category_num"].'-'.$post["id"].'">
                <div class="header-item">
                    <ul>
                        <li style="display: flex;"><img class="user-image" src="'.$api_host.'/media/'.$post["profile_image"].'"></li>
                        <li class="btn-acount" onclick="account_info(\''.$post["user_id"].'\')">
                          <div class="header-info-user">
                            <span id="user-name">'.$post["name"].'</span>
                            <span id="username">'.$post["username"].'</span>
                          </div>
                        </li>
                        <li class="category-name-of-item" onclick="quotes_link()" style="background : var(--active-option);">
                          <span>إقتباسات</span>
                          <span>&nbsp;</span>
                        </li>
                        <li class="item-more-options" onclick="toggle_item_options(this)">
                          <span class="material-icons icon">more_horiz</span>
                          <span>&nbsp;</span>
                        </li>
                    </ul>
                </div>
            
                <ul class="item-options">
                    <li onclick="notify('.$post["id"].',0,'.$post["category_num"].')">
                        <span class="span-text">إبلاغ</span> &nbsp;
                        <span class="material-icons icon">flag_circle</span>
                    </li>
                  
                </ul>
            
                <div class="item-content " style="opacity:1">
                    <div class="item-text" style="font-size:16px;" onclick="show_full_text(this,1,'.$post["id"].')">'.natural_text($post["post_text"]).'</div>
                </div>

                
              <ul class="footer-item">
                <li onclick="share('.$post["id"].','.$post["category_num"].')">
                    <span class="material-icons icon icon-footer">share</span>
                </li>
                <li id="li-save" onclick="save(this)"><span class="material-icons icon icon-footer">bookmark_border</span></li>
                <li onclick="download(this)"><span class="material-icons icon icon-footer">save_alt</span></li>
                <li onclick="copy(this)"><span class="material-icons icon icon-footer">copy_all</span></li>
                <li id="li-like" onclick="like(this,'.$post["id"].','.$post["category_num"].',0)">
                    <span class="material-icons icon icon-footer">favorite_border</span>
                </li>
                <input id="item-id" type="hidden" value="'.$post["id"].'">
                <input id="user-id" type="hidden" value="'.$post["user_id"].'">
                <input id="category-num" type="hidden" value="'.$post["category_num"].'">
              </ul>
              </div>
      ';   
      if(!is_dir("../../../../public_html/archives/quotes/".$added_year."/".$added_month."/".$added_day)){
        if(!is_dir("../../../../public_html/archives/quotes/")){
          mkdir("../../../../public_html/archives/quotes/");
          chmod("../../../../public_html/archives/quotes/",0777);
        }

        if(!is_dir("../../../../public_html/archives/quotes/".$added_year)){
          mkdir("../../../../public_html/archives/quotes/".$added_year);
          chmod("../../../../public_html/archives/quotes/".$added_year,0777);
        }

        if(!is_dir("../../../../public_html/archives/quotes/".$added_year."/".$added_month)){
          mkdir("../../../../public_html/archives/quotes/".$added_year."/".$added_month);
          chmod("../../../../public_html/archives/quotes/".$added_year."/".$added_month,0777);
        }

        if(!is_dir("../../../../public_html/archives/quotes/".$added_year."/".$added_month."/".$added_day)){
          mkdir("../../../../public_html/archives/quotes/".$added_year."/".$added_month."/".$added_day);
          chmod("../../../../public_html/archives/quotes/".$added_year."/".$added_month."/".$added_day,0777);
        }

      }
      $file = fopen("../../../../public_html/archives/quotes/".$added_year."/".$added_month."/".$added_day."/{$post_id}.html","w");
      chmod("../../../../public_html/archives/quotes/".$added_year."/".$added_month."/".$added_day."/{$post_id}.html",0777);
      fwrite($file,$post_dom);
     }else{
        $post_dom = '';
        unlink("../../../../public_html/archives/quotes/".$added_year."/".$added_month."/".$added_day."/{$post_id}.html");
     }
    // set data in file
  }
}
?>