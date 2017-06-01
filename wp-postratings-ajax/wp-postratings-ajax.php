<?php
/*
Plugin Name: WP-PostRatings AJAX
Description: Adds AJAX loading to WP-PostRatings plugin. The rating system will be loaded in AJAX, and it will make the plugin usable with cache system like WP SuperCache.
Version: 1.0
Author: Joffrey Letuve
Author URI: http://www.joffrey-letuve.com
Text Domain: wp-postratings-ajax
License: GPL2
*/


/*
    Copyright 2017 Joffrey Letuve  (email : joffrey@letuve.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Security check
 * Prevent direct access to the file.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



### Function: Make postratings callable in AJAX without restriction
if(!function_exists('ajax_ratings_results')) {
	function ajax_ratings_results()
	{
		if(!isset($_GET['pid']))
		{
	        wp_die('Post ID parameter is missing');
		}
        $post_id = $_GET['pid'];
	    the_ratings('span', $post_id);

		wp_die();
	}
 add_action('wp_ajax_postratingsresults', 'ajax_ratings_results');
 add_action('wp_ajax_nopriv_postratingsresults', 'ajax_ratings_results');
}


### Function: Print JS code to load rating through ajax request
if(!function_exists('the_ratings_ajax')) {
	function the_ratings_ajax($post_id=0)
	{
		if($post_id==0)
            $post_id = get_the_ID();

		$html = '<span id="post-ratings-ajax-'.$post_id.'"></span>';
		$html.= "<script>$(document).ready(function(){post_ratings_results_ajax('".$post_id."', '#post-ratings-ajax-$post_id');});</script>";
		echo $html;
	}
}


### Function: Short Code For Loading PostRatings through AJAX
if(!function_exists('ratings_ajax_shortcode'))
{
	function ratings_ajax_shortcode( $atts ) {
		$attributes = shortcode_atts( array( 'id' => 0 ), $atts );
		return the_ratings_ajax($attributes['id']);
	}

	// Overwrite initial [ratings] shortcode
	function overwrite_shortcode()
	{
		remove_shortcode( 'ratings' );
		add_shortcode( 'ratings', 'ratings_ajax_shortcode' );
	}
	add_action( 'wp_loaded', 'overwrite_shortcode' );
}


### Register the .js loading
wp_enqueue_script('wp-postratings-ajax', plugins_url('wp-postratings-ajax/wp-postratings-ajax.js'), array('jquery'), 1.0, true);