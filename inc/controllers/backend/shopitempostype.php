<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 8/07/2018
 * Time: 22:18
 */

namespace Xe_GOC\Inc\Controllers\Backend;


use Xe_GOC\Inc\Models\Frontend\shopItem;

class ShopItem_Postype {

	private $initiated = false;
	private $posttype = XE_GOC_POSTYPE_SHOP_ITEM;
	private $post_id;

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	public function __construct()
	{

		if ( ! $this->initiated ) {
			$this->init_hooks();
		}

	}

	/**
	 * Load all the wordpress hooks
	 */
	private function init_hooks(){
		$this->initiated = true;


		// Admin hooks
		if(is_admin()){
			$this->admin_hooks();

		}

		// Enqueu frontend scripts
		add_action('wp_enqueue_scripts', array($this,'loadJSFrontend'));
		// create production post type
		add_action('init',array($this,'create_post_type'));
		// add custom soort taxonomy
		add_action('init',array($this,'create_soort_tax'));

	}

	/**
	 *
	 */

	private function admin_hooks(){

		add_action('admin_enqueue_scripts', array($this,'loadJSBackend'));

		// add meta
		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes', array($this,'add_custom_meta_box') );

		// Save meta boxes
		add_action( 'save_post', array($this,'saveMeta'), 10, 2 );
	}



	/**
	 * LOad the backend JS & CSS
	 */
	public function loadJSBackend(){

		wp_enqueue_script( 'jquery' );
	}

	/**
	 *
	 */
	public function loadJSFrontend(){

		wp_enqueue_script( 'jquery' );

	}

	/**
	 * Create a new Activiteis Post type
	 */
	public function create_post_type() {

		$labels = array(
			'name'               => _x( 'Shop item', 'post type general name', 'xe_goc' ),
			'singular_name'      => _x( 'Shop item', 'post type singular name', 'xe_goc' ),
			'menu_name'          => _x( 'Shop items', 'admin menu', 'xe_goc' ),
			'name_admin_bar'     => _x( 'Shop item', 'add new on admin bar', 'xe_goc' ),
			'add_new'            => _x( 'Add new', 'Post', 'xe_goc' ),
			'add_new_item'       => __( 'Add new shop item', 'xe_goc' ),
			'new_item'           => __( 'New shop item', 'xe_goc' ),
			'edit_item'          => __( 'Edit shop item', 'xe_goc' ),
			'view_item'          => __( 'View shop item', 'xe_goc' ),
			'all_items'          => __( 'All shop items', 'xe_goc' ),
			'search_items'       => __( 'Search shop items', 'xe_goc' ),
			'parent_item_colon'  => __( 'Older shop items:', 'xe_goc' ),
			'not_found'          => __( 'No shop items found.', 'xe_goc' ),
			'not_found_in_trash' => __( 'No shop items in trash.', 'xe_goc'),
			'featured_image'     => __( 'Shop item image', 'xe_goc'),
			'set_featured_image' => __('Select a image', 'xe_goc'),
			'remove_featured_image' => __('Remove image', 'xe_goc'),
			'use_featured_image' => __ ('Use image', 'xe_goc')
		);


		// Register activitys  post type
		register_post_type( $this->posttype,
			array(
				'labels' => $labels,
				'public' => false,
				'description' => 'Shop items',
				'show_ui' => true,
				'show_in_menu' => true,
				'exclude_from_search' => false,  // you should exclude it from search results
				'show_in_nav_menus' => true,  // you shouldn't be able to add it to menus
				'supports' => array('title','editor','thumbnail'),
				'menu_icon' => 'dashicons-cart',
				'rewrite' => array(
					'with_front'            => false,
					'pages'                 => false,
					'feeds'                 => false
				)
			)
		);
	}


	/**
	 * Add custom taxonomy for activity type
	 */
	public function create_soort_tax() {

		$labels = array(
			'name'              => _x( 'Type', 'taxonomy general name', $this->posttype ),
			'singular_name'     => _x( 'Type', 'taxonomy singular name', $this->posttype ),
			'search_items'      => __( 'Search Types', $this->posttype ),
			'all_items'         => __( 'All Types', $this->posttype ),
			'parent_item'       => __( 'Head Type', $this->posttype ),
			'parent_item_colon' => __( 'Head Type:', $this->posttype ),
			'edit_item'         => __( 'Edit Type', $this->posttype ),
			'update_item'       => __( 'Update Type', $this->posttype ),
			'add_new_item'      => __( 'New Type', $this->posttype ),
			'new_item_name'     => __( 'New name', $this->posttype ),
			'menu_name'         => __( 'Types', $this->posttype ),
		);

		register_taxonomy(
			XE_GOC_POSTYPE_SHOP_ITEM_TYPE,
			$this->posttype,
			array(
				'labels' => $labels,
				'hierarchical' => true,
				'query_var'             => true,
			)
		);
	}



	/**
	 * Add custom meta boxes
	 */

	public function add_custom_meta_box(){
		
		// Add a meta box
		add_meta_box(
			'xe_goc_meta',      // Unique ID
			esc_html__('Item specific settings', 'xe_goc'),    // Title
			array($this, 'custom_main_meta'),   // Callback function
			$this->posttype,         // Admin page (or post type)
			'normal',         // Context
			'default'         // Priority
		);
	}

	/**
	 * Load the custom meta item
	 */
	public function custom_main_meta(){

		global $post_ID;

		require_once (XE_GOC_PLUGIN_PATH.'templates/backend/shop-item/custom_meta.php');

	}

	/* Save the meta content. */
	public function saveMeta( $post_id, $post ){

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		foreach($_POST as $key => $item){ // loop every post item

			$checkPost = strpos($key,shopItem::$save_needle);
			if($checkPost !== false){

				// add to meta
				/* Get the meta value of the custom field key. */
				$meta_value = get_post_meta( $post_id, $key, true );

				/* If a new meta value was added and there was no previous value, add it. */
				if ( $item && '' == $key )
					add_post_meta( $post_id, $key, $item, true );

				/* If the new meta value does not match the old value, update it. */
				elseif ( $item && $item != $meta_value )
					update_post_meta( $post_id, $key, $item );

				/* If there is no new meta value but an old value exists, delete it. */
				elseif ( '' == $item && $meta_value )
					delete_post_meta( $post_id, $key, $item );

			}

		}


	}

	/**
	 * @param $v
	 *
	 * @return string
	 */
	private function getMetaKey($v){

		return shopItem::getMetaKey($v);

	}

}