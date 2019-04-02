<?php

namespace StarterKit\Model;

use StarterKit\Helper\Assets;
use StarterKit\Helper\Utils;

/**
 * Shortcode model
 *
 * @category   Wordpress
 * @package    Starter Kit Backend
 * @author     SolidBunch
 * @link       https://solidbunch.com
 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */
class Shortcode {

	/**
	 * Shortcode name
	 */
	public $shortcode;

	/**
	 * Shortcode config
	 */
	public $config;

	/**
	 * Shortcode directory
	 */
	public $shortcode_dir;

	/**
	 * Shortcode URI
	 */
	public $shortcode_uri;

	/**
	 * Shortcode constructor.
	 *
	 * @param $data
	 */
	public function __construct( $data = null ) {
		if ( $data ) {
			$this->shortcode     = $data['config']['base'];
			$this->shortcode_dir = $data['shortcode_dir'];
			$this->shortcode_uri = $data['shortcode_uri'];
			$this->config        = $data['config'];

			if ( Utils::is_vc() ) {
				$this->vc_shortcode();
			} else {
				$this->wp_shortcode();
			}

			// Add AJAX script
			if ( file_exists( $this->shortcode_dir . '/ajax.php' ) ) {
				require_once( $this->shortcode_dir . '/ajax.php' );
			}
		}
	}

	/**
	 * Add Visual Composer shortcode support
	 *
	 */
	public function vc_shortcode() {

		if ( class_exists( 'WPBakeryShortCode' ) && file_exists( $this->shortcode_dir . '/vc.php' ) ) {
			// Add shortcode map
			vc_map( $this->config );

			// Add shortcode to VC
			require_once( $this->shortcode_dir . '/vc.php' );
		}
	}

	/**
	 * Add native Wordpress shortcode support
	 *
	 */
	public function wp_shortcode() {
		global $shortcode_tags;

		add_shortcode( $this->shortcode, array( $this, 'content' ) );
	}

	/**
	 * Prepare atts for shortcode
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function atts( $atts ) {
		if ( ! empty( $atts['css'] ) ) {
			if ( Utils::is_vc() && function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class = trim( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,
					vc_shortcode_custom_css_class( $atts['css'], ' ' ), $this->shortcode, $atts ) );
			} else {
				$css_class = $this->custom_css_class( $atts['css'] );
				if ( ! empty( $css_class ) ) {
					Starter_Kit()->Controller->Shortcodes->custom_css[] = $atts['css'];
				}
			}
			$atts['classes'] = $css_class . ' ' . trim( ! empty( $atts['classes'] ) && trim( $atts['classes'] ) ? $atts['classes'] : '' );
		}

		return $atts;
	}

	public function custom_css_class( $param_value, $prefix = '' ) {
		$css_class = preg_match( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/',
			$param_value ) ? $prefix . preg_replace( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', '$1',
				$param_value ) : '';

		return $css_class;
	}

	/**
	 * Prepare data for shortcode view
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function data( $data ) {
		if ( empty( $data['id'] ) && ! empty( $data['atts']['el_id'] ) ) {
			$data['id'] = $data['atts']['el_id'];
		}

		return $data;
	}

	/**
	 * Parse param group atts
	 *
	 * @see vc_param_group_parse_atts()
	 *
	 * @param $atts_string
	 *
	 * @return array|mixed
	 */
	function param_group_parse_atts( $atts_string ) {
		return json_decode( urldecode( $atts_string ), true );
	}

	/**
	 * Enqueue shortcode style
	 *
	 * @see Assets::enqueue_style
	 * @param $handle
	 * @param string $src
	 * @param array $deps
	 * @param mixed $ver
	 * @param string $media
	 */
	public function enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		//wp_enqueue_style( $handle, $src, $deps, $ver, $media );
		Assets::enqueue_style( $handle, $src, $deps, $ver, $media );
	}

	/**
	 * Enqueue shortcode script
	 *
	 * @see Assets::enqueue_script
	 * @param $handle
	 * @param string $src
	 * @param array $deps
	 * @param mixed $ver
	 * @param bool $in_footer
	 */
	public function enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		//wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
		Assets::enqueue_script( $handle, $src, $deps, $ver, $in_footer );
	}

	/**
	 * Wrapper for Assets::localize_script
	 *
	 * @see Assets::localize_script
	 * @param $handle
	 * @param $object_name
	 * @param $l10n
	 */
	public function localize_script( $handle, $object_name, $l10n ) {
		Assets::localize_script( $handle, $object_name, $l10n );
	}

	/**
	 * Wrapper for Assets::add_inline_style
	 *
	 * @see Assets::add_inline_script
	 *
	 * @param $handle
	 * @param $data
	 * @param string $position
	 */
	public function add_inline_script( $handle, $data, $position = 'after' ) {
		Assets::add_inline_script( $handle, $data, $position );
	}

}
