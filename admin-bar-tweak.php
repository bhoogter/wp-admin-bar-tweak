<?php
/*
Plugin Name: Admin Bar Tweak
Version: 1.0.0
Plugin URI: http://www.churchofthebeyond.com/
Description: Shows more info on the Admin Bar
Author: Benjamin Hoogterp
Author URI: http://www.blazingglory.org/
Contributors: bhoogterp
*/

function adminbartweak_version() {return "1.0.0";}
function adminbartweak_private() {return "<img src='/wp-content/plugins/admin-bar-tweak/lock.png' width='16' height='16' alt='Private' title='Private'/>";}


function PostDump()
	{
	$recent = new WP_Query("showposts=2000&orderby=date&order=asc&post_status=any");
	$x = -1;
	while($recent->have_posts())
		{
		$recent->the_post();
		global $more;    // Declare global $more (before the loop).
		$more = 1;       // Set (inside the loop) to display all content, including text below more.		$id = $post->ID;

		if (in_category('12')) continue;		// skip videos
		if (get_post_status( get_the_ID() ) == "draft") continue;

//print "<h2>".$x." - ".get_post_status( get_the_ID() )."</h2>";

		$x++;

		$a = 0;
		$b = 0;
		$n = $_REQUEST["POST-DUMP-2275"];
		if ($n>=1) {$a = $b; $b = 52;}
		if ($n>=2) {$a = $b; $b = 100;}
		if ($n>=3) {$a = $b; $b = 159;}
		if ($n>=4) {$a = $b; $b = 199;}
		if ($n>=5) {$a = $b; $b = 209;}
		if ($n>=6) {$a = $b; $b = 300;}
		if ($n>=7) {$a = $b; $b = 350;}
		if ($n>=8) {$a = $b; $b = 400;}
		if ($n>=9) {$a = $b; $b = 450;}
		if ($n>=10) {$a = $b; $b = 999;}


		if ($x <= $a) continue;
		if ($x > $b) die();
		$t = $x - $a;

		print "<p align='center'><span style='font: bold 24px/24px Garamond'>$t</span></p>\n";
		print "<p align='center'><span style='font: bold 36px/36px Garamond'>".get_the_title()."</span></p>\n";

		$C = get_the_content();
		$C = str_replace(array("�","�","�","�","�"),array("","\"","","",""), $C);		// Kill bad characters

		$C = str_replace("&nbsp;", " ", $C);							// replace HTML entity
		$C = str_replace("  "," ",$C);
		$C = str_replace("  "," ",$C);
		$C = str_replace("  "," ",$C);
		$C = str_replace("  "," ",$C);
		while (strpos($C, "  ")) $C = str_replace("  "," ",$C);					// No double spaces ever for books

		$C = str_replace("-150x150","",$C);							// Modify Image Displays
		$C = str_replace('height="150"',"",$C);
//		$C = str_replace("<img", "<img align='left'", $C);					// Left align and Grayscale images 
		$C = str_replace("src=\"", "src=\"http://www.churchofthebeyond.com/wp-content/plugins/admin-bar-tweak/bnw.php?i=", $C);

		$a = strpos($C, "<a");									// Remove or mutilate hyperlinks
		$b = strpos($C, ">", $a);
		$C = str_replace(substr($C, $a, $b-$a+1), "", $C);
		$C = str_replace("<a ", "", $C);
		$C = str_replace("</a>", "", $C);

//		$C = preg_replace_callback('$[<]blockquote[>](.*)[<]/blockquote[>]$', "DoBQ", $C);

//		$C = str_replace(array("<blockquote>","</blockquote>"), array("<blockquote><span style='font-size:11; font-face: Calibri;text-align:justify;'>","</span></blockquote>"), $C);

		$C = str_replace("\r","",$C);								// Paragraphs
		$C = str_replace("\n\n", "\n</span></p><p><span  style=\"font-size:12;line-height:18px; font-face: 'Times New Roman';text-align:justify;\">\n", $C);
//		$C = str_replace("\n\n", "\n<br>\n", $C);


		$C .= "<p align='center'><img style='width:2.5in;height:.25in' src='http://www.churchofthebeyond.com/wp-content/plugins/admin-bar-tweak/HR3.png'/></p>\n";

		print "<div><p><span style=\"font-size:12;line-height:18px; font-face: 'Times New Roman';text-align:justify;\">".$C."</span></p></div>";

		print "<br/>\n";
		
		}
	die();
	}

function DoBQ($s)
	{
	$s = str_replace("<blockquote>", "<blockquote><p><span style='font-size:11; font-face: Calibri;text-align:justify;'>", $s);
	$s = str_replace("</blockquote>", "</span></p></blockquote><p><span style=\"font-size:12;line-height:18px; font-face: 'Times New Roman';text-align:justify;\">", $s);
	$s = str_replace("\r","",$s);
	$s = str_replace("\n\n", "\n</span></p>\n", $s);

	return "</span></p>" . $s . "<p><span style='font-size:11; font-face: Calibri;text-align:justify;'>";
	}


function get_pages_with_parent_sort($x, $y){return $x->menu_order-$y->menu_order;}
function get_pages_with_parent($pid)
	{
//print "<br/>get_pages_with_parent($pid)";
	if (!($p = get_pages( array( 'post_status'=>'publish,private' )))) return false;

//if ($pid==702){print "<br/>rrrrrrrr";$q = $p;foreach($q as $qq) unset($qq->post_content);print_r($q);}

	$a = array();
	foreach($p as $page)	if ($page->post_parent == $pid) $a[] = $page;

	if (count($a)==0) return false;

	usort($a, "get_pages_with_parent_sort");

//if ($pid==702){print "<br/>rrrrrrrr";$q = $p;foreach($q as $qq) unset($qq->post_content);print_r($q);}

	return $a;
	}

function pages_menu_tree($id = 0)
	{
//print "<br/>pages_menu_tree($id, $parent_id)\n";
	global $wp_admin_bar;

	if ($id==0)
		{
		$title = 'Pages';
		$menu = 'adminbar_pages';
		$href = FALSE;
		$private = '';
		$parent = false;
		}
	else
		{
		$P = get_page($id);
		$parent = ($P->post_parent == 0) ? 'adminbar_pages' : 'adminbar_pages_'.$P->post_parent;
		$menu = 'adminbar_pages_'.$id;
		$title = substr($P->post_title,0,20);
		$href = get_page_link( $P->ID );
		$private = $P->post_status!='private'? '' : adminbartweak_private(). ' ';

//print "<br/>id=$id, parent=$P->parent, menu=$menu, title=$title\n";
		}

	$num = 0;
	if ($id&&$P->menu_order) $num = "(".$P->menu_order. ") ";
	$wp_admin_bar->add_menu( array(	'id' => $menu, 'title' => __( $num . $private . $title ), 'href' => $href, 'parent' => $parent ) );

	if (!($x = get_pages_with_parent($id))) return false;
	foreach($x as $child)
		if ($child->ID != 0)
			pages_menu_tree($child->ID);

	}			



function adminbartweak_addlinks()
	{
	global $wp_admin_bar;
	if (!is_admin_bar_showing()) return;
	if ( !is_super_admin()) return;

	pages_menu_tree();

	$p = get_posts( array( 'post_status'=>'draft', 'numberposts'=>'20') );
	if (!!$p) 
		{
		$wp_admin_bar->add_menu( array(	'id' => 'adminbar_drafts',		'title' => __( 'Drafts'),	'href' => FALSE ) );
		foreach ($p as $post)
			$wp_admin_bar->add_menu( array(	'parent' => 'adminbar_drafts',	'title' => __( $post->post_title ),	'href' => '/wp-admin/post.php?post='.$post->ID.'&action=edit' ));
		}

	$p = get_posts( array( 'post_status'=>'future', 'numberposts'=>'20') );
	if (!!$p) 
		{
		$wp_admin_bar->add_menu( array(	'id' => 'adminbar_scheduled',		'title' => __( 'Scheduled'),	'href' => FALSE ) );
		foreach ($p as $post)
			{
			$td = date("m/d",strtotime($post->post_date));
			$t = trim($td . " " .__( $post->post_title ));
			$wp_admin_bar->add_menu( array(	'parent' => 'adminbar_scheduled',	'title' => $t,	'href' => '/?p='.$post->ID ));
//			$wp_admin_bar->add_menu( array(	'parent' => 'adminbar_scheduled',	'title' => '<a href="'.'/wp-admin/post.php?post='.$post->ID.'&action=edit'.'">[]</a> '.$t,	'href' => '/?p='.$post->ID ));
			}
		}


	}

function add_spam_can()
	{
	$i = "/wp-content/plugins/admin-bar-tweak/delete-icon.png";		//  Should be path to some nice icon.. also adjust size below

	$n = wp_create_nonce('bulk-comments');

	$x = "";
	$x .= "/wp-admin/edit-comments.php?comment_status=spam"; 		//  Should get directory from WP settings...

	$x .= "&delete_all=Empty%20Spam";
	$x .= "&_wpnonce=$n";
	$x .= "&_wp_http_referer=/wp-admin/";
	$x .= "&pagegen_timestamp=" . date("Y-m-d H:i:s");

	$s = "";
	$s .=  "<div style='display:inline-block;position:absolute;top:82px;left:68px;'>";
	$s .= "<a href='$x'>";
	$s .= "<img src='$i' width='24' height='24' alt='Empty Spam' title='Empty Spam' />\n";
	$s .= "</a>";
	$s .=  "</div>";
	echo $s;
	}
add_action('right_now_table_end', 'add_spam_can',10);


add_action('admin_bar_menu', 'adminbartweak_addlinks', 40);

if (@$_REQUEST["POST-DUMP-2275"]) add_action("init", "PostDump");

