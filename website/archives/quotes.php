<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
$data = json_decode(file_get_contents('php://input'),1);
$category_table = "ahke_category.quotes";
if(isset($_GET["id"])){
  $post_id = local_filter_input($_GET["id"]);
  $get_post_sql = "SELECT a.*,b.username,b.name,b.profile_image
                   FROM {$category_table} a 
                   INNER JOIN ahke_user.user b on a.user_id = b.id
                   WHERE a.id = {$post_id} AND a.status = 1 ";
  $get_post = $conn->query($get_post_sql);
  $post = $get_post->fetch(PDO::FETCH_ASSOC);
  
  $post_dom = '
      <div class="item" id="1-'.$post["id"].'">
            <div class="header-item">
                <ul>
                    <li style="display: flex;"><img class="user-image" src="'.$api_host.'/media/'.$post["profile_image"].'"></li>
                    <li class="btn-acount" onclick="account_info('.$post["id"].')">
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
                <li onclick="notify('.$post["id"].',0,1)">
                    <span class="span-text">إبلاغ</span> &nbsp;
                    <span class="material-icons icon">flag_circle</span>
                </li>
              
            </ul>
        
            <div class="item-content " style="opacity:1">
                <div class="item-text" style="font-size:16px;" onclick="show_full_text(this,1,'.$post["id"].')">'.$post["post_text"].'</div>
            </div>

            
	        <ul class="footer-item">
		        <li onclick="share('.$post["id"].',1)">
		            <span class="material-icons icon icon-footer">share</span>
		        </li>
		        <li onclick="save(this)"><span class="material-icons icon icon-footer">bookmark_border</span></li>
		        <li onclick="download(this)"><span class="material-icons icon icon-footer">save_alt</span></li>
		        <li onclick="copy(this)"><span class="material-icons icon icon-footer">copy_all</span></li>
		        <li onclick="like(this,'.$post["id"].',1,0)">
		            <span class="material-icons icon icon-footer">favorite_border</span>
		        </li>
		        <input id="item-id" type="hidden" value="'.$post["id"].'">
		        <input id="user-id" type="hidden" value="'.$post["user_id"].'">
		        <input id="category-num" type="hidden" value="1">
	        </ul>
          </div>
  ';
  // set data in file
  $added_date = $post["added_date"];
  $added_date = explode(" ", $added_date);
  $added_date = explode("-", $added_date[0]);
  $added_year = $added_date[0];
  $added_month = $added_date[1];
  $added_day = $added_date[2];
  if(!is_dir("../../../ahke.net/archives/quotes/".$added_year."/".$added_month."/".$added_day)){
	  if(!is_dir("../../../ahke.net/archives/quotes/")){
        mkdir("../../../ahke.net/archives/quotes/");
        chmod("../../../ahke.net/archives/quotes/",0777);
	  }

      if(!is_dir("../../../ahke.net/archives/quotes/".$added_year)){
        mkdir("../../../ahke.net/archives/quotes/".$added_year);
	    chmod("../../../ahke.net/archives/quotes/".$added_year,0777);
      }

      if(!is_dir("../../../ahke.net/archives/quotes/".$added_year."/".$added_month)){
        mkdir("../../../ahke.net/archives/quotes/".$added_year."/".$added_month);
	    chmod("../../../ahke.net/archives/quotes/".$added_year."/".$added_month,0777);
      }

      if(!is_dir("../../../ahke.net/archives/quotes/".$added_year."/".$added_month."/".$added_day)){
        mkdir("../../../ahke.net/archives/quotes/".$added_year."/".$added_month."/".$added_day);
	    chmod("../../../ahke.net/archives/quotes/".$added_year."/".$added_month."/".$added_day,0777);
      }
  }
  $file = fopen("../../../ahke.net/archives/quotes/".$added_year."/".$added_month."/".$added_day."/{$post_id}.html","w");
  chmod("../../../ahke.net/archives/quotes/".$added_year."/".$added_month."/".$added_day."/{$post_id}.html",0777);
  fwrite($file,$post_dom);
}
?>