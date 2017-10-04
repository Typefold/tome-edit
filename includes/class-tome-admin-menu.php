<?php


/**
 * Tome Admin Menu
 * 
 * In this class, we edit default wordpress menu for custom tome needs.
 *
 * @package    tome-edit
 * @subpackage tome-edit/admin
 */
class Tome_Admin_Menu
{

	protected $loader;

	public	 $form_data;

	function __construct( $loader )
	{
		$this->loader = $loader;

		$this->define_admin_hooks();
	}

	private function define_admin_hooks()
	{
		$this->loader->add_action('admin_menu', $this, 'custom_menu_page_removing');
		$this->loader->add_action('admin_menu', $this, 'add_menu_items');
		$this->loader->add_filter('custom_menu_order', $this, 'submenu_items_order');
		$this->loader->add_action('init', $this, 'edit_default_post_types');
	}


	function submenu_items_order( $menu_ord ) 
	{
	    global $menu, $submenu, $parent_file;

	    // Enable the next line to see all menu orders
	    // echo '<pre style="margin-left: 100px;">'.print_r($submenu,true).'</pre>';



	    // Change the order in "Writing" submenu
	    $arr = array();
	    $arr[] = $submenu['tome-writing'][2];     //my original order was 5,10,15,16,17,18
	    $arr[] = $submenu['tome-writing'][1];
	    $arr[] = $submenu['tome-writing'][0];
	    $arr[] = $submenu['tome-writing'][3];
	    $arr[] = $submenu['tome-writing'][4];
	    $submenu['tome-writing'] = $arr;

	    // Remove tags from places
	    unset($submenu['edit.php?post_type=tome_place'][15]);
	    // Remove tags from media
	    unset($submenu['upload.php'][15]);

	    return $menu_ord;
	}



	// 1. Change labels for default post types
	// 2. Move default post types like Posts and Pages under different post type in admin menu
	function edit_default_post_types() {
		global $wp_post_types;

		// 1. change labels
		$wp_post_types['post']->labels->all_items = 'Posts';
		$wp_post_types['page']->labels->all_items = 'Pages';

		// 2. different menu position
		$wp_post_types['page']->show_in_menu = 'tome-writing';
		$wp_post_types['post']->show_in_menu = 'tome-writing';

		$wp_post_types['chapter']->show_in_menu = 'tome-writing';
	}


	/**
	 * Removing wordpress pages from admin menu / submenus that we don't need
	 */
	function custom_menu_page_removing() {
		remove_menu_page( 'index.php' );
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit.php?post_type=tome_gallery' );
		remove_menu_page( 'edit.php?post_type=tome_media' );
		remove_menu_page( 'edit.php?post_type=tome_map' );
		remove_menu_page( 'edit.php?post_type=tome_reference' );
		remove_submenu_page( 'upload.php', 'media-new.php' );
	}


	function add_menu_items() {
		add_menu_page( 'tome_writing', 'Writing', 'edit_posts', 'tome-writing', NULL, 'dashicons-edit', 4 );
		add_menu_page( 'help_tips', 'Help Tips', 'edit_posts', 'tome-help', NULL, 'dashicons-edit', 999 );
		add_submenu_page( 'upload.php', 'Galleries', 'Galleries', 'edit_posts', 'edit.php?post_type=tome_gallery', NULL );
		add_submenu_page( 'tome-writing', 'Categories', 'Categories', 'manage_categories', 'edit-tags.php?taxonomy=category&post_type=chapter', NULL );
	}


}