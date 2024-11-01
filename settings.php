<?php

/**
 * TextFilter Settings API
 *
 */
if ( !class_exists('TextFilter_Settings_API' ) ):
class TextFilter_Settings_API {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'textfilter_admin_init') );
        add_action( 'admin_menu', array($this, 'textfilter_admin_menu') );
    }

    function textfilter_admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_textfilter_settings_sections() );
        $this->settings_api->set_fields( $this->get_textfilter_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function textfilter_admin_menu() {
        add_options_page( 'TextFilter', 'TextFilter', 'delete_posts', 'textfilter', array($this, 'textfilter_plugin_page') );
    }

    function get_textfilter_settings_sections() {
        $sections = array(
            array(
                'id'    => 'tfwidget_basic',
                'title' => __( 'Basic Settings', 'tfwidget' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_textfilter_settings_fields() {
    	$images = array (
    		'textfilter.png', 'printer.svg', 'pdf.svg', 'email.svg' 
    	);
    	
    	foreach($images as $img) {
    		$icons[$img] = " <img src='".plugins_url( 'assets/img/icons/'.$img, __FILE__ )."' style='width:16px;'> ";
    	}
    	
		$args = array(
		   'public'   => true,
		);
		
		$postTypes = get_post_types($args);
		unset($postTypes["attachment"]);
		unset($postTypes["revision"]);		
		
		
        $settings_fields = array(
            'tfwidget_basic' => array(
                array(
                    'name'              => 'button_text',
                    'label'             => __( 'Button Text', 'tfwidget' ),
                    'desc'              => __( 'The text label for your TextFilter button.', 'tfwidget' ),
                    'placeholder'       => __( 'Text Input placeholder', 'tfwidget' ),
                    'type'              => 'text',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'    => 'icons',
                    'label'   => __( 'Icons', 'tfwidget' ),
                    'desc'    => __( 'Select the icon(s) to appear in the TextFilter button. (Icon selections will not affect functionality available to user.)', 'tfwidget' ),
                    'type'    => 'multicheck',
                    'options' => $icons
                ),
                array(
                    'name'    => 'placement',
                    'label'   => __( 'Placement', 'tfwidget' ),
                    'desc'    => __( 'The TextFilter will automatically be inserted before or after your content.', 'tfwidget' ),
                    'type'    => 'radio',
                    'default' => 'below',
                    'options' => array(
                        'above' => 'Above Content',
                        'below'  => 'Below Content',
                        'manual'  => 'Manual (You will need to insert the widget via shortcode or PHP function.)'                        
                    )
                ),
                array(
                    'name'    => 'alignment',
                    'label'   => __( 'Alignment', 'tfwidget' ),
                    'desc'    => __( 'Select text-alignment for your TextFilter button. ', 'tfwidget' ),
                    'type'    => 'radio',
                    'default' => 'left',
                    'options' => array(
                        'left' => 'Left',
                        'center'  => 'Center',                        
                        'right'  => 'Right',
                        'inherit'  => 'Inherit',                        
                    )
                ),
                array(
                    'name'    => 'post_types',
                    'label'   => __( 'Post Types', 'tfwidget' ),
                    'desc'    => __( 'Select the post types that should include the TextFilter button.', 'tfwidget' ),
                    'type'    => 'multicheck',
                    'options' => $postTypes
                )               
            )
        );

        return $settings_fields;
    }

    function textfilter_plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

}
endif;
