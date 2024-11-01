<?php
/**
 * Plugin Name: TextFilter Print, E-Mail and PDF Button
 * Plugin URI: https://textfilter.me/buttons/
 * Description: Convert web pages into PDF, ePub, mobi / Kindle files. (Download, print, and e-mail.)
 * Author: TextFilter
 * Author URI: https://votable.net/textfilter/
 * Version: 0.4
 * Source Repo: https://github.com/tareq1988/wordpress-settings-api-class
 * Source Plugin: https://wordpress.org/plugins/settings-api/
 */

require_once dirname( __FILE__ ) . '/src/class.settings-api.php';
require_once dirname( __FILE__ ) . '/settings.php';


function textfilter_encode_string($string){
	$replace = array("~", "`","[", "]", "^", "|", "\\", "{", "}");
	$string = str_replace($replace, "", $string);
	$string = htmlentities($string, ENT_QUOTES, ini_get("default_charset"), FALSE);
	return $string;
}

new TextFilter_Settings_API();

function textfilter_option( $option, $section, $default = '' ) {
 
    $options = get_option( $section );
 
    if ( isset( $options[$option] ) ) {
    return $options[$option];
    }
 
    return $default;
}

if (!is_admin()) add_action("wp_enqueue_scripts", "textfilter_scripts", 11);
function textfilter_scripts() {
	// temp url for testing/development
	wp_enqueue_script( 'textfilter-js', 'http://votable.net/textfilter/api/v1/widget.js', array(), rand(1,10000), true );
}

function textfilter_button($permalink, $title) {
	$title = textfilter_encode_string($title);
	$permalink = textfilter_encode_string($permalink);
	
	$button_text = textfilter_option( 'button_text', 'tfwidget_basic', '' );
	$alignment = textfilter_option( 'alignment', 'tfwidget_basic', 'left' );

	$images = textfilter_option( 'icons', 'tfwidget_basic', array('textfilter.png', 'printer.svg', 'pdf.svg', 'email.svg' ) );
	
	$icons = "";
	
	foreach($images as $img) {
		$icons .= " <img src='".plugins_url( 'assets/img/icons/'.$img, __FILE__ )."' style='width:16px;'> ";
	}	
    	
	$link = "<a href='https://votable.net/textfilter/?title=".$title."&url=".$permalink."' data-widget-type='wp-plugin' data-title='".$title."' data-permalink='".$permalink."' rel='nofollow' class='textfilterRevealModal' style='text-decoration:none; display:block;'>".$icons.$button_text."</a>";
	
	if($alignment !== "inherit"){
		$link = "<p style='text-align:".$alignment.";'>".$link."</p>";
	}
	
	return $link;
}

// Add shortcode for printfriendly button
add_shortcode( 'textfilter', 'textfilter_button' );



add_filter( 'the_content', 'textfilter_content_filter', 20 );
/**
 * Add the TextFilter to the above or below post content.
 *
 * @uses is_single()
 */
function textfilter_content_filter( $content ) {
	$position = textfilter_option( 'position', 'tfwidget_basic', 'below' );
	$postTypes = textfilter_option( 'post_types', 'tfwidget_basic', 'post' );
	$postTypes = array_keys($postTypes);
	
	$content = "<div class='textfilterContentWrapper'>".$content."</div>";
    	
	if(is_singular($postTypes)){

		if ( $position == "above" ) {
			// Add image to the beginning of each page
			$content = textfilter_button(get_permalink(), get_the_title()).$content;
		} elseif ( $position == "below" ) {
			// Returns the content.
			$content = $content.textfilter_button(get_permalink(), get_the_title());
		}
    }
    
    return $content;
}