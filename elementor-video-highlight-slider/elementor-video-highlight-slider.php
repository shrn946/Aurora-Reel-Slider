<?php
/**
 * Plugin Name: Aurora Reel Slider for Elementor
 * Description: Custom Elementor widget for a centered autoplay video highlight slider.
 * Version: 1.0.0
 * Author: WP desing lab
 * Text Domain: evhs
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EVHS_VERSION', '1.0.0' );
define( 'EVHS_FILE', __FILE__ );
define( 'EVHS_PATH', plugin_dir_path( __FILE__ ) );
define( 'EVHS_URL', plugin_dir_url( __FILE__ ) );

final class EVHS_Plugin {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_elementor_notice' ) );
			return;
		}

		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_styles' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	public function missing_elementor_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'Aurora Reel Slider for Elementor requires Elementor to be installed and activated.', 'evhs' ) . '</p></div>';
	}

	public function register_styles() {
		wp_register_style( 'evhs-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );
		wp_register_style( 'evhs-video-slider', EVHS_URL . 'assets/css/video-slider.css', array( 'evhs-swiper' ), EVHS_VERSION );
	}

	public function register_scripts() {
		wp_register_script( 'evhs-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );
		wp_register_script( 'evhs-video-slider', EVHS_URL . 'assets/js/video-slider.js', array( 'jquery', 'elementor-frontend', 'evhs-swiper' ), EVHS_VERSION, true );
	}

	public function register_widgets( $widgets_manager ) {
		require_once EVHS_PATH . 'widgets/class-video-highlight-slider-widget.php';
		$widgets_manager->register( new \EVHS\Widgets\Video_Highlight_Slider_Widget() );
	}
}

EVHS_Plugin::instance();
