<?php
/**
 * Plugin Name: GO Media Page Fields
 * Plugin URI: http://gopperman.com
 * Description: Basic Fields For A Webpage about Media Appearances
 * Version: 0.1
 * Author: Greg Opperman
 * Author URI: http://www.gopperman.com
 * @package go.media.fields
 * @version 0.1.0
 * @author Greg Opperman <gopperman@gmail.com>
*/

class GO_Media_Fields {
	function __construct() {
		if ( $this->editing_media_page() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			add_action( 'fm_post_page', array( $this, 'header_fields' ) );
			add_action( 'fm_post_page', array( $this, 'media_video_fields' ) );
			add_action( 'fm_post_page', array( $this, 'client_fields' ) );
		}

		if ( $this->editing_media_page() ) {
			add_action( 'admin_menu', array( $this, 'clean_page_meta_boxes' ) );
		}

		add_filter( 'fieldmanager_revision_fields', array( $this, 'register_fields_for_revision' ), 10, 1 );
	}

	function editing_media_page() {
		if ( isset( $_GET['post'] ) ) {
			$id = $_GET['post'];
			$template_slug = get_page_template_slug( $id );
			return 'template-media.php' === $template_slug;
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
					// FIXME: Fix this
					//'header_text' => new Fieldmanager_Textfield( 'Header Text' ),
					'description' => new Fieldmanager_TextArea( array(
						'label' => 'Description',
		            ) ),
					'ctas' => new Fieldmanager_Group( array(
						'label' => 'Call to Action',
						'description' => 'Calls to action are buttons that link to posts or pages that you want to promote on the homepage header.',
						'minimum_count' => 1,
						'extra_elements' => 0,
						'required' => true,
						'limit' => 1,
						'children' => array(
							'text' => new Fieldmanager_Textfield( 'CTA Text' ),
						),
					) ),
				),
			) );
			$header->add_meta_box( 'Header Area', 'page', 'normal', 'high' );
		}
	}

	function client_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$clients = new Fieldmanager_Group( array(
				'name' => 'media_clients',
				'children' => array(
					'header_text' => new Fieldmanager_Textfield( 'Header Text' ),
					'image' => new Fieldmanager_Media( array(
						'label' => 'Client Logo',
						'description' => 'A grayscale image of the client logo with a transparent background',
						'minimum_count' => 1,
						'limit' => 0,
						'add_more_label' => 'Add another logo',
						'required' => true,
						'mime_type' => 'image',
					) ),
				),
			) );
			$clients->add_meta_box( 'Client Logos', 'page', 'normal', 'high' );
		}
	}

	/**
	 * Add FM fields to page
	 *
	 * @return void
	 */
	function media_video_fields() {
		$videos = new Fieldmanager_Textfield( array(
			'name' => 'media_videos',
			'label' => 'Video Link',
			'description' => 'Paste in links to videos that are supported by wordpress embedding. For more info: https://codex.wordpress.org/Embeds',
			'minimum_count' => 1,
			'extra_elements' => 0,
			'required' => true,
			'limit' => 12,
		) );
		$videos->add_meta_box( 'Media Videos', 'page', 'normal', 'high' );
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
		$fields['page']['media_videos'] = 'Media Videos';
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

new GO_Media_Fields;
