<?php
/**
 * Plugin Name: Diary Helper
 * Description:
 * Author:      Changwoo
 * Author URI:  https://blog.changwoo.pe.kr
 * Plugin URI:  https://github.com/chwnam/diary-helper
 * Version:     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'diary_helper_get_client_ip_address' ) ) {
	function diary_helper_get_client_ip_address(): string {
		return $_SERVER['REMOTE_ADDR'] ?? '';
	}
}


if ( ! function_exists( 'diary_helper_get_whitelist' ) ) {
	function diary_helper_get_whitelist(): array {
		if ( ! defined( 'DIARY_HELPER_WHITELIST' ) ) {
			define( 'DIARY_HELPER_WHITELIST', '127.0.0.1, 192.168.10.1' );
		}

		return array_map( 'trim', explode( ',', DIARY_HELPER_WHITELIST ) );
	}
}


if ( ! function_exists( 'diary_helper_is_protected' ) ) {
	function diary_helper_is_protected(): bool {
		$ip_address = diary_helper_get_client_ip_address();
		$whitelist  = diary_helper_get_whitelist();

		$in_whitelist = in_array( $ip_address, $whitelist, true );
		$is_logged_in = is_user_logged_in();

		return ! ( $in_whitelist || $is_logged_in );
	}
}


if ( ! function_exists( 'diary_helper_is_login_url' ) ) {
	function diary_helper_is_login_url(): bool {
		$request_uri = site_url( $_SERVER['REQUEST_URI'] ?? '' );
		$login_url   = wp_login_url();

		return 0 === strpos( $request_uri, $login_url );
	}
}


if ( ! function_exists( 'diary_helper_on_init' ) ) {
	function diary_helper_on_init() {
		if ( ! is_admin() && diary_helper_is_protected() && ! diary_helper_is_login_url() ) {
			wp_safe_redirect( wp_login_url( site_url( $_SERVER['REQUEST_URI'] ?? '' ) ) );
			exit;
		}
	}
}

add_action( 'init', 'diary_helper_on_init' );
