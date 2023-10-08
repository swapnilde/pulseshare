<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://swapnild.com
 * @since      1.0.0
 * @package    Spotify_Wordpress_Elementor
 */

namespace SpotifyWPE\Admin;

use SpotifyWPE\Includes\Options\SFWEOptionsPanel;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin-specific functionality of the plugin.
 */
class SpotifyWordpressElementorAdmin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$options_panel = $this->get_options_page();
		new SFWEOptionsPanel( $options_panel['args'], $options_panel['settings'] );

		if ( $this->check_spotify_api_keys_empty() ) {
			add_action( 'admin_notices', array( $this, 'spotify_api_keys_empty_notice' ) );
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param string $hook Name of the hook.
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		wp_enqueue_style( $this->plugin_name, SPOTIFY_WORDPRESS_ELEMENTOR_URLPATH . 'assets/admin/css/spotify-wordpress-elementor-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook Name of the hook.
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		wp_enqueue_script( $this->plugin_name . '-manifest', SPOTIFY_WORDPRESS_ELEMENTOR_URLPATH . 'assets/manifest.js', array(), $this->version, array( 'strategy'  => 'defer', 'in_footer' => true ) );

		wp_enqueue_script( $this->plugin_name . '-vendor', SPOTIFY_WORDPRESS_ELEMENTOR_URLPATH . 'assets/vendor.js', array(), $this->version, array( 'strategy'  => 'defer', 'in_footer' => true ) );

		wp_enqueue_script( $this->plugin_name, SPOTIFY_WORDPRESS_ELEMENTOR_URLPATH . 'assets/admin/js/spotify-wordpress-elementor-admin.js', array( 'jquery' ), $this->version, array( 'strategy'  => 'defer', 'in_footer' => true ) );

		wp_localize_script(
			$this->plugin_name,
			'SpotifyWPEAdminVars',
			array(
				'home_url'    => get_home_url(),
				'site_url'    => esc_url_raw( get_site_url() ),
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'rest_url'    => esc_url_raw( get_rest_url() ),
				'user'        => wp_get_current_user(),
				'user_avatar' => get_avatar_url( wp_get_current_user()->ID ),
				'sfwe_options' => array(
					'client_id'     => $this->check_spotify_api_keys_empty() ? '' : get_option( 'sfwe_options' )['sfwe_client_id'],
					'client_secret' => $this->check_spotify_api_keys_empty() ? '' : get_option( 'sfwe_options' )['sfwe_client_secret'],
					'show_id'       => get_option( 'sfwe_options' )['sfwe_show_id'],
					'album_id'      => get_option( 'sfwe_options' )['sfwe_album_id'],
				),
			)
		);

	}

	/**
	 * Register menu, submenu, options pages .
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return array Array of pages configuration.
	 */
	private function get_options_page() {

		// Page.
		$panel_args = array(
			'title'           => 'Spotify For WP',
			'option_name'     => 'sfwe_options',
			'slug'            => 'sfwe-options-panel',
			'user_capability' => 'manage_options',
			'tabs'            => array(
				'sfwe-api-tab'         => esc_html__( 'API Keys', 'sfwe' ),
				'sfwe-integration-tab' => esc_html__( 'Integrations', 'sfwe' ),
			),
			'icon_url'        => 'dashicons-easyproposal_admin_menu_icon',
			'position'        => '59.1',
		);

		// Settings.
		$panel_settings = array(
			// Tab 1.
			'sfwe_client_id'     => array(
				'label'       => esc_html__( 'Client ID', 'sfwe' ),
				'type'        => 'text',
				'description' => '',
				'tab'         => 'sfwe-api-tab',
			),
			'sfwe_client_secret' => array(
				'label'       => esc_html__( 'Client Secret', 'sfwe' ),
				'type'        => 'text',
				'description' => '',
				'tab'         => 'sfwe-api-tab',
			),
			// Tab 2.
			'sfwe_show_id'       => array(
				'label'       => esc_html__( 'Podcast Show ID', 'sfwe' ),
				'type'        => 'text',
				'description' => '',
				'tab'         => 'sfwe-integration-tab',
			),
			'sfwe_album_id'      => array(
				'label'       => esc_html__( 'Album ID', 'sfwe' ),
				'type'        => 'text',
				'description' => '',
				'tab'         => 'sfwe-integration-tab',
			),
		);

		return array(
			'args'     => $panel_args,
			'settings' => $panel_settings,
		);
	}

	/**
	 * Check if the spotify client id and secret are set.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return boolean True if empty.
	 */
	public function check_spotify_api_keys_empty() {
		$sfwe_options           = get_option( 'sfwe_options' );
		$spotify_client_id      = $sfwe_options['sfwe_client_id'] ?? '';
		$spotify_client_secret  = $sfwe_options['sfwe_client_secret'] ?? '';

		return empty( $spotify_client_id ) || empty( $spotify_client_secret );
	}

	/**
	 * Display notice if the spotify client id and secret are empty.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return void
	 */
	public function spotify_api_keys_empty_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p>
				<?php
				printf(
					/* translators: 1: Plugin name 2: Settings page link */
					esc_html__( '%1$sPlease set the Spotify Client ID and Client Secret in the %2$s.', 'sfwe' ),
					sprintf(
						'<strong>%1$s</strong>',
						esc_html__( 'Spotify For Wordpress: ', 'sfwe' )
					),
					sprintf(
						'<a href="%1$s">%2$s</a>',
						admin_url( 'admin.php?page=sfwe-options-panel' ),
						esc_html__( 'settings page', 'sfwe' )
					)
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Add block categories.
	 *
	 * @param array  $block_categories Array of categories.
	 * @param object $editor_context Post object.
	 * @since    1.0.0
	 * @return array Array of categories.
	 */
	public function add_block_categories( $block_categories, $editor_context ) {
		$block_categories[] = array(
			'slug'  => 'spotify-wordpress-elementor',
			'title' => __( 'Spotify For Wordpress', 'sfwe' ),
		);

		return $block_categories;
	}

	/**
	 * Register block script.
	 *
	 * @since    1.0.0
	 */
	public function register_block_script() {
		if ( ! $this->check_spotify_api_keys_empty() ) {
			register_block_type( SPOTIFY_WORDPRESS_ELEMENTOR_DIRPATH . 'assets/admin/blocks/list-embed' );
			register_block_type( SPOTIFY_WORDPRESS_ELEMENTOR_DIRPATH . 'assets/admin/blocks/album-embed' );
		}
	}

}
