<?php

/**
* tome order funtionality
*/
class Tome_Order
{
	protected $loader;
	
	function __construct( $loader )
	{
		$this->loader = $loader;
		$this->define_admin_hooks();
	}


	function define_admin_hooks() {
		$this->loader->add_action('admin_menu', $this, 'tome_order_menu_item');
		// $this->loader->add_filter('posts_orderby', $this, 'tome_sort', 99, 2);
	}


	/**
	 * Adds new "Tome Order" page to the admin menu
	 */
	function tome_order_menu_item() {
		add_menu_page(
			'My Plugin Title',
			'Posts Order',
			'edit_posts',
			'tome-order',
			array($this, 'tome_order_page'),
			'dashicons-dashboard',
			1
		);
	}

	/**
	 * Callback for the "tome_order_menu_item" function.
	 * HTML for Tome Dashboard
	 */
	function tome_order_page() {
	?>
	<h2>hello</h2>
	<?php
	}

}