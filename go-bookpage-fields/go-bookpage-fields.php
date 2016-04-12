<?php
/**
 * Plugin Name: GO Book Page Fields
 * Plugin URI: http://gopperman.com
 * Description: Basic Fields For A Webpage about a Book
 * Version: 0.1
 * Author: Greg Opperman
 * Author URI: http://www.gopperman.com
 * @package go.bookpage.fields
 * @version 0.1.0
 * @author Greg Opperman <gopperman@gmail.com>
*/

class GO_Bookpage_Fields {
	function __construct() {
		if ( $this->editing_bookpage() ) {
			add_action( 'fm_post_page', array( $this, 'header_fields' ) );
			add_action( 'fm_post_page', array( $this, 'excerpt_fields' ) );
			add_action( 'fm_post_page', array( $this, 'testimonial_fields' ) );
			add_action( 'fm_post_page', array( $this, 'worksheet_fields' ) );
		}

		add_filter( 'fieldmanager_revision_fields', array( $this, 'register_fields_for_revision' ), 10, 1 );
	}

	function editing_bookpage() {
		if ( isset( $_GET['post'] ) ) {
			$id = $_GET['post'];
			$template_slug = get_page_template_slug( $id );
			return 'template-book.php' === $template_slug;
		} else {
			// You must be doing AJAX or something
			return true;
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
				'name' => 'content_header',
				'children' => array(
					'image' => new Fieldmanager_Media( array(
						'label' => 'Book Image',
						'required' => true,
						'mime_type' => 'image',
					) ),
					'description' => new Fieldmanager_RichTextArea( array(
						'label' => 'Description',
				        'buttons_1' => array( 'bold', 'italic', 'link' ),
						'buttons_2' => array(),
				        'editor_settings' => array(
				        	'quicktags' => false,
				            'media_buttons' => false,
			            ),
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
							'link' => new Fieldmanager_Link( array(
								'label' => 'Enter URL:',
							) ),
						),
					) ),
				),
			) );
			$header->add_meta_box( 'Header Area', 'page', 'normal', 'high' );
		}
	}

	function excerpt_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$excerpts = new Fieldmanager_Group( array(
				'name' => 'book_excerpts',
				'minimum_count' => 0,
				'extra_elements' => 0,
				'required' => true,
				'limit' => 6,
				'sortable' => true,
				'add_more_label' => 'Add Excerpt',
				'children' => array(
					'chapter' => new Fieldmanager_TextField( 'Chapter Number' ),
					'excerpt_link' => new Fieldmanager_Autocomplete( array(
						'label' => 'Find an excerpt Post',
						'datasource' => new Fieldmanager_Datasource_Post( array(
							'query_args' => array( 'post_type' => array( 'post' ) ),
						) ),
					) ),
				),
			) );
			$excerpts->add_meta_box( 'Excerpt Links', 'page', 'normal', 'high' );
		}
	}

	function testimonial_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$content = new Fieldmanager_Group( array(
				'name' => 'book_testimonials',
				'label' => 'Testimonial',
				'description' => 'Testimonials and/or praise for your book',
				'minimum_count' => 1,
				'extra_elements' => 0,
				'required' => true,
				'limit' => 3,
				'sortable' => true,
				'add_more_label' => 'Add Testimonial',
				'children' => array(
					'testimonial' => new Fieldmanager_RichTextArea( array(
				        'name' => 'testimonial',
				        'buttons_1' => array( 'bold', 'italic', 'link' ),
						'buttons_2' => array(),
				        'editor_settings' => array(
				        	'quicktags' => false,
				            'media_buttons' => false,
			            ),
		            ) ),
				),
			) );
			$content->add_meta_box( 'Content Areas', 'page', 'normal', 'high' );
		}
	}

	function worksheet_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$worksheet_instructions = new Fieldmanager_Textfield( array(
				'name' => 'book_worksheet_instructions',
				'label' => 'One-line Worksheet Instruction',
			) );
			$worksheets = new Fieldmanager_Group( array(
				'name' => 'book_worksheets',
				'minimum_count' => 0,
				'extra_elements' => 0,
				'required' => true,
				'limit' => 4,
				'sortable' => true,
				'add_more_label' => 'Add Worksheet',
				'children' => array(
					'worksheet_link' => new Fieldmanager_Autocomplete( array(
						'label' => 'Find a Worksheet Post',
						'datasource' => new Fieldmanager_Datasource_Post( array(
							'query_args' => array( 'post_type' => array( 'post' ) ),
						) ),
					) ),
				),
			) );
			$worksheet_instructions->add_meta_box( 'Worksheet Instructions', 'page', 'normal', 'low' );
			$worksheets->add_meta_box( 'Worksheet Links', 'page', 'normal', 'low' );
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
	function register_fields_for_revision( $fields ) {
		$fields['page']['book_excerpts'] = 'Book Excerpts';
		$fields['page']['book_testimonials'] = 'Book Testimonials';
		$fields['page']['book_worksheets'] = 'Book Worksheets';
		return $fields;
	}
}

new GO_Bookpage_Fields;
