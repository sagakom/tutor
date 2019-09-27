<?php
/**
 * FormHandler class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.4.3
 */

namespace TUTOR;


if ( ! defined( 'ABSPATH' ) )
	exit;


class FormHandler {

	public function __construct() {
		add_action('tutor_action_tutor_user_login', array($this, 'process_login'));
		add_action('tutor_action_tutor_retrieve_password', array($this, 'tutor_retrieve_password'));

		add_action( 'tutor_reset_password_notification', array( $this, 'reset_password_notification' ), 10, 2 );
	}

	public function process_login(){
		tutils()->checking_nonce();


		$username = tutils()->array_get('log', $_POST);
		$password = tutils()->array_get('pwd', $_POST);


		try {
			$creds = array(
				'user_login'    => trim( wp_unslash( $username ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				'user_password' => $password, // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				'remember'      => isset( $_POST['rememberme'] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			);


			$validation_error = new \WP_Error();
			$validation_error = apply_filters( 'tutor_process_login_errors', $validation_error, $creds['user_login'], $creds['user_password'] );

			if ( $validation_error->get_error_code() ) {
				throw new \Exception( '<strong>' . __( 'Error:', 'tutor' ) . '</strong> ' . $validation_error->get_error_message() );
			}

			if ( empty( $creds['user_login'] ) ) {
				throw new \Exception( '<strong>' . __( 'Error:', 'tutor' ) . '</strong> ' . __( 'Username is required.', 'tutor' ) );
			}

			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
				}
			}

			// Perform the login.
			$user = wp_signon( apply_filters( 'tutor_login_credentials', $creds ), is_ssl() );

			if ( is_wp_error( $user ) ) {
				$message = $user->get_error_message();
				$message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', $message );
				throw new \Exception( $message );
			} else {
				tutor_redirect_back(apply_filters('tutor_login_redirect_url', tutils()->tutor_dashboard_url()));
			}
		} catch ( \Exception $e ) {
			tutor_flash_set('warning', apply_filters( 'login_errors', $e->getMessage()) );
			do_action( 'tutor_login_failed' );
		}



	}





	public function tutor_retrieve_password(){
		tutils()->checking_nonce();

		//echo '<pre>';
		//die(print_r($_POST));

		$login = sanitize_user( tutils()->array_get('user_login', $_POST));

		if ( empty( $login ) ) {
			tutor_flash_set('danger', __( 'Enter a username or email address.', 'tutor' ));
			return false;
		} else {
			// Check on username first, as customers can use emails as usernames.
			$user_data = get_user_by( 'login', $login );
		}

		// If no user found, check if it login is email and lookup user based on email.
		if ( ! $user_data && is_email( $login ) && apply_filters( 'tutor_get_username_from_email', true ) ) {
			$user_data = get_user_by( 'email', $login );
		}

		$errors = new \WP_Error();

		do_action( 'lostpassword_post', $errors );

		if ( $errors->get_error_code() ) {
			tutor_flash_set('danger', $errors->get_error_message() );
			return false;
		}

		if ( ! $user_data ) {
			tutor_flash_set('danger', __( 'Invalid username or email.', 'tutor' ) );
			return false;
		}

		if ( is_multisite() && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
			tutor_flash_set('danger', __( 'Invalid username or email.', 'tutor' ) );
			return false;
		}

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;

		do_action( 'retrieve_password', $user_login );

		$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

		if ( ! $allow ) {
			tutor_flash_set('danger', __( 'Password reset is not allowed for this user', 'tutor' ) );
			return false;
		} elseif ( is_wp_error( $allow ) ) {
			tutor_flash_set('danger', $allow->get_error_message() );
			return false;
		}

		// Get password reset key (function introduced in WordPress 4.4).
		$key = get_password_reset_key( $user_data );

		// Send email notification.
		do_action( 'tutor_reset_password_notification', $user_login, $key );
	}


	public function reset_password_notification( $user_login = '', $reset_key = ''){

		//var_dump($user_login);
		///die(var_dump($reset_key));

		$html = "<h3>".__('Check your E-Mail', 'tutor')."</h3>";
		$html .= "<p>".__("We've sent an email to this account's email address. Click the link in the email to reset your password", 'tutor')."</p>";
		$html .= "<p>".__("If you don't see the email, check other places it might be, like your junk, spam, social, promotion or others folders.", 'tutor')."</p>";
		tutor_flash_set('success', $html);
	}


}