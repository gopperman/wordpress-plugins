<?php
/**
 * Plugin Name: GO Practice Areas Fields
 * Plugin URI: http://gopperman.com
 * Description: Basic Fields For A Webpage about Services / Practice Areas
 * Version: 0.1
 * Author: Greg Opperman
 * Author URI: http://www.gopperman.com
 * @package go.practice.area.fields
 * @version 0.1.0
 * @author Greg Opperman <gopperman@gmail.com>
*/

class GO_Practice_Areas_Fields {
	function __construct() {
		if ( $this->editing_practice_areas_page() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			add_action( 'fm_post_page', array( $this, 'header_fields' ) );
			add_action( 'fm_post_page', array( $this, 'practice_area' ) );
			add_action( 'fm_post_page', array( $this, 'featured_posts_field' ) );
		}

		if ( $this->editing_practice_areas_page() ) {
			add_action( 'admin_menu', array( $this, 'clean_page_meta_boxes' ) );
		}

		add_filter( 'fieldmanager_revision_fields', array( $this, 'register_fields_for_revision' ), 10, 1 );
	}

	function editing_practice_areas_page() {
		if ( isset( $_GET['post'] ) ) {
			$id = $_GET['post'];
			$template_slug = get_page_template_slug( $id );
			return 'template-practice-areas.php' === $template_slug;
		} else {
			// You must be doing AJAX or something
			return true;
		}
	}

	/**
	 * Add FM fields to page
	 *
	 * @return void
	 */
	function header_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$header = new Fieldmanager_Group( array(
				'name' => 'content_header',
				'children' => array(
					'description' => new Fieldmanager_TextArea( array(
						'label' => 'Description',
		            ) ),
				),
			) );
			$header->add_meta_box( 'Header Area', 'page', 'normal', 'high' );
		}
	}

	function practice_area() {
		$practice_areas =  new Fieldmanager_RichTextArea( array(
			'name' => 'practice_area_content',
	        'editor_settings' => array(
	        	'quicktags' => true,
	            'media_buttons' => false,
            ),
		) );
		$practice_areas->add_meta_box( 'Practice Area Content', 'page', 'normal', 'high' );
	}

	function featured_posts_field() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$featured_posts = new Fieldmanager_Group( array(
				'name' => 'featured_posts',
				'minimum_count' => 0,
				'extra_elements' => 0,
				'required' => true,
				'limit' => 3,
				'sortable' => true,
				'add_more_label' => 'Add Post',
				'children' => array(
					'post_link' => new Fieldmanager_Autocomplete( array(
						'label' => 'Find a Post',
						'datasource' => new Fieldmanager_Datasource_Post( array(
							'query_args' => array( 'post_type' => array( 'post' ) ),
						) ),
					) ),
				),
			) );
			$featured_posts->add_meta_box( 'Featured Posts', 'page', 'normal', 'high' );
		}
	}

	/**
	 * In order for fieldmanager-revisions to work, we need to
	 * register the respective homepage fieldmanager zones.
	 * This will allow the plugin to use the field when previewing the page
	 *
	 * @param array List of fields eligible for revision
	 * @return array Modify fields in the filter to include custom fields
	 */
	function register_fields_for_revision( $fields ) {
		$fields['page']['content_header'] = 'Header Fields';
		return $fields;
	}

	/**
	 * Remove unnecessary meta boxes from page
	 *
	 * @return void
	 */
	function clean_page_meta_boxes() {
		remove_post_type_support( 'page', 'editor' );
	}
}

new GO_Practice_Areas_Fields;
