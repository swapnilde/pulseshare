<?php
/**
 * Helper functions for the plugin.
 *
 * @link       https://swapnild.com
 * @since      1.0.0
 * @package    PulseShare
 */

namespace PulseShare\includes;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Helper functions for the plugin.
 */
class Helper {

	/**
	 * Check if the spotify client id and secret are set.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return boolean True if empty.
	 */
	public static function check_pulseshareapi_keys_empty() {
		$pulseshare_options      = get_option( 'pulseshare_options' );
		$pulseshareclient_id     = $pulseshare_options['pulseshare_client_id'] ?? '';
		$pulseshareclient_secret = $pulseshare_options['pulseshare_client_secret'] ?? '';

		return empty( $pulseshareclient_id ) || empty( $pulseshareclient_secret );
	}

	/**
	 * Get the spotify access token.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return string Access token.
	 */
	public static function get_pulseshareaccess_token() {
		$access_token = get_transient( 'pulseshare_access_token' );

		if ( ! empty( $access_token ) ) {
			return $access_token;
		}

		$pulseshare_options = get_option( 'pulseshare_options' );
		$client_id          = $pulseshare_options['pulseshare_client_id'] ?? '';
		$client_secret      = $pulseshare_options['pulseshare_client_secret'] ?? '';

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return '';
		}

		$token_data = wp_remote_post(
			'https://accounts.spotify.com/api/token',
			array(
				'body' => array(
					'grant_type'    => 'client_credentials',
					'client_id'     => $client_id,
					'client_secret' => $client_secret,
				),
			)
		);

		if ( is_wp_error( $token_data ) ) {
			return '';
		}

		$status_code = wp_remote_retrieve_response_code( $token_data );
		if ( 200 !== $status_code ) {
			return '';
		}

		$parsed_response = json_decode( wp_remote_retrieve_body( $token_data ) );

		if ( empty( $parsed_response ) || ! isset( $parsed_response->access_token, $parsed_response->expires_in ) ) {
			return '';
		}

		set_transient( 'pulseshare_access_token', $parsed_response->access_token, $parsed_response->expires_in );

		return $parsed_response->access_token;
	}

	/**
	 * Get the spotify episodes.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return  array    Episodes.
	 */
	public static function get_pulseshareall_episodes() {
		$pulseshare_options = get_option( 'pulseshare_options' );
		$pulseshareshow_id  = $pulseshare_options['pulseshare_show_id'] ?? '';

		if ( empty( $pulseshareshow_id ) ) {
			return array();
		}

		$access_token = self::get_pulseshareaccess_token();

		if ( empty( $access_token ) ) {
			return array();
		}

		$url  = 'https://api.spotify.com/v1/shows/' . $pulseshareshow_id . '/episodes?market=US';
		$show = wp_remote_get(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			)
		);

		if ( is_wp_error( $show ) ) {
			return array();
		}

		$episodes = json_decode( wp_remote_retrieve_body( $show ) );

		if ( empty( $episodes ) || ! isset( $episodes->items ) || ! is_array( $episodes->items ) ) {
			return array();
		}

		$episodes_array = array();
		foreach ( $episodes->items as $episode ) {
			if ( isset( $episode->id, $episode->name ) ) {
				$episodes_array[ $episode->id ] = $episode->name;
			}
		}

		return $episodes_array;
	}

	/**
	 * Get the spotify show tracks.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return  array    Show tracks.
	 */
	public static function get_pulseshareshow_tracks() {
		$pulseshare_options = get_option( 'pulseshare_options' );
		$pulseshareshow_id  = $pulseshare_options['pulseshare_album_id'] ?? '';

		if ( empty( $pulseshareshow_id ) ) {
			return array();
		}

		$access_token = self::get_pulseshareaccess_token();

		if ( empty( $access_token ) ) {
			return array();
		}

		$url  = 'https://api.spotify.com/v1/albums/' . $pulseshareshow_id . '/tracks?market=US';
		$show = wp_remote_get(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			)
		);

		if ( is_wp_error( $show ) ) {
			return array();
		}

		$tracks = json_decode( wp_remote_retrieve_body( $show ) );

		if ( empty( $tracks ) || ! isset( $tracks->items ) || ! is_array( $tracks->items ) ) {
			return array();
		}

		$tracks_array = array();
		foreach ( $tracks->items as $track ) {
			if ( isset( $track->id, $track->name ) ) {
				$tracks_array[ $track->id ] = $track->name;
			}
		}

		return $tracks_array;
	}

	/**
	 * Register menu, submenu, options pages .
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return array Array of pages configuration.
	 */
	public static function get_options_page() {

		// Page.
		$panel_args = array(
			'title'           => 'PulseShare',
			'option_name'     => 'pulseshare_options',
			'slug'            => 'pulseshare-options-panel',
			'user_capability' => 'manage_options',
			'tabs'            => array(
				'pulseshare-api-tab'         => esc_html__( 'API Keys', 'pulseshare' ),
				'pulseshare-integration-tab' => esc_html__( 'Integrations', 'pulseshare' ),
			),
			'icon_url'        => 'dashicons-pulsesharemenu_icon',
			'position'        => '59.1',
		);

		// Settings.
		$panel_settings = array(
			// Tab 1.
			'pulseshare_client_id'     => array(
				'label'       => esc_html__( 'Client ID', 'pulseshare' ),
				'type'        => 'text',
				'description' => '',
				'tab'         => 'pulseshare-api-tab',
			),
			'pulseshare_client_secret' => array(
				'label'       => esc_html__( 'Client Secret', 'pulseshare' ),
				'type'        => 'text',
				'description' => '',
				'tab'         => 'pulseshare-api-tab',
			),
			// Tab 2.
			'pulseshare_show_id'       => array(
				'label'       => esc_html__( 'Podcast Show ID', 'pulseshare' ),
				'type'        => 'text',
				'description' => '',
				'tab'         => 'pulseshare-integration-tab',
			),
			'pulseshare_album_id'      => array(
				'label'       => esc_html__( 'Album ID', 'pulseshare' ),
				'type'        => 'text',
				'description' => '',
				'tab'         => 'pulseshare-integration-tab',
			),
		);

		return array(
			'args'     => $panel_args,
			'settings' => $panel_settings,
		);
	}
}
