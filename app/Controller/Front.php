<?php
namespace StarterKit\Controller;

use StarterKit\Helper\Assets;
use StarterKit\Helper\Utils;

/**
 * Front controller
 *
 * Controller which loading only on front (pages, posts etc)
 * contains all needed additional hooks,methods
 *
 * @category   Wordpress
 * @package    Starter Kit Backend
 * @author     SolidBunch
 * @link       https://solidbunch.com
 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */
class Front {

	/**
	 * Constructor - add all needed actions
	 *
	 * @return void
	 **/
	public function __construct() {

		// add site icon
		add_action( 'wp_head', array( $this, 'add_site_icon' ) );

		// load assets
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
		// remove default styles for Unyson Breadcrummbs
		add_action( 'wp_enqueue_scripts', array( $this, 'remove_assets' ), 99, 1 );
		add_action( 'wp_footer', array( $this, 'remove_assets' ) );

		// Change excerpt dots
		add_filter( 'excerpt_more', array( $this, 'change_excerpt_more' ) );

		// remove jquery migrate for optimization reasons
		add_filter( 'wp_default_scripts', array( $this, 'dequeue_jquery_migrate' ) );

		// Anti-spam
		add_action( 'phpmailer_init', array( $this, 'antispam_form' ) );

		// add GTM
		add_action( 'wp_head', array( $this, 'add_gtm_head' ) );
		add_action( 'wp_footer', array( $this, 'add_gtm_body' ) );

	}

	/**
	 * Add site icon from customizer
	 *
	 * @return void
	 **/
	public function add_site_icon() {

		if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
			wp_site_icon();
		}

	}

	/**
	 * Load JavaScript and CSS files in a front-end
	 *
	 * @return void
	 **/
	public function load_assets() {

		// add support for visual composer animations, row stretching, parallax etc
		if ( function_exists( 'vc_asset_url' ) ) {
			wp_enqueue_script(
				'waypoints',
				vc_asset_url( 'lib/waypoints/waypoints.min.js' ),
				array( 'jquery' ),
				WPB_VC_VERSION,
				true
			);
			wp_enqueue_script(
				'wpb_composer_front_js',
				vc_asset_url( 'js/dist/js_composer_front.min.js' ),
				array( 'jquery' ),
				WPB_VC_VERSION,
				true
			);
		}

		// JS scripts
		Assets::enqueue_script('jquery');
		Assets::enqueue_script(
			'google-fonts',
			'/assets/libs/google-fonts/webfont.js',
			false,
			false,
			true
		);
		Assets::enqueue_script_dist('starter-kit-front', 'app.min.js', array( 'jquery', 'google-fonts' ));

		$js_vars = array(
			'ajaxurl'    => esc_url( admin_url( 'admin-ajax.php' ) ),
			'assetsPath' => get_template_directory_uri() . '/assets',
		);

		Assets::enqueue_script( 'starter-kit-front' );
		wp_localize_script( 'starter-kit-front', 'themeJsVars', $js_vars );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			Assets::enqueue_script( 'comment-reply' );
		}

		if ( $this->antispam_enabled() === 1 ) {
			Assets::enqueue_script(
				'starter-kit-antispam',
				'/assets/js/antispam.js',
				array(
					'jquery',
				),
				Starter_Kit()->config['cache_time'], true
			);
		}


		// CSS styles
		Assets::enqueue_style_dist('starter-kit-libs', 'libs.css');
		Assets::enqueue_style_dist('starter-kit-front', 'front.css');

	}

	/**
	 * Check if anti-spam enabled in theme options
	 *
	 * @return int
	 */
	public function antispam_enabled() {
		return (int) Utils::get_option( 'forms_antispam', 0 );
	}

	/**
	 * Remove no needed default js and styles
	 *
	 * @return void
	 */
	public function remove_assets() {

		// disable huge default JS composer styles
		wp_dequeue_style( 'js_composer_front' );
		wp_dequeue_style( 'animate-css' );
		//wp_dequeue_style( 'fw-ext-breadcrumbs-add-css' );

	}

	/**
	 * Change excerpt More text
	 *
	 * @param $more
	 *
	 * @return string
	 */
	public function change_excerpt_more( $more ) {
		return '…';
	}

	/**
	 * Remove jquery migrate for optimization reasons
	 *
	 * @param $scripts
	 */
	public function dequeue_jquery_migrate( $scripts ) {
		if ( ! is_admin() ) {
			$scripts->remove( 'jquery' );
			$scripts->add( 'jquery', false, array( 'jquery-core' ), '1.10.2' );
		}
	}

	/**
	 * @param \PHPMailer $phpmailer
	 *
	 * @return null|\PHPMailer
	 */
	public function antispam_form( \PHPMailer $phpmailer ) {

		if ( $this->antispam_enabled() !== 1 ) {
			return null;
		}

		if ( ! empty( $_POST ) && empty( $_POST['as_code'] ) ) {
			$phpmailer->clearAllRecipients();
		}

		return $phpmailer;
	}


	/**
	 * Load Google Tag Manager
	 **/
	public function add_gtm_head() {
		$tag_manager_code = Utils::get_option( 'tag_manager_code', '' );
		$site_url         = get_site_url();

		if ( ! empty( $tag_manager_code ) && strpos( $site_url, 'wpengine.com' ) === false ) {

			Starter_Kit()->View->load( '/template-parts/gtm',
				array( 'head' => true, 'tag_manager_code' => $tag_manager_code ) );

		}
	}

	/**
	 * add GTM after open <body> tag
	 */
	public function add_gtm_body() {
		$tag_manager_code = Utils::get_option( 'tag_manager_code', '' );
		$site_url         = get_site_url();

		if ( ! empty( $tag_manager_code ) && strpos( $site_url, 'wpengine.com' ) === false ) {

			Starter_Kit()->View->load( '/template-parts/gtm',
				array( 'head' => false, 'tag_manager_code' => $tag_manager_code ) );

		}
	}

}
