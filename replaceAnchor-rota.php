<?php

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

$otladka = 4;
/* Чтение инфы из файла или базы */
$mainarr = array();
$row = 0;
$file = "http://fanski.ru/noindex/rota.csv";
$handle = fopen($file, "r");
//echo 'File http://fanski.ru/noindex/ggl.csv - das not exist!';

if (!$handle) exit('File $file - das not exist!');
//$url = 'http://fanski.ru/ukraina/rafting-na-yuzhnom-buge';

while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	
    $num = count($data);
    for ($c=0; $c < $num; $c++) {
		$mainarr[$row][$c]=$data[$c];
    }
	//print_r($mainarr);
	$url = $mainarr[$row][0];	
	$url2  = $mainarr[$row][1];	
	$anchor  = $mainarr[$row][2];
	
	$postid = url_to_postid( $url );
	$post = get_post( $postid );
	$title = $post->post_title;
	$content = $post->post_content;
	$post_content = esc_html($content);
	
	$ncontent = replaceAnchor($content, $url2, $anchor);
	if ($ncontent === false) continue;
	$post->post_content = $ncontent;

	// Обновляем данные в БД
	$post_id = wp_update_post( $post );

    $row++;if ($row>$otladka) break;
}

if ($post_id == 0) {
   echo 'Something went wrong!!!';
} else { 
   echo "Post $post_id Success\n"; 
}//if ($post_id == 0) {

fclose($handle);
exit;


function replaceAnchor($content, $url2, $anchor){

    $substr = ' href="'. $url2 . '"';
    $pos = strpos($content, $substr);
    $rest = substr($content, $pos);//, 
    $anclen = strlen($anchor);
    $pos2 = strpos($rest, '</a>');
    $rest2 = substr($rest, 0, $pos2+4);
    $pos3 = strpos($rest2, '>');
    $anchor = substr($rest2, $pos3+1, $pos2-$pos3-1);
    
    if (strpos($anchor, 'ttp://') !== false) {
        echo " !!! 1 Anchor is http:// !!!";
        return false;
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

    /* Для title="'.$anchor */
    $substr = '<a title="'.$anchor.'" href="'. $url2 . '"';
    $pos0 = strpos($content, $substr);
    $anclen = strlen($anchor);
    if ($pos0 == 0) $pos -= $anclen + 10;
    else $pos = $pos0;
    $rest = substr($content, $pos);
    $i = 0;
    while (strpos($rest, '<a ') === false && $i < 10) {
        $i++;
        $pos--;
        $rest = substr($content, $pos);
    }

    $pos2 = strpos($rest, '</a>');
    $rest2 = substr($rest, 0, $pos2+4);
    $pos3 = strpos($rest2, '>');
    
    $replace = $url2;
    $newrest = str_replace($anchor, $replace, $rest2);
    $newrest2 = $anchor . " " . $newrest;
    $newcontent = str_replace($rest2, $newrest2, $content);

return $newcontent;
}


?>