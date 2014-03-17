<?php
/*
Plugin Name: Bullet FAQs
Plugin URI: http://bappi-d-great.com
Description: Provides nice Frequently Asked Questions Page with answers hidden untill the question is clicked then the desired answer fades smoothly into view, like accordion. User will have options to add categories, and questions based on those categories. Users can show question from a single category using shortcode. They will have control to change theme (among 9 themes), animation speed and custom CSS.
Version: 1.0
Author: Bappi D Great
Author URI: http://bappi-d-great.com
License: GPLv2 or later
*/

require_once 'lib/base.php';
require_once 'lib/widget.php';

//Defining main class
class FAQ extends BASE
{
    
    public $options;
    
    //Constructor
    public function __construct() {
        parent::__construct();
        $this->faq_init();
        $this->options = get_option('faq_options');
    }
    
    
    /*
     * 
     * Initialize all required methods
     * 
     */
    public function faq_init() {
        add_action('init', array($this, 'faq_category'));
        add_action('init', array($this, 'create_post_type'));
        add_shortcode( 'show_faq', array($this, 'faq_shortcode') );
        add_action('admin_menu' , array($this, 'register_faq_settings')); 

        
        //Adding styles and scripts
        add_action( 'wp_enqueue_scripts', array($this, 'user_faq_styles') );
        
        //Adding custom columns in taxonomy
        add_action( "manage_edit-faq_categories_columns",          array($this, 'posts_columns_id') );
        add_filter( "manage_edit-faq_categories_sortable_columns", array($this, 'posts_columns_id') );
        add_filter( "manage_faq_categories_custom_column",         array($this, 'posts_custom_id_columns'), 10, 3 );
        
        
        //Adding widget
        add_action( 'widgets_init', array($this, 'register_faq_widget') );
        
        add_action('admin_init', array($this, 'register_settings_and_fields'));
    }
    
    
    /*
     * 
     * ShortCode Enabling and displaying into front end
     * 
     */
    public function faq_shortcode($atts) {
        extract( shortcode_atts( array(
		'id' => ''
	), $atts ) );
  
        $html = '';
        $data = $this->options;
        if($id != '')
        {
            $cat = get_term( $id, 'faq_categories' );
            include 'templates/category_view.php';
        }
        else
        {
            $cat = get_terms('faq_categories');
            include 'templates/all_view.php';
        }
        
        return $html;
    }
    
    /*
     * Registering widget
     */
    public function register_faq_widget() {
        register_widget( 'faq_widget' );
    }
    
    /*
     * Adding Settings page in FAQ Menu
     */
    public function register_faq_settings() {
        add_submenu_page('edit.php?post_type=faq', 'FAQ Settings', 'FAQ Settings', 'edit_posts', 'faq_settings', array($this, 'faq_settings'));
        add_action('admin_init', array($this, 'service_settings_store'));
    }
    
    public function service_settings_store() {
        
    }
    
    /*
     * Settings page view in Dashboard
     */
    public function faq_settings() {
        if (!current_user_can('manage_options')) {  
            wp_die('You do not have sufficient permissions to access this page.');  
        }

        ?>
        <div class="wrap rev-admin">
            <?php screen_icon('tools'); ?>
            <h2>Faq Settings</h2>
            <form method="post" action="options.php"> 
                <?php settings_fields('faq_options'); ?>
                <?php do_settings_sections('faq_settings'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    
    /*
     * Fields for FAQ Settings Page
     */
    public function register_settings_and_fields() {
        
        register_setting('faq_options', 'faq_options');
        
        add_settings_section('faq_main_section', 'Faq Settings', array($this, 'faq_main_sec_cb'), 'faq_settings');
        add_settings_field('faq_theme', 'Choose a theme:', array($this, 'faq_theme'), 'faq_settings', 'faq_main_section');
        add_settings_field('faq_expand', 'Enable expand all rows:', array($this, 'faq_expand'), 'faq_settings', 'faq_main_section');
        add_settings_field('faq_speed', 'Animation Speed:', array($this, 'faq_speed'), 'faq_settings', 'faq_main_section');
        add_settings_field('faq_css', 'Custom CSS:', array($this, 'faq_css'), 'faq_settings', 'faq_main_section');
    }
    
    /*
     * Callbacks for Settings Page
     */
    public function faq_theme() {
        $html = "<select name='faq_options[theme]'>";
        $html .= "<option value='theme-1' ". (($this->options['theme'] == 'theme-1') ? 'selected' : '').">Theme 1</option>";
        $html .= "<option value='theme-2' ". (($this->options['theme'] == 'theme-2') ? 'selected' : '').">Theme 2</option>";
        $html .= "<option value='theme-3' ". (($this->options['theme'] == 'theme-3') ? 'selected' : '').">Theme 3</option>";
        $html .= "<option value='theme-4' ". (($this->options['theme'] == 'theme-4') ? 'selected' : '').">Theme 4</option>";
        $html .= "<option value='theme-5' ". (($this->options['theme'] == 'theme-5') ? 'selected' : '').">Theme 5</option>";
        $html .= "<option value='theme-6' ". (($this->options['theme'] == 'theme-6') ? 'selected' : '').">Theme 6</option>";
        $html .= "<option value='theme-7' ". (($this->options['theme'] == 'theme-7') ? 'selected' : '').">Theme 7</option>";
        $html .= "<option value='theme-8' ". (($this->options['theme'] == 'theme-8') ? 'selected' : '').">Theme 8</option>";
        $html .= "<option value='theme-9' ". (($this->options['theme'] == 'theme-9') ? 'selected' : '').">Theme 9</option>";
        $html .= "</select>";
        echo $html;
    }
    
    public function faq_expand() {
        $html = "<select name='faq_options[expand]'>";
        $html .= "<option value='false' ". (($this->options['expand'] == 'false') ? 'selected' : '').">No</option>";
        $html .= "<option value='true' ". (($this->options['expand'] == 'true') ? 'selected' : '').">Yes</option>";
        $html .= "</select>";
        echo $html;
    }
    
    public function faq_speed() {
        echo "<input type='text' name='faq_options[faq_speed]' value='{$this->options['faq_speed']}' /> The lower the value, the faster the animation";
    }
    
    public function faq_css() {
        echo "<textarea name='faq_options[faq_css]' rows='10' cols='80'>{$this->options['faq_css']}</textarea>";
    }


    public function faq_main_sec_cb() {
        
    }
    
}

$faq = new FAQ();