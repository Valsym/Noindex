# Noindex Project<br>
This project was implemented Aug-2017<br>
My client web site WordPress (in exchanges for selling >400 SEO-links) failed under the filter Search engine Yandex.

# Task: 

It is necessary to remove these links anchor text to naked URL (without anchor link)<br> 
Закрытие ссылок тэгами <noindex></noindex> не представляет сложностей.<br>
Таким образом, главной задачей была переделка анкоров ссылок в безанкорные во всех статьях, размещенных на сайте. За все  время сущетвования сайта таких ссылок было размещено довольно большое количество и вручную переделывать анкоры заняло бы слишком долгое время.

To solve the problem was the script was written: replaceAnchor.php

Because the Site was written in Worpress, so to modify articles with SEO links used the same approach as in the draft:https://github.com/Valsym/parser-autoposter


The php script (see files replaceAnchor.php) was run on the hosting manually directly from your browser.<br> 
   *****************************************************************************************
   The scripts generate not just html code (this is pretty simple), but the pages in the Wordpress CMS format that was the main problem. However, as always, managed to solve it. <br>
   Therefore, first read the content of the articles, which were posted SEO links, and then altered the anchors and the anchor was placed outside the links right in front of her. The updated content overwritten Stored in the Database of the website.<br>
   *****************************************************************************************
   List of URLs of articles which have been placed SEO-links as well as the required links to the websites of SEO and anchor text in udobnom csv format supplied by the exchange GGL, Miralinks. 
   ************************************************************************************
<b>A few words about the algorithm.</b><br>
After reading the Man-Like-Url (Permalink as http://site.com/%postname%/) from the file ggl.cvs, for further work it is necessary to get the article ID (of the post):<br>
$postid = url_to_postid( $url );<br>

You can continue to read all the necessary info from the corresponding row of the database for WordPress ID:<br>
$post = get_post( $postid );<br>
Here $post is an associative array, in particular containing the body of the post $content = $post->post_content;

Next, it calls a function replaceAnchor():<br>
$ncontent = replaceAnchor($content, $url2); <br>
which actually changes the anchor without the anchor and puts the anchor on the outside of SEO-references.

And at the end is overwriting the content of the post:<br>
$post->post_content = $ncontent;<br>
$post_id = wp_update_post( $post );

Then move on to the next record in the main loop.
	
In function replaceAnchor I decided not to use regular expressions or DOM nodes for substring search<br> with anchor text and SEO-link, and used by more than sorry and clear (to me) search using<br> strpos, strripos, substr & str_replace.

In the case where the anchor originally was without the anchor: <br>
if (strpos($anchor, 'http://') !== false) {...},<br>
the content of the post was not changed and carried out the transition to the next step of the main loop.
	
*********************************************************************************************************	


