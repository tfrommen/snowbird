<?php

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly
endif;

if ( ! class_exists( 'Snowbird' ) ) :

	final class Snowbird {
		protected static $instance = null;

		public static function instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
		}

		/**
		 * Theme Default Mods.
		 *
		 * @return array
		 */
		public static function mods() {
			$color_scheme = snowbird_get_color_scheme();

			return apply_filters( 'snowbird_default_mods', array(
				/**
				 * Site Identity
				 */
				'logo_image'               => '',
				'logo_image_2x'            => '',
				/**
				 * Theme Settings
				 */
				'site_sidebar_type'        => 'left',
				/**
				 * Loop - Posts Listing
				 */
				'loop_content'             => 'full',
				'loop_excerpt_length'      => 55,
				/**
				 * Post (Single)
				 */
				'post_display_author_bio'  => 1,
				'post_display_share_this'  => 1,
				'post_display_navigation'  => 1,
				'post_display_related'     => 1,
				/**
				 * Page
				 */
				'page_display_share_this'  => 0,
				/**
				 * Footer
				 */
				'footer_widget_area'       => 'one-third',
				'footer_menu_location'     => 'social',
				/**
				 * Scheme
				 */
				'color_scheme'             => 'default',
				/**
				 * Colors - Header
				 */
				'header_text_color'        => $color_scheme['header_text_color'],
				'header_background_color'  => $color_scheme['header_background_color'],
				/**
				 * Colors - Content
				 */
				'content_title_color'      => $color_scheme['content_title_color'],
				'content_text_color'       => $color_scheme['content_text_color'],
				'content_alt_text_color'   => $color_scheme['content_alt_text_color'],
				'content_accent_color'     => $color_scheme['content_accent_color'],
				'content_background_color' => $color_scheme['content_background_color'],
				/**
				 * Colors - Footer
				 */
				'footer_title_color'       => $color_scheme['footer_title_color'],
				'footer_text_color'        => $color_scheme['footer_text_color'],
				'footer_alt_text_color'    => $color_scheme['footer_alt_text_color'],
				'footer_accent_color'      => $color_scheme['footer_accent_color'],
				'footer_background_color'  => $color_scheme['footer_background_color'],
				/**
				 * Colors - Button
				 */
				'button_text_color'        => $color_scheme['button_text_color'],
				'button_background_color'  => $color_scheme['button_background_color'],
				/**
				 * Header Image
				 */
				'header_overlay_color'     => '#000000',
				'header_overlay_opacity'   => 30,
			) );
		}

		/**
		 * Theme Default Options.
		 *
		 * @return mixed|null|void
		 */
		public static function options() {

			return apply_filters( 'snowbird_default_options', array(
				'footer_text' => '&copy; %CUR_YEAR% %SITE_LINK%. Powered by %WP_LINK%.',
				'custom_css'  => '',
			) );
		}

		/**
		 * Default Theme Mod value.
		 *
		 * @param $name
		 *
		 * @return mixed|null|void
		 */
		public static function mod_default( $name ) {
			$def = self::mods();


			if ( isset( $def[ $name ] ) ) {
				return apply_filters( "snowbird_theme_mod_{$name}", $def[ $name ] );
			}

			return null;
		}

		/**
		 * Wrapper function for get_theme_mod.
		 *
		 * @param $name
		 * @param bool|false $default
		 *
		 * @return mixed|string|void
		 */
		public static function mod( $name, $default = false ) {
			// Look into WordPress
			if ( false !== get_theme_mod( $name ) ) {
				return get_theme_mod( $name );
			}

			// Look into Theme Defaults
			$def = self::mods();

			if ( isset( $def[ $name ] ) ) {
				return apply_filters( "snowbird_theme_mod_{$name}", $def[ $name ] );
			}

			unset( $def );

			return apply_filters( "snowbird_theme_mod_{$name}", $default );
		}

		/**
		 * Default Theme Option value.
		 *
		 * @param $name
		 *
		 * @return mixed|null|void
		 */
		public static function option_default( $name ) {
			$def = self::options();

			if ( isset( $def[ $name ] ) ) {
				return apply_filters( "snowbird_option_{$name}", $def[ $name ] );
			}

			return null;
		}

		/**
		 * Current Theme Settings Key
		 *
		 * @return string
		 */
		public static function option_key( $name = null ) {
			$codename = self::codename( 'settings' );

			return ! is_null( $name ) ? $codename . '[' . sanitize_title_with_dashes( $name ) . ']' : $codename;
		}

		/**
		 * Helper function to get theme option.
		 *
		 * @param $name
		 * @param bool|false $default
		 *
		 * @return mixed|void
		 */
		public static function option( $name, $default = false ) {

			$options = get_option( self::option_key() );

			if ( isset( $options[ $name ] ) ) {
				return $options[ $name ];
			}

			// Look into Theme Defaults
			$def = self::options();

			if ( isset( $def[ $name ] ) ) {
				return apply_filters( "snowbird_option_{$name}", $def[ $name ] );
			}

			unset( $def );

			return apply_filters( "snowbird_option_{$name}", $default );
		}

		/**
		 * Current Theme Name
		 *
		 * @param null $key
		 *
		 * @return string
		 */
		public static function codename( $key = null ) {
			if ( wp_get_theme()->parent() ) {
				$codename = wp_get_theme()->parent()->get( 'Name' ) . '-' . wp_get_theme()->get( 'Name' );
			} else {
				$codename = wp_get_theme()->get( 'Name' );
			}

			$codename = sanitize_title_with_dashes( $codename );

			return ! is_null( $key ) ? $codename . '-' . sanitize_title_with_dashes( $key ) : $codename;
		}


		/**
		 * Current Theme Version value to enqueue CSS/JS.
		 *
		 * @return string
		 */
		public static function version() {
			return wp_get_theme()->get( 'Version' );
		}

		//
		// Helpers
		//

		/**
		 * Helper to prepare name for generating cache.
		 *
		 * @param $group
		 * @param $key
		 *
		 * @return string
		 */
		public static function cache_key( $group, $key ) {
			return substr( self::codename(), 0, 6 ) . '-' . substr( $group, 0, 4 ) . '-' . md5( self::version() . $key );
		}

		/**
		 * Helper to prepare group name for cache.
		 *
		 * @return string
		 */
		public static function cache_group() {
			return self::codename() . '-' . self::version();
		}

		/**
		 * Helper function for convert Opacity to float.
		 *
		 * @param $value
		 *
		 * @return float|mixed
		 */
		public static function css_opacity( $value ) {
			$value = intval( $value );
			$value = min( $value, 100 );

			return 0 < $value ? ( $value / 100 ) : $value;
		}

		/**
		 * Wrapper function for Schema less URL.
		 *
		 * @param $url
		 *
		 * @return string
		 */
		public static function protocol( $url ) {
			$url = str_replace( array( 'http://', 'https://' ), '//', $url );

			return esc_url( $url );
		}

		/**
		 * Helper function to convert Colors.
		 *
		 * @param $color
		 *
		 * @return array
		 */
		public static function hex_to_rgb( $color ) {
			$color = trim( $color, '#' );

			if ( 3 == strlen( $color ) ) {
				$r = hexdec( substr( $color, 0, 1 ) . substr( $color, 0, 1 ) );
				$g = hexdec( substr( $color, 1, 1 ) . substr( $color, 1, 1 ) );
				$b = hexdec( substr( $color, 2, 1 ) . substr( $color, 2, 1 ) );
			} else if ( 6 == strlen( $color ) ) {
				$r = hexdec( substr( $color, 0, 2 ) );
				$g = hexdec( substr( $color, 2, 2 ) );
				$b = hexdec( substr( $color, 4, 2 ) );
			} else {
				return array();
			}

			return array( 'red' => $r, 'green' => $g, 'blue' => $b );
		}

		/**
		 * Helper function for CSS RGBA values.
		 *
		 * @param $color
		 * @param $opacity
		 *
		 * @return string|void
		 */
		public static function rgba( $color, $opacity ) {
			$array = self::hex_to_rgb( $color );
			$alpha = self::css_opacity( $opacity );

			if ( empty ( $array ) ) {
				return;
			}

			$array['opacity'] = $alpha;

			return vsprintf( 'rgba( %1$s, %2$s, %3$s, %4$1.2f)', $array );
		}

		//
		// Data Helpers
		//

		/**
		 * Query for Related Posts.
		 *
		 * @param int $count
		 * @param int $current_post_id
		 * @param bool|true $cache
		 *
		 * @return mixed|WP_Error|WP_Query
		 */
		public static function posts_related( $count = 4, $current_post_id = 0, $cache = true ) {
			$count           = 0 < intval( $count ) ? $count : 4;
			$current_post_id = 0 < $current_post_id ? $current_post_id : get_the_ID();

			if ( ! is_single() || 1 > $current_post_id ) {
				return new WP_Error( 'skipped', __( 'No posts to show.', 'snowbird' ) );
			}

			$cache_key  = self::cache_key( 'd', 'related_posts' . $count . $current_post_id );
			$cache_time = apply_filters( 'snowbird_related_posts_cache_time', MINUTE_IN_SECONDS );

			if ( false === ( $data = get_transient( $cache_key ) ) || false === $cache ) {
				$post_cats = wp_get_object_terms( $current_post_id, 'category', array( 'fields' => 'ids' ) );
				$post_tags = wp_get_object_terms( $current_post_id, 'post_tag', array( 'fields' => 'ids' ) );

				$args = apply_filters( 'snowbird_related_posts_query', array(
					// date | rand
					'orderby'        => 'rand',
					'order'          => 'DESC',
					'post_status'    => 'publish',
					'post_type'      => 'post',
					'posts_per_page' => $count,
					'paged'          => 1,
					'post__not_in'   => array( $current_post_id ),
					// Exclude current post
					'has_password'   => false,
					// has_password true means posts with passwords, false means posts without.
					'no_found_rows'  => true,
					'tax_query'      => array(
						'relation' => 'OR',
						array(
							'taxonomy' => 'category',
							'field'    => 'id',
							'terms'    => $post_cats,
							'operator' => 'IN',
						),
						array(
							'taxonomy' => 'post_tag',
							'field'    => 'id',
							'terms'    => $post_tags,
							'operator' => 'IN',
						),
					),
				) );

				$data = new WP_Query( $args );

				set_transient( $cache_key, $data, $cache_time );
			}

			return $data;
		}

		/**
		 * Helper function to Get image data based on image url.
		 *
		 * @param $url
		 *
		 * @return array|string
		 */
		public static function url_to_image_data( $url ) {
			$data = '';

			if ( $id = attachment_url_to_postid( $url ) ) {
				$thumb = wp_get_attachment_image_src( $id );

				if ( $thumb ) {

					$data = array(
						'id'     => absint( $id ),
						'url'    => esc_url_raw( $thumb[0] ),
						'width'  => absint( $thumb[1] ),
						'height' => absint( $thumb[2] )
					);

				}

				unset( $thumb );
				unset( $id );
			}

			return $data;
		}

		//
		// Conditional Helpers
		//

		/**
		 * Helper function to Identify Browser/OS.
		 *
		 * @param $query
		 *
		 * @return bool
		 */
		public static function is_current_agent( $query ) {
			if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
				return false;
			}

			preg_match( "/iPhone|iPad|iPod|Android|webOS|Safari|Chrome|Firefox|Opera|MSIE/", $_SERVER['HTTP_USER_AGENT'], $matches );

			$_agent = null;
			$agent  = current( $matches );

			switch ( $agent ) {
				case 'iPhone':
				case 'iPad':
				case 'iPod':
					$_agent = 'iOS';
					break;
				case 'MSIE':
					$_agent = 'IE';
					break;
			}

			switch ( $agent ) {
				case 'iPhone':
				case 'iPad':
				case 'iPod':
				case 'Android':
				case 'webOS':
				case 'Safari':
				case 'Chrome':
				case 'Firefox':
				case 'Opera':
					break;
			}

			return ( strtolower( $query ) == strtolower( $_agent ) || strtolower( $query ) == strtolower( $agent ) );
		}

		/**
		 * Helper function to Identify WooCommerce.
		 *
		 * @return bool
		 */
		public static function is_woo_commerce() {
			if ( ! class_exists( 'WC', false ) ) {
				return false;
			}

			return true;
		}

		//
		// Debug Helpers
		//

		public static function esc_array( &$item, $key ) {
			$item = is_string( $item ) ? esc_html( $item ) : $item;
		}

		public static function print_pre( $var, $esc = true ) {
			if ( $esc && is_array( $var ) ) {
				array_walk_recursive( $var, array( __CLASS__, 'esc_array' ) );
			} elseif ( $esc && is_string( $var ) ) {
				$var = esc_html( $var );
			}

			print '<pre>';
			print_r( $var );
			print '</pre>';
		}

		public static function print_dump( $var, $esc = true ) {
			if ( $esc && is_array( $var ) ) {
				array_walk_recursive( $var, array( __CLASS__, 'esc_array' ) );
			} elseif ( $esc && is_string( $var ) ) {
				$var = esc_html( $var );
			}

			print '<pre>';
			var_dump( $var );
			print '</pre>';
		}
	}

endif;


/**
 * Returns the main instance of Snowbird
 *
 * @return Snowbird instance.
 */
function Snowbird() {
	return Snowbird::instance();
}

Snowbird();
