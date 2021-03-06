<?php
namespace StarterKit\Model;

/**
 * Layout model
 *
 * @category   Wordpress
 * @package    Starter Kit Backend
 * @author     SolidBunch
 * @link       https://solidbunch.com
 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */
class Layout {

	/**
	 * Model constructor
	 */
	public function __construct() {

		add_action( 'init', function() {
			$this->register_post_type();
		}, 5 );

	}

	/**
	 * Register custom post type
	 */
	public function register_post_type() {

		register_post_type( 'composerlayout', array(
			'label'               => esc_html__( 'Header / Footer', 'starter-kit' ),
			'description'         => '',
			'public'              => true,
			'show_ui'             => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'_builtin'            => false,
			'show_in_menu'        => true,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'menu_position'       => null,
			'rewrite'             => false,
			'query_var'           => true,
			'supports'            => array( 'title', 'editor' ),
			'labels'              => array(
				'name'               => esc_html__( 'Header / Footer', 'starter-kit' ),
				'singular_name'      => esc_html__( 'Header / Footer', 'starter-kit' ),
				'menu_name'          => esc_html__( 'Header / Footer', 'starter-kit' ),
				'add_new'            => esc_html__( 'Add New Header / Footer', 'starter-kit' ),
				'add_new_item'       => esc_html__( 'Add New Header / Footer', 'starter-kit' ),
				'edit'               => esc_html__( 'Edit', 'starter-kit' ),
				'edit_item'          => esc_html__( 'Edit Header / Footer', 'starter-kit' ),
				'new_item'           => esc_html__( 'New Header / Footer', 'starter-kit' ),
				'view'               => esc_html__( 'View Header / Footer', 'starter-kit' ),
				'view_item'          => esc_html__( 'View Header / Footer', 'starter-kit' ),
				'search_items'       => esc_html__( 'Search Header / Footer', 'starter-kit' ),
				'not_found'          => esc_html__( 'No Header / Footer Found', 'starter-kit' ),
				'not_found_in_trash' => esc_html__( 'No Header / Footer Found in Trash', 'starter-kit' ),
				'parent'             => esc_html__( 'Parent Header / Footer', 'starter-kit' )
			)
		));

	}

	/**
	 * Get default composer layout
	 *
	 * @param string $layout_type
	 *
	 * @return \WP_Query
	 */
	public function get_default_layout( $layout_type = 'header' ) {
		$args                 = array(
			'post_type'      => 'composerlayout',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => '_layouttype',
					'value' => $layout_type,
				),
				array(
					'key'   => '_appointment',
					'value' => 'default',
				),

			)
		);
		$default_layout_query = new \WP_Query( $args );
		wp_reset_query();

		return $default_layout_query;
	}

	/**
	 * Get all composer layouts
	 *
	 * @param string $layout_type
	 *
	 * @return \WP_Query
	 */
	public function get_layouts( $layout_type = 'header' ) {

		$args    = array(
			'post_type'      => 'composerlayout',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				'composerlayout_layouttype' => array(
					'key'   => '_layouttype',
					'value' => $layout_type,
				),
			),
			'order'          => 'ASC',
		);
		$layouts = new \WP_Query( $args );
		wp_reset_query();

		return $layouts;
	}
}
