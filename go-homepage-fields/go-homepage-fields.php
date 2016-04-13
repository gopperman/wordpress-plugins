<?php
/**
 * Plugin Name: GO Homepage Fields
 * Plugin URI: http://gopperman.com
 * Description: Creates Home Page and registers some basic fields
 * Version: 0.1
 * Author: Greg Opperman
 * Author URI: http://www.gopperman.com
 * @package go.homepage.fields
 * @version 0.1.0
 * @author Greg Opperman <gopperman@gmail.com>
*/

class GO_Homepage_Fields {
	function __construct() {
		register_activation_hook( __FILE__, array( $this, 'homepage_init' ) );

		if ( $this->is_editing_homepage() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			add_action( 'fm_post_page', array( $this, 'header_fields' ) );
			add_action( 'fm_post_page', array( $this, 'about_fields' ) );
			add_action( 'fm_post_page', array( $this, 'content_fields' ) );
			add_action( 'fm_post_page', array( $this, 'product_fields' ) );
		}
		if ( $this->is_editing_homepage() ) {
			add_action( 'admin_menu', array( $this, 'clean_homepage_meta_boxes' ) );
			add_action( 'admin_title', array( $this, 'edit_homepage_title' ) );
		}

		add_filter( 'fieldmanager_revision_fields', array( $this, 'register_homepage_fields_for_revision' ), 10, 1 );
	}

	/**
	 * Creates a home page if one doesn't exist yet, sets it to be the front page.
	 */
	function homepage_init() {
		$home = $this->get_page_id( 'home-page' );
		// Create Home Page
		if ( ! $home ) {
			$homepage = array(
				'post_name' => 'home-page',
				'post_title' => 'Home Page',
				'post_status' => 'publish',
				'post_type' => 'page',
			);
			$insert = wp_insert_post( $homepage );
			if ( is_wp_error( $insert ) ) {
				print_r( $insert->get_error_message() );
			} else {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $insert );
			}
		}
	}

	/**
	 * Remove unnecessary meta boxes from Homepage
	 *
	 * @return void
	 */
	function clean_homepage_meta_boxes() {
		remove_meta_box( 'pageparentdiv', 'page', 'side' );
		remove_post_type_support( 'page', 'title' );
		remove_post_type_support( 'page', 'editor' );
	}

	/**
	 * Change title of admin page for homepage
	 * See: http://wordpress.stackexchange.com/questions/17025/change-page-title-in-admin-area
	 *
	 * @return string Title for admin panel
	 */
	function edit_homepage_title() {
		global $post, $title, $action, $current_screen;
		if ( isset( $current_screen->post_type ) && 'page' === $current_screen->post_type && 'edit' === $action ) {
			$title = 'Edit Home Page';
		}
		return $title;
	}

	/**
	* Gets page ID based on slug
	*
	* @return int (or null)
	*/
	function get_page_id ( $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			return $page->ID;
		} else {
			return null;
		}
	}

	/**
	* Check if we're editing the home page right now
	*
	* @return bool
	*/
	function is_editing_homepage() {
		global $pagenow;
		if ( 'post.php' === $pagenow ) {
			$homepage = get_option( 'page_on_front' );
			$current_id = array_key_exists( 'post', $_GET ) ? $_GET['post'] : $_POST['post_ID'] ;
			return $current_id === $homepage;
		} else {
			return false;
		}
	}

	/**
  	 * Add FM-Zones fields to homepage
	 *
	 * @return void
	 */
	function header_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$header = new Fieldmanager_Group( array(
				'name' => 'homepage_header',
				'children' => array(
					'header_text' => new Fieldmanager_Textfield( 'Header Text' ),
					'header_description' => new Fieldmanager_TextArea( 'Description' ),
					'ctas' => new Fieldmanager_Group( array(
						'label' => 'Call to Action',
						'description' => 'Calls to action are buttons that link to posts or pages that you want to promote on the homepage header.',
						'minimum_count' => 1,
						'extra_elements' => 0,
						'required' => true,
						'limit' => 2,
						'sortable' => true,
						'add_more_label' => 'Add CTA',
						'children' => array(
							'text' => new Fieldmanager_Textfield( 'CTA Text' ),
							'link' => new Fieldmanager_Autocomplete( array(
								'label' => 'Find a Post / Page',
								'datasource' => new Fieldmanager_Datasource_Post( array(
									'query_args' => array( 'post_type' => array( 'post', 'page' ) ),
								) ),
							) ),
						),
					) ),
				),
			) );
			$header->add_meta_box( 'Header Area', 'page', 'normal', 'high' );
		}
	}

	function about_fields() {
		$about = new Fieldmanager_TextArea( array(
			'name' => 'homepage_about',
			'description' => 'A brief but detailed description about you',
		) );
		$about->add_meta_box( 'About', 'page', 'normal', 'high' );
	}

	function content_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$content = new Fieldmanager_Group( array(
				'name' => 'homepage_content',
				'label' => 'Content Area',
				'description' => 'Areas to highlight your specialties and expertise',
				'minimum_count' => 1,
				'extra_elements' => 0,
				'required' => true,
				'limit' => 3,
				'sortable' => true,
				'add_more_label' => 'Add Content Area',
				'children' => array(
					'header_text' => new Fieldmanager_Textfield( 'Header Text' ),
					'header_description' => new Fieldmanager_TextArea( 'Description' ),
					'cta' => new Fieldmanager_Group( array(
						'label' => 'Call to Action',
						'children' => array(
							'text' => new Fieldmanager_Textfield( 'CTA Text' ),
							'link' => new Fieldmanager_Autocomplete( array(
								'label' => 'Find a Post / Page',
								'datasource' => new Fieldmanager_Datasource_Post( array(
									'query_args' => array( 'post_type' => array( 'post', 'page' ) ),
								) ),
							) ),
						),
					) ),
				),
			) );
			$content->add_meta_box( 'Content Areas', 'page', 'normal', 'high' );
		}
	}

	function product_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$product = new Fieldmanager_Group( array(
				'name' => 'homepage_product',
				'children' => array(
					'image' => new Fieldmanager_Media( array(
						'label' => 'Product Image',
						'description' => 'An image of the product with a transparent background',
						'required' => true,
						'mime_type' => 'image',
					) ),
					'header_text' => new Fieldmanager_Textfield( 'Header Text' ),
					'header_description' => new Fieldmanager_TextArea( 'Description' ),
					'cta' => new Fieldmanager_Group( array(
						'label' => 'Call to Action',
						'children' => array(
							'text' => new Fieldmanager_Textfield( 'CTA Text' ),
							'link' => new Fieldmanager_Autocomplete( array(
								'label' => 'Find a Post / Page',
								'datasource' => new Fieldmanager_Datasource_Post( array(
									'query_args' => array( 'post_type' => array( 'post', 'page' ) ),
								) ),
							) ),
						),
					) ),
				),
			) );
			$product->add_meta_box( 'Product Callout', 'page', 'normal', 'high' );
		}
	}

	/**
	 * In order for fieldmanager-revisions to work, we need to
	 * register the respective homepage fieldmanager zones.
	 * This will allow the plugin to use the field when previewing the home page
	 * FIXME: Change this to run only on front page
	 *
	 * @param array List of fields eligible for revision
	 * @return array Modify fields in the filter to include homepage_primary_content
	 */
	function register_homepage_fields_for_revision( $fields ) {
		$fields['page']['homepage_header'] = 'Header Area';
		$fields['page']['homepage_about'] = 'Header About';
		$fields['page']['homepage_content'] = 'Header Content';
		$fields['page']['homepage_product'] = 'Header Product';
		return $fields;
	}
}

new GO_Homepage_Fields;
