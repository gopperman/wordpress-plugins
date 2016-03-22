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
		$template_slug = ( isset( $_GET['post'] ) ) ? get_page_template_slug( $_GET['post'] ) : null;
		if ( 'template-book.php' === $template_slug || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			add_action( 'fm_post_page', array( $this, 'excerpt_fields' ) );
			add_action( 'fm_post_page', array( $this, 'testimonial_fields' ) );
			add_action( 'fm_post_page', array( $this, 'worksheet_fields' ) );
		}

		add_filter( 'fieldmanager_revision_fields', array( $this, 'register_fields_for_revision' ), 10, 1 );
	}

	function exerpt_fields() {
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
						'label' => 'Find a Worksheet Post',
						'datasource' => new Fieldmanager_Datasource_Post( array(
							'query_args' => array( 'post_type' => array( 'post' ) ),
						) ),
					) ),
				),
			) );
			$excerpts->add_meta_box( 'Excerpt Links', 'page', 'side', 'high' );
		}
	}

	function testimonial_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
			$content = new Fieldmanager_Group( array(
				'name' => 'book_testimonials',
				'label' => 'Content Area',
				'description' => 'Testimonials and/or praise for your book',
				'minimum_count' => 1,
				'extra_elements' => 0,
				'required' => true,
				'limit' => 3,
				'sortable' => true,
				'add_more_label' => 'Add Testimonial',
				'children' => array(
					'testimonial' => new Fieldmanager_TextArea( 'Testimonial' ),
					'attribution' => new Fieldmanager_TextArea( 'Attribution' ),
				),
			) );
			$content->add_meta_box( 'Content Areas', 'page', 'normal', 'high' );
		}
	}

	function worksheet_fields() {
		if ( class_exists( 'Fieldmanager_Group' ) ) {
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
	function register_homepage_fields_for_revision( $fields ) {
		$fields['page']['book_excerpts'] = 'Book Excerpts';
		$fields['page']['book_testimonials'] = 'Book Testimonials';
		$fields['page']['book_worksheets'] = 'Book Worksheets';
		return $fields;
	}
}

new GO_Bookpage_Fields;
