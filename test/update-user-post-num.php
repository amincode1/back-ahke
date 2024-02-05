<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
include("../include/config.php");
include("../include/crypt.php");
$get_users_sql = "SELECT * FROM ahke_user.user ";
$get_users = $conn->query($get_users_sql);
while ($user = $get_users->fetch()) {
	$user_id = $user["id"];
	$get_user_do_you_know_num_sql = "SELECT COUNT(id) AS num FROM ahke_category.do_you_know WHERE user_id = {$user_id} ";
	$get_user_do_you_know_num = $conn->query($get_user_do_you_know_num_sql);
    $get_user_do_you_know_num = $get_user_do_you_know_num->fetch();
    $do_you_know_num = $get_user_do_you_know_num["num"];

    $get_user_from_book_num_sql = "SELECT COUNT(id) AS num FROM ahke_category.from_book WHERE user_id = {$user_id} ";
	$get_user_from_book_num = $conn->query($get_user_from_book_num_sql);
    $get_user_from_book_num = $get_user_from_book_num->fetch();
    $from_book_num = $get_user_from_book_num["num"];

    $get_user_images_num_sql = "SELECT COUNT(id) AS num FROM ahke_category.images WHERE user_id = {$user_id} ";
	$get_user_images_num = $conn->query($get_user_images_num_sql);
    $get_user_images_num = $get_user_images_num->fetch();
    $images_num = $get_user_images_num["num"];


    $get_user_questionnaire_num_sql = "SELECT COUNT(id) AS num FROM ahke_category.questionnaire WHERE user_id = {$user_id} ";
	$get_user_questionnaire_num = $conn->query($get_user_questionnaire_num_sql);
    $get_user_questionnaire_num = $get_user_questionnaire_num->fetch();
    $questionnaire_num = $get_user_questionnaire_num["num"];

    $get_user_quotes_num_sql = "SELECT COUNT(id) AS num FROM ahke_category.quotes WHERE user_id = {$user_id} ";
	$get_user_quotes_num = $conn->query($get_user_quotes_num_sql);
    $get_user_quotes_num = $get_user_quotes_num->fetch();
    $quotes_num = $get_user_quotes_num["num"];

    $get_user_recommendations_num_sql = "SELECT COUNT(id) AS num FROM ahke_category.recommendations WHERE user_id = {$user_id} ";
	  $get_user_recommendations_num = $conn->query($get_user_recommendations_num_sql);
    $get_user_recommendations_num = $get_user_recommendations_num->fetch();
    $recommendations_num = $get_user_recommendations_num["num"];

    $get_user_stories_num_sql = "SELECT COUNT(id) AS num FROM ahke_category.stories WHERE user_id = {$user_id} ";
    $get_user_stories_num = $conn->query($get_user_stories_num_sql);
    $get_user_stories_num = $get_user_stories_num->fetch();
    $stories_num = $get_user_stories_num["num"];

    $get_user_series_num_sql = "SELECT COUNT(id) AS num FROM ahke_category.series WHERE user_id = {$user_id} ";
    $get_user_series_num = $conn->query($get_user_stories_num_sql);
    $get_user_series_num = $get_user_series_num->fetch();
    $series_num = $get_user_series_num["num"];

    $update_user_num_sql = "UPDATE ahke_user.user_post_num SET 
                           do_you_know = {$do_you_know_num},
                           from_book = {$from_book_num},
                           images = {$images_num},
                           questionnaire = {$questionnaire_num},
                           quotes = {$quotes_num},
                           recommendations = {$recommendations_num},
                           stories = {$stories_num},
                           series = {$series_num},
                           all_category = do_you_know+from_book+images+questionnaire+quotes+recommendations+stories+series
                           WHERE user_id = {$user_id}
                           ";
    $update_user_num = $conn->exec($update_user_num_sql);

    // $insert_user_num_sql = "INSERT INTO ahke_user.user_post_num (
    //                        `do_you_know`,`from_book`,`images`,`questionnaire`,`quotes`,`recommendations`,`stories`,`all_category`,`user_id`) VALUES 
    //                        ($do_you_know_num,$from_book_num,$images_num,$questionnaire_num,$quotes_num,$recommendations_num,$stories_num,do_you_know+from_book+images+questionnaire+quotes+recommendations+stories,$user_id)";
    // $conn->exec($insert_user_num_sql);
}
?>