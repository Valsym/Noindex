<?php
/* Привязка к админке сайта */
define('WP_USE_THEMES', true);
$ABSPATH = dirname(dirname(__FILE__));
$site="http://fanski.ru";
if (file_exists("http://fanski.ru/wp-blog-header.php"))
           require 'http://fanski.ru/wp-blog-header.php';

$wpl="http://fanski.ru/wp-includes/post.php";
if (file_exists($wpl))
          require_once( $wpl);
require(dirname(dirname(__FILE__)) . '/wp-load.php');

$post = array();
$rows = 11;
/* Чтение инфы из файла .cvs */
$mainarr = array();
$row = 0;
$file = "http://fanski.ru/noindex/ggl.csv";
$handle = fopen($file, "r");

if (!$handle) exit('File $file - not exist!');

/* Главный цикл */

while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	
    $num = count($data);

    for ($c=0; $c < $num; $c++) {
		$mainarr[$row][$c]=$data[$c];
    }

	$url = $mainarr[$row][0];	
	$url2  = $mainarr[$row][1];	
	$anchor  = $mainarr[$row][2];

	
	$postid = url_to_postid( $url );
	$post = get_post( $postid );
	$title = $post->post_title;
	$content = $post->post_content;
	$post_content = esc_html($content);
	
	
	$ncontent = replaceAnchor($content, $url2);
	if ($ncontent === false) continue;

	$post->post_content = $ncontent;

	// Обновляем данные в БД
	$post_id = wp_update_post( $post );
	
    $row++;if ($row > $rows) break;
}

if ($post_id == 0) {
   echo 'Something went wrong!!!';
} else { 
   echo "Post $post_id Success\n"; 
}//if ($post_id == 0) {

fclose($handle);
exit;


function replaceAnchor($content, $url2){
    
    $substr = '<a href="'. $url2 . '"';
    $pos = strpos($content, $substr);
    $rest = substr($content, $pos);
    $pos2 = strpos($rest, '</a>');
    
    $rest2 = substr($rest, 0, $pos2+4);
    $pos3 = strpos($rest2, '>');
    $anchor = substr($rest2, $pos3+1, $pos2-$pos3-1);
    strpos($rest2, 'ttp://');
    if (strpos($anchor, 'http://') !== false) {
        $pos = strripos($content, $substr);
        $rest = substr($content, $pos);
        $pos2 = strpos($rest, '</a>');
        
        $rest2 = substr($rest, 0, $pos2+4);
        $pos3 = strpos($rest2, '>');
        $anchor = substr($rest2, $pos3+1, $pos2-$pos3-1);
        if (strpos($anchor, 'ttp://') !== false) {
            echo " !!! 2 Anchor is http:// !!!";
            return false;
        }
    }
    
    $replace = $url2;
    $newrest = str_replace($anchor, $replace, $rest2);
    $newrest2 = $anchor . " " . $newrest;
    $newcontent = str_replace($rest2, $newrest2, $content);

return $newcontent;
}


?>