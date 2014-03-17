<?php

class BASE {
    
    //Constructor
    public function __construct() {
        
    }
    
    /*
     * 
     * Custom Post Type
     * 
     */
    public function create_post_type() {
        $labels = array(
            'name' => _x('FAQs', 'post type general name'),
            'singular_name' => _x('FAQ', 'post type singular name'),
            'add_new' => _x('Add New', 'Slide'),
            'add_new_item' => __('Add New FAQ'),
            'edit_item' => __('Edit FAQ'),
            'new_item' => __('New FAQ'),
            'all_items' => __('All FAQs'),
            'view_item' => __('View FAQs'),
            'search_items' => __('Search FAQs'),
            'not_found' => __('No FAQ found'),
            'not_found_in_trash' => __('No FAQ found in Trash'),
            'parent_item_colon' => '',
            'menu_name' => __('FAQs')
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => _x('faqs', 'URL slug')),
            'capability_type' => 'page',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'page-attributes')
        );
        register_post_type('faq', $args);
    }
    
    
    /*
     * 
     * Custom category taxonomy for FAQs
     * 
     */
    public function faq_category() {
	
        // create a new taxonomy
        $labels = array(
            'name' => _x( 'FAQ Categories', 'taxonomy general name' ),
            'singular_name' => _x( 'faq_categoriy', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search Categories' ),
            'all_items' => __( 'All Categories' ),
            'parent_item' => __( 'Parent Category' ),
            'parent_item_colon' => __( 'Parent Category:' ),
            'edit_item' => __( 'Edit Category' ),
            'update_item' => __( 'Update Category' ),
            'add_new_item' => __( 'Add New Category' ),
            'new_item_name' => __( 'New Category Name' ),
        );
	register_taxonomy(
		'faq_categories',
		array('faq'),
		array(
                    'hierarchical' => true,
                    'labels' => $labels,
                    'show_ui' => true,
                    'query_var' => true,
                    'show_admin_column' => true,
                    'rewrite' => array( 'slug' => 'recordings' ),
                     )
       );
    }
    
    /*
     * 
     * Adding Styles and Scripts
     * 
     */
    public function user_faq_styles() {
        wp_register_script( 'accordion_js', plugins_url().'/bullet_faqs/js/faqAccordion.js', array( 'jquery' ) );
        wp_enqueue_script( 'accordion_js' );
        
        wp_register_style( 'accordion_css', plugins_url().'/bullet_faqs/css/faqs.css');
        wp_enqueue_style( 'accordion_css' );
    }
    
    /*
     * 
     * Adding column in taxonomy
     * 
     */
    public function posts_columns_id($columns) {
        return $columns + array ( 'tax_id' => 'ID' );    
    }

    public function posts_custom_id_columns($v, $name, $id) {
        return $id;
    }
    
}
