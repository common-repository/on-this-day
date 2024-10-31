<?php
/*
Plugin Name: On This Day
Plugin URI: http://www.assembla.com/wiki/show/llbbsc/wpOTD
Description: On This Day plugin for WordPress
Version: 0.6.4
Author: Yu-Jie Lin
Author URI: http://www.livibetter.com/

Creation Date: 6/28/2007 4:36:42 UTC+8
*/
/*
 * Copyright 2007 Yu-Jie Lin
 * 
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option)
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

define('ON_THIS_DAY_DOMAIN', 'OnThisDay');
load_plugin_textdomain(ON_THIS_DAY_DOMAIN, 'wp-content/plugins/' . ON_THIS_DAY_DOMAIN . '/locale');

// Old method
function OTDList($targetPost=null) {
	global $post, $wp_query;
	$oldPost = $post;
	// If $targetPost presents, then use it;
	if ( $targetPost !== null ) $post = $targetPost;
	// If $targetPost not presents, check if not in loop, then set $post to null for querying using current date.
	elseif (!$wp_query->in_the_loop) $post = null;

	$options = get_option('OTDOptions');
		
	if (!$wp_query->in_the_loop) {
		// Outer loop (Widget)
		if ($options['widgetSinglePost']&&(is_single() || is_page()&&$options['includePages'])) {
			echo OTDGetListHTML($oldPost, $options['widgetBlock'], $options['widgetItem'], $options['widgetPostNoResult']);
			}
		else {
			echo OTDGetListHTML(null, $options['widgetBlock'], $options['widgetItem'], $options['widgetNoResult']);
			}
		}
	elseif (is_single()) {
		// Single post mode
		echo OTDGetListHTML($post, $options['singleBlock'], $options['singleItem'], $options['singleNoResult']);
		}
	else {		
		// Multi-posts mode
		echo OTDGetListHTML($post, $options['multiBlock'], $options['multiItem'], $options['multiNoResult']);
		}
	$post = $oldPost;
	}

function OTDGetListHTML($post=null, $block, $item, $noResult) {
	global $wpdb;
	$block = stripslashes($block);
	$item = stripslashes($item);
	$noResult = stripslashes($noResult);

	$options = get_option('OTDOptions');
	
	$filterID = '';
	$time = time() + (get_option('gmt_offset') * 3600);
	if ($post!==null) {
		$month = mysql2date('n', $post->post_date);
		$day = mysql2date('j', $post->post_date);
		$filterID = "AND ID!=$post->ID";
		}
	else {
		$year = gmdate('Y', $time);
		$month = gmdate('m', $time);
		$day = gmdate('d', $time);
 		}

	$filterProtected = ($options['showProtected']) ? '' : 'AND post_password=""';
	$excludeCurrentYear = ($options['excludeCurrentYear']) ? 'AND YEAR(post_date)<' . gmdate('Y', $time) : '';
	$postTypes = ($options['includePages']) ? '"post","page"' : '"post"';
	$postsLimit = ($options['limit']) ? " LIMIT $options[limit]" : '';
	$yearsLimit = ($options['limitYears']) ? ' AND YEAR(post_date)>' . (gmdate('Y', $time) - $options['limitYears'] - $options['excludeCurrentYear']) : '';
	$posts = $wpdb->get_results("SELECT ID, post_title, post_date, post_content, post_password FROM $wpdb->posts WHERE post_status= \"publish\" AND post_type in ($postTypes) AND post_date_gmt<=DATE_ADD('1970-01-01', INTERVAL UNIX_TIMESTAMP() SECOND) $excludeCurrentYear$yearsLimit AND MONTH(post_date)=$month AND DAYOFMONTH(post_date)=$day $filterID $filterProtected ORDER BY post_date DESC$postsLimit");

	if (count($posts)>0) {
		// Prepare for %items%
		$items = '';
		foreach($posts as $post) {
			$subs = array();
			$subs['%year%'] = mysql2date('Y', $post->post_date);
			$subs['%permalink%'] = get_permalink($post->ID);
			$subs['%title%'] = apply_filters('the_title', $post->post_title);
			$subs['%excerpt%'] = '';
			if (strpos($item, '%excerpt%')!==false && $post->post_password=='') {
				// Get excerpt. based on wp_trim_excerpt in formatting.php
				$text = $post->post_content;
				$text = apply_filters('the_content', $text);
				$text = str_replace(']]>', ']]&gt;', $text);
				$text = strip_tags($text);
				$words = explode(' ', $text, $options['excerptLength'] + 1);
				if (count($words)>$options['excerptLength']) {
					array_pop($words);
					array_push($words, '[...]');
					$text = implode(' ', $words);
					}
				if (strlen($text)>0)
					$subs['%excerpt%'] = $text;
				}
			$items .= str_replace(array_keys($subs), array_values($subs), $item);
			}
		$form = (strpos($block, '%search%')!==false)?GetDateSearchForm($month, $day):'';
		$html = str_replace(array('%items%', '%search%'), array($items, $form), $block);	
		}
	else {
		$form = (strpos($noResult, '%search%')!==false)?GetDateSearchForm($month, $day):'';
		$html = str_replace('%search%', $form, $noResult);
		}

	return $html;
	}

function OTDGetListTitle($title, $post=null) {
	$title = stripslashes($title);

	if ($post===null)
		$date = time() + (get_option('gmt_offset') * 3600);
	else
		$date = mysql2date('U', $post->post_date);
	$subs = array();

	preg_match_all('/%date:(.*?)%/', $title, $matches);
	if (count($matches[0])>0)
		for($i=0; $i<count($matches[0]); $i++) {
			$datetag = $matches[0][$i];
			$datefmt = $matches[1][$i];
			if (array_key_exists($datetag, $subs)) continue;
			$subs[$datetag] = gmdate($datefmt, $date);
			}
	return "\n" . str_replace(array_keys($subs), array_values($subs), $title);
	}

function OTDLoopProcess() {
	// No need to list OTD in admin page.
	if (is_admin()) return;

	global $wp, $wp_query;
	
	$options = get_option('OTDOptions');
	
	$isSingle = is_single();
	if ( $isSingle && !$options['enableSingle']) return;
	if (!$isSingle && !$options[ 'enableMulti']) return;

	// set options
	$title    = ($isSingle) ? $options[   'singleTitle'] : $options[   'multiTitle'];
	$block    = ($isSingle) ? $options[   'singleBlock'] : $options[   'multiBlock'];
	$item     = ($isSingle) ? $options[    'singleItem'] : $options[    'multiItem'];
	$noResult = ($isSingle) ? $options['singleNoResult'] : $options['multiNoResult'];
	
	foreach($wp_query->posts as $post) {
		$html = OTDGetListTitle($title, $post);
		$html .= OTDGetListHTML($post, $block, $item, $noResult);

		if ($isSingle || strpos($post->post_content, '<!--more-->')===false)
			$post->post_content .= $html; // Append citations after post content
		else // Insert citations before more tag.
			$post->post_content = str_replace('<!--more-->', $html.'<!--more-->', $post->post_content);
		}
	setup_postdata($wp_query->posts[0]); // Reset first post.
	}

// TODO: Hook only when enableSingle or enableMulti is true  
add_action('loop_start', 'OTDLoopProcess');

/* Misc.
======================================== */

// gettext failback function
function ___($msgid, $domain, $failBack) {
	$translated = __($msgid, $domain);
	return ($translated == $msgid) ? $failBack : $translated;
	}

function dateArchiveTitle() {
	global $wp_query;
	$options = get_option('OTDOptions');

	$hasYear  = strpos($wp_query->request,       'YEAR') !== false;
	$hasMonth = strpos($wp_query->request,     ' MONTH') !== false;
	$hasDay   = strpos($wp_query->request, 'DAYOFMONTH') !== false;
	$idx = $hasYear * 4 + $hasMonth * 2 + $hasDay;
    $strings = array(
		'',
		__('%1$s of each month', ON_THIS_DAY_DOMAIN),
		__('every %2$s', ON_THIS_DAY_DOMAIN),
		__('%1$s of %2$s', ON_THIS_DAY_DOMAIN),
		__('%3$s', ON_THIS_DAY_DOMAIN),
		__('%1$s of each month in %3$s', ON_THIS_DAY_DOMAIN),
		__('%2$s in %3$s', ON_THIS_DAY_DOMAIN),
		__('%1$s of %2$s in %3$s', ON_THIS_DAY_DOMAIN));

	return sprintf($strings[$idx],
		get_the_time(___('!Date archive title format for Day'  , ON_THIS_DAY_DOMAIN, 'jS')),
		get_the_time(___('!Date archive title format for Month', ON_THIS_DAY_DOMAIN,  'F')),
		get_the_time(___('!Date archive title format for Year' , ON_THIS_DAY_DOMAIN,  'Y')));
	}

function GetDateSearchForm($month=0, $day=0, $year=0) {
	global $wpdb;
	global $postYears;
	$options = get_option('OTDOptions');

	if (!isset($postYears))
		$postYears = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS Y FROM $wpdb->posts WHERE post_status= \"publish\" AND post_type=\"post\" AND post_date_gmt<=DATE_ADD('1970-01-01', INTERVAL UNIX_TIMESTAMP() SECOND) ORDER BY YEAR(post_date)");
	$years = $postYears;
	
	$form = '<form class="dateSearchForm" id="dateSearchForm-' . rand() . '" action=""><div class="dateSearchForm"><select class="dateSearchMonth" name="dateSearchMonth">';

	if ($month==0) $form .= '<option value="00" selected="selected">&nbsp;</option>';
	else $form .= '<option value="00">&nbsp;</option>';
	for($i=1; $i<=12; $i++) {
		$form .= '<option value="';
		$form .= sprintf('%02d"', $i);
		if ($i==$month) $form .= ' selected="selected"';
		$form .= '>' . gmdate($options['formMonth'], gmmktime(0, 0, 0, $i, 1, 0)) . '</option>';
		}
	$form .= '</select><select class="dateSearchDay" name="dateSearchDay">';

	if ($day==0) $form .= '<option value="00" selected="selected">&nbsp;</option>';
	else $form .= '<option value="00">&nbsp;</option>';
	for($i=1; $i<=31; $i++) {
		$form .= '<option value="';
		$form .= sprintf('%02d"', $i);
		if ($i==$day) $form .= ' selected="selected"';
		$form .= '>' . gmdate($options['formDay'], gmmktime(0, 0, 0, 0, $i, 0)) . '</option>';
		}
	$form .= '</select><select class="dateSearchYear" name="dateSearchYear">';

	if ($year==0) $form .= '<option value="0000" selected="selected">&nbsp;</option>';
	else $form .= '<option value="0000">&nbsp;</option>';
	foreach($years as $yearData) {
		$form .= "<option value=\"$yearData->Y\"";
		if ($yearData->Y==$year) $form .= ' selected="selected"s';
		$form .= '>' . gmdate($options['formYear'], gmmktime(0, 0, 0, 1, 1, $yearData->Y)) . '</option>';
		}
	$form .= '</select><input class="dateSearchButton" type="button" value="' . $options['formSearch'] . '" onclick="searchDate(this.form)"/></div></form>';
	
	return $form;
	}

function OTDAddJS() {
	global $wp_rewrite;
	$datePermstruct = $wp_rewrite->get_date_permastruct();
	if ($datePermstruct===FALSE) {
		$datePermstruct = '/?year=%year%&monthnum=%monthnum%&day=%day%';
		}
?>
<script type="text/javascript">
//<![CDATA[
function searchDate(form) {
	var URI = "<?php echo bloginfo('url'); echo $datePermstruct; ?>";
	URI = URI.replace("%year%", form.dateSearchYear.value);
	URI = URI.replace("%monthnum%", form.dateSearchMonth.value);
	URI = URI.replace("%day%", form.dateSearchDay.value);
	window.open(URI, "_self");
	}
//]]>
</script>
<?php
	}

add_action('wp_head', 'OTDAddJS');

/* Widget
======================================== */

function OTDRenderWidget($args) {
	extract($args);

	$options = get_option('OTDOptions');

	if ($options['widgetSinglePost']&&is_single() || is_page()&&$options['includePages']) {
		global $post;
		echo $before_widget;
		echo $before_title, OTDGetListTitle($options['widgetPostTitle'], $post), $after_title;
		echo OTDGetListHTML($post, $options['widgetBlock'], $options['widgetItem'], $options['widgetPostNoResult']);
		echo $after_widget;
		}
	elseif (!(is_page()&&!$options['includePages'])) {
		echo $before_widget;
		echo $before_title, OTDGetListTitle($options['widgetTitle']), $after_title;
		echo OTDGetListHTML(null, $options['widgetBlock'], $options['widgetItem'], $options['widgetNoResult']);
		echo $after_widget;
		}
	}

function OTDRegWidget() {
	if ( function_exists('register_sidebar_widget') )
//		register_sidebar_widget(__('On this day...', ON_THIS_DAY_DOMAIN), 'OTDRenderWidget'); // Bug?
		register_sidebar_widget('On this day...', 'OTDRenderWidget');
	}

add_action('plugins_loaded', 'OTDRegWidget');

/* Options
======================================== */

function OTDGetAllDefaultOptions() {
	$options = array();
	$options = array_merge(OTDGetDefaultGeneralOptions()   , $options);
	$options = array_merge(OTDGetDefaultWidgetOptions()    , $options);
	$options = array_merge(OTDGetDefaultSingleOptions()    , $options);
	$options = array_merge(OTDGetDefaultMultiOptions()     , $options);
	$options = array_merge(OTDGetDefaultSearchFormOptions(), $options);
	return $options;
	}

function OTDGetDefaultGeneralOptions() {
	$options = array();
	$options['limit']              = 5;
	$options['limitYears']         = 0;
	$options['excerptLength']      = 20;
	$options['showProtected']      = false;
	$options['enableSingle']       = false;
	$options['enableMulti']        = false;
	$options['includePages']       = false;
	$options['excludeCurrentYear'] = false;
	return $options;
	}

function OTDGetDefaultWidgetOptions() {
	$options = array();
	$options['widgetSinglePost']   = true;
	$options['widgetTitle']        = __('On this day...', ON_THIS_DAY_DOMAIN);
	$options['widgetPostTitle']    = __('On this day...', ON_THIS_DAY_DOMAIN);
	$options['widgetBlock']        = __("<ul>\n%items%\n</ul>\n%search%", ON_THIS_DAY_DOMAIN);
	$options['widgetItem']         = __("<li>%year%: <a href=\"%permalink%\">%title%</a> &mdash; %excerpt%</li>\n", ON_THIS_DAY_DOMAIN);
	$options['widgetNoResult']     = __('No posts on this day.%search%', ON_THIS_DAY_DOMAIN);
	$options['widgetPostNoResult'] = __('No other posts on this day.%search%', ON_THIS_DAY_DOMAIN);
	return $options;
	}

function OTDGetDefaultSingleOptions() {
	$options = array();
	$options['singleTitle']    = __('<h3>On this day...</h3>', ON_THIS_DAY_DOMAIN);
	$options['singleBlock']    = __("<ul>\n%items%\n</ul>\n%search%", ON_THIS_DAY_DOMAIN);
	$options['singleItem']     = __("<li>%year%: <a href=\"%permalink%\">%title%</a> &mdash; %excerpt%</li>\n", ON_THIS_DAY_DOMAIN);
	$options['singleNoResult'] = __('No other posts on this day.%search%', ON_THIS_DAY_DOMAIN);
	return $options;
	}

function OTDGetDefaultMultiOptions() {
	$options = array();
	$options['multiTitle']    = __('<h3>On this day...</h3>', ON_THIS_DAY_DOMAIN);
	$options['multiBlock']    = __("<ul>\n%items%\n</ul>\n%search%", ON_THIS_DAY_DOMAIN);
	$options['multiItem']     = __("<li>%year%: <a href=\"%permalink%\">%title%</a> &mdash; %excerpt%</li>\n", ON_THIS_DAY_DOMAIN);
	$options['multiNoResult'] = __('No other posts on this day.%search%', ON_THIS_DAY_DOMAIN);
	return $options;
	}

function OTDGetDefaultSearchFormOptions() {
	$options = array();
	$options['formMonth']  = __('M', ON_THIS_DAY_DOMAIN);
	$options['formDay']    = __('j', ON_THIS_DAY_DOMAIN);
	$options['formYear']   = __('Y', ON_THIS_DAY_DOMAIN);
	$options['formSearch'] = __('&raquo;', ON_THIS_DAY_DOMAIN);
	return $options;
	}

/* Admin
======================================== */

function OTDAdminMenu() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('On This Day', ON_THIS_DAY_DOMAIN), __('On This Day', ON_THIS_DAY_DOMAIN), 'manage_options', __FILE__, 'OTDOptions');

	// Check options for prior to 0.2.1
	if (get_option('OTDlimit')!==false) { // Remove values
		delete_option('OTDlimit');
		delete_option('OTDbeforeList');
		delete_option('OTDafterList');
		delete_option('OTDbeforePost');
		delete_option('OTDafterPost');
		delete_option('OTDshowExcerpt');
		delete_option('OTDexcerptLength');
		delete_option('OTDbeforeExcerpt');
		delete_option('OTDafterExcerpt');
		delete_option('OTDnoPost');
		delete_option('OTDshowSearch');
		delete_option('OTDwidgetTitle');
		delete_option('OTDnoOtherPost');
		delete_option('OTDshowProtected');
		}
		
	$options = get_option('OTDOptions');
	if (!is_array($options)) {
		// Version prior to 0.5
		$options = unserialize($options);
		// Check options for 0.2.1-0.3
		if ($options === false) { // Add values
			$options = OTDGetAllDefaultOptions();
			add_option('OTDOptions', $options);
			}
		else {
			// Check options for 0.3
			if (!array_key_exists('includePages', $options)) // Add values
				$options = array_merge(OTDGetAllDefaultOptions(), $options);

			// Check options for 0.4
			if (!array_key_exists('formSearch', $options)) // Add values
				$options = array_merge(OTDGetDefaultSearchFormOptions(), $options);
			update_option('OTDOptions', $options);
			}
		}
	else {
		// Check options for 0.6.2
		if (!array_key_exists('limitYears', $options)) // Add values
			$options = array_merge(OTDGetAllDefaultOptions(), $options);

		update_option('OTDOptions', $options);
		}
	}

// Displays Next 7 Dates in Previous Years
function OTDHook_activity_box_end() {
	global $wpdb;
	$time = time() + get_option('gmt_offset') * 3600;
	$currentDate = gmdate('m-d', $time);
	$currentYear = gmdate('Y', $time);
	$postTypes = '"post","page"';
	// Check cross year
	if ($currentYear != gmdate('Y', $time + 3600 * 24 * 7))
		$crossYear = " OR (YEAR(post_date) <= $currentYear AND MONTH(post_date) = 1 AND DAYOFMONTH(post_date) <= " . gmdate('d', $time + 3600 * 24 * 7) . ')';
?>
<div>
<h3><?php _e('Within 7 Dates in Previous Years', ON_THIS_DAY_DOMAIN); ?></h3>
<?php
	// Close date first, then close year first.
	if ($posts = $wpdb->get_results("SELECT ID, post_title, post_date FROM $wpdb->posts WHERE post_status=\"publish\" AND post_type in ($postTypes) AND post_date_gmt<=DATE_ADD('1970-01-01', INTERVAL UNIX_TIMESTAMP() SECOND) AND ((YEAR(post_date)<$currentYear AND post_date BETWEEN CONCAT_WS('-', YEAR(post_date), '$currentDate') AND CONCAT_WS('-', YEAR(post_date), '$currentDate') + INTERVAL 7 DAY)$crossYear) ORDER BY MONTH(post_date) ASC, DAYOFMONTH(post_date) ASC, YEAR(post_date) DESC LIMIT 7")) {
		echo '<ul>';
		global $post;
		foreach ($posts as $post) 
			the_title('<li>' . get_the_time('F j, Y') . ': <a href="' . get_permalink($post->ID) . '">', '</a></li>');
		echo '</ul>';
		}
	else
		_e('No posts found! You need to write harder!', ON_THIS_DAY_DOMAIN);
?>
</div>
<?php
	}

function OTDDeactivate() {
	if ($_GET['by'] == 'plugin')
		delete_option('OTDOptions');
	}

include_once('OptionsPage.php');
add_action('admin_menu', 'OTDAdminMenu');
add_action('activity_box_end', 'OTDHook_activity_box_end');

register_deactivation_hook(__FILE__, 'OTDDeactivate');
?>
