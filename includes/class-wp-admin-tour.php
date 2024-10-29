<?php
/**
 * Admin tour main class.
 *
 * @package WordPress
 */

// phpcs:disable Generic.Formatting.MultipleStatementAlignment.NotSameWarning
if ( ! class_exists( 'WP_Admin_Tour' ) ) {

	/**
	 * Declare class `WP_Admin_Tour`
	 */
	class WP_Admin_Tour {

		/**
		 * Init hooks.
		 */
		public function init_hooks() {
			add_action( 'admin_enqueue_scripts', array( $this, 'wat_admin_scripts' ) );
			add_action( 'wp_ajax_wat_dismiss_pointer', array( $this, 'wat_dismiss_pointer' ) );
			add_action( 'wp_ajax_nopriv_wat_dismiss_pointer', array( $this, 'wat_dismiss_pointer' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'wat_add_dashboard_widgets' ) );
			add_action( 'admin_init', array( $this, 'wat_admin_init' ) );
			add_action( 'admin_bar_menu', array( $this, 'wat_admin_bar' ), 99 );
			if ( ! defined( 'WAT_SHOW_ADMIN_BAR_OPTION' ) || WAT_SHOW_ADMIN_BAR_OPTION ) {
				add_action( 'admin_footer', array( $this, 'wat_admin_footer' ) );
			}
		}

		/**
		 * Load admin init hook.
		 */
		public function wat_admin_init() {
			if ( get_option( 'wat-activated', false ) ) {
				delete_option( 'wat-activated' );
				if ( ! headers_sent() ) {
					wp_safe_redirect(
						add_query_arg(
							'wat_start_tour',
							1,
							admin_url()
						)
					);
					exit();
				}
			}
		}

		/**
		 * Register admin scripts.
		 */
		public function wat_admin_scripts() {
			$pointers = wat_get_pointers();
			if ( ! empty( $pointers ) ) {
				wp_register_script( 'wat-pointer', plugin_dir_url( __FILE__ ) . '../assets/js/wat-pointers.js', array( 'wp-pointer' ), true, true );
				wp_register_style( 'wat-pointer', plugin_dir_url( __FILE__ ) . '../assets/css/wat-pointers.css', array( 'wp-pointer' ), true );

				wp_enqueue_script( 'wat-pointer' );
				wp_enqueue_style( 'wat-pointer' );

				$i18n = apply_filters(
					'wat_button_labels',
					array(
						'next_button'        => __( 'Next', 'admin-tour' ),
						'prev_button'        => __( 'Prev', 'admin-tour' ),
						'finish_button'      => __( 'Finish', 'admin-tour' ),
						'go_to_button'       => __( 'Go to', 'admin-tour' ),
						'close_button_title' => __( 'Close', 'admin-tour' ),
					)
				);

				if ( ! is_admin() && is_user_logged_in() ) {
					$wat_screen_id = array( 'woocommerce' );
					if ( function_exists( 'apt_current_dokan_page_id' ) ) {
						$wat_screen_id[] = apt_current_dokan_page_id();
					}
					if ( in_array( $pointers['screen_id'], $wat_screen_id, true ) ) {
						$pointers['screen_id'] = $pointers['screen_id'] . '_' . get_current_user_id();
					}
				}
				wp_localize_script(
					'wat-pointer',
					'WAT',
					array(
						'ajax_url'       => admin_url( 'admin-ajax.php' ),
						'pointers'       => $pointers['pointers'],
						'security_nonce' => wp_create_nonce( 'wat_dismiss_pointer' ),
						'i18n'           => $i18n,
						'current_screen' => $pointers['screen_id'],
					)
				);
			}
		}

		/**
		 * Dismiss pointers.
		 */
		public function wat_dismiss_pointer() {
			check_ajax_referer( 'wat_dismiss_pointer', 'nonce' );
			$screen = isset( $_POST['screen'] ) ? sanitize_title( wp_unslash( $_POST['screen'] ) ) : 'general';
			$transient_key = 'wat_dismiss_pointers_' . $screen;

			set_transient( $transient_key, 1, apply_filters( 'wat_dismiss_expiration_time', 30 * DAY_IN_SECONDS ) );

			wp_send_json_success();
		}

		/**
		 * Add admin bar.
		 *
		 * @param object $admin_bar Admin bar object.
		 */
		public function wat_admin_bar( $admin_bar ) {
			if ( function_exists( 'get_current_screen' ) && wat_allowed_roles() ) {
				$current_screen = get_current_screen();
				if ( $current_screen && ! in_array( $current_screen->id, array( 'dashboard' ), true ) ) {
					if ( ! defined( 'WAT_SHOW_ADMIN_BAR_OPTION' ) || WAT_SHOW_ADMIN_BAR_OPTION ) {
						$args = array(
							'id'    => 'wat-screen-option-slug',
							// translators: %1$s to current screen ID.
							'title' => wp_sprintf( __( 'Current Screen: <span style="color: #F00;" id="wat_copy_screen_id">%1$s<span>', 'admin-tour' ), $current_screen->id ),
							'href'  => 'javascript:;',
							'meta'  => array(
								'title' => __( 'Click to copy', 'admin-tour' ),
							),
						);
						$admin_bar->add_menu( $args );
					}

					$pointers = wat_get_pointers( true );
					if ( ! empty( $pointers ) && isset( $pointers[ $current_screen->id ] ) ) {
						$transient_key = 'wat_dismiss_pointers_' . $current_screen->id;
						if ( get_transient( $transient_key ) ) {
							$start_tour = array(
								'id'    => 'wat-start-tour',
								'title' => __( 'Start Tour', 'admin-tour' ),
								'href'  => add_query_arg( 'wat_start_tour', 1 ),
							);
							$admin_bar->add_menu( $start_tour );
						}
					}
				}
			}
		}

		/**
		 * Add click to copy script in admin footer.
		 */
		public function wat_admin_footer() {
			if ( wat_allowed_roles() ) {
				?>
		<script>
			jQuery( document ).ready( function( $ ) {
				$( document ).on( 'click', '#wp-admin-bar-wat-screen-option-slug a', function() {
				var el = document.getElementById( 'wat_copy_screen_id' );
				var screenID = el.innerText;
				var listener = function( ev ) {
					ev.clipboardData.setData( "text/plain", screenID );
					ev.preventDefault();
				};
				document.addEventListener( 'copy', listener );
				document.execCommand( 'copy' );
				document.removeEventListener( 'copy', listener );
				var range = document.createRange();
				range.selectNodeContents( el );
				var sel = window.getSelection();
				sel.removeAllRanges();
				sel.addRange( range );
				return false;
			} );
		} );
		</script>
				<?php
			}
		}

		/**
		 * Register dashboard widget.
		 */
		public function wat_add_dashboard_widgets() {
			if ( wat_allowed_roles() ) {
				add_meta_box( 'wat_widget', __( 'Admin Tour', 'admin-tour' ), array( $this, 'wat_dashboard_widget' ), 'dashboard', 'side', 'high' );
			}
		}

		/**
		 * Render widget.
		 */
		public function wat_dashboard_widget() {
			$pointers = wat_get_pointers( true );
			$pointer_list = '';
			if ( ! empty( $pointers ) ) {
				foreach ( $pointers as $s => $pointer ) {
					$s = 'general' === $s ? 'index.php' : $s;
					$screen = WP_Screen::get( $s );
					$screen_name = $screen->id;
					$screen_name = explode( '-', $screen_name );
					$screen_name = end( $screen_name );
					$screen_name = preg_replace( '/[^A-Za-z0-9\-]/', ' ', $screen_name );
					$screen_name = ucwords( $screen_name );

					if ( isset( $pointer['screen_info'] ) && isset( $pointer['screen_info']['name'] ) ) {
						$screen_name = $pointer['screen_info']['name'];
					}

					$menu_url = '#';
					if ( isset( $pointer['screen_info'] ) && isset( $pointer['screen_info']['url'] ) ) {
						$menu_url = add_query_arg( 'wat_start_tour', 1, $pointer['screen_info']['url'] );
					}
					$extra_button_style = '';
					if ( ! empty( $pointer_list ) ) {
						$extra_button_style = ' style="margin-top: 10px;"';
					}
					$pointer_list .= '<tr><td width="50%">' . esc_html( $screen_name ) . '</td><td width="50%"><a href="' . $menu_url . '" class="button button-primary"' . $extra_button_style . '>' . esc_html__( 'Start Tour', 'admin-tour' ) . '</a></td></tr>';
				}
				?>
				<table>
					<tbody>
						<?php echo wp_kses_post( $pointer_list ); ?>
					</tbody>
				</table>
				<?php
			}
		}
	}
}
