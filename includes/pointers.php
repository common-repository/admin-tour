<?php
/**
 * Register pointers.
 *
 * @package WordPress
 */

// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
// phpcs:disable Generic.Formatting.MultipleStatementAlignment.NotSameWarning
if ( ! function_exists( 'wat_get_pointers' ) ) {

	/**
	 * Get all register pointers.
	 *
	 * @param bool $return_all Return all pointers.
	 * @return array
	 */
	function wat_get_pointers( $return_all = false ) {
		$pointers  = array();
		$pointers['general'] = array(
			'screen_info' => array(
				'name' => __( 'Dashboard', 'admin-tour' ),
				'url'  => admin_url(),
			),
			array(
				'id'    => 'menu_posts',
				'tagget_element' => '#menu-posts',
				'title' => __( 'Manage Posts', 'admin-tour' ),
				'content' => __( 'You can create, edit or delete any post on the site.', 'admin-tour' ),
				'position' => array(
					'edge' => 'left',
					'align' => 'left',
				),
				'url' => admin_url( 'edit.php' ),
			),
			array(
				'id'    => 'menu_media',
				'tagget_element' => '#menu-media',
				'title' => __( 'Handle Media', 'admin-tour' ),
				'content' => __( 'You can upload images, videos, documents.', 'admin-tour' ),
				'position' => array(
					'edge' => 'left',
					'align' => 'left',
				),
				'url' => admin_url( 'upload.php' ),
			),
			array(
				'id'    => 'menu_pages',
				'tagget_element' => '#menu-pages',
				'title' => __( 'Manage Pages', 'admin-tour' ),
				'content' => __( 'You can create, edit or delete any page on the site.', 'admin-tour' ),
				'position' => array(
					'edge' => 'left',
					'align' => 'left',
				),
				'url' => add_query_arg( array( 'post_type' => 'page' ), admin_url( 'edit.php' ) ),
			),
			array(
				'id'    => 'menu_users',
				'tagget_element' => '#menu-users',
				'title' => __( 'Manage Users', 'admin-tour' ),
				'content' => __( 'who has admin rights, its email and update their password.', 'admin-tour' ),
				'position' => array(
					'edge' => 'left',
					'align' => 'left',
				),
				'url' => admin_url( 'users.php' ),
			),
			array(
				'id'    => 'wat_widget',
				'tagget_element' => '#wat_widget .postbox-header, #wat_widget h2.ui-sortable-handle',
				'title' => __( 'Full tour', 'admin-tour' ),
				'content' => __( 'You can check all the tours available in the site.', 'admin-tour' ),
				'position' => array(
					'edge' => 'top',
					'align' => 'left',
				),
				'url' => admin_url( 'index.php' ),
			),
		);

		$pointers['edit-category'] = array(
			'screen_info' => array(
				'name' => __( 'Category', 'admin-tour' ),
				'url'  => add_query_arg( 'taxonomy', 'category', admin_url( 'edit-tags.php' ) ),
			),
			array(
				'id'    => 'tag_name',
				'tagget_element' => '#tag-name',
				'title' => __( 'Add New Category', 'admin-tour' ),
				'content' => __( 'You can add the category name.', 'admin-tour' ),
				'position' => array(
					'edge' => 'top',
					'align' => 'left',
				),
			),
			array(
				'id'    => 'tag_slug',
				'tagget_element' => '#tag-slug',
				'title' => __( 'Category Slug', 'admin-tour' ),
				'content' => __( 'You can add the category slug that will be used to access that category.', 'admin-tour' ),
				'position' => array(
					'edge' => 'top',
					'align' => 'left',
				),
			),
			array(
				'id'    => 'tag_edit',
				'tagget_element' => '#the-list tr:eq(0) .name',
				'title' => __( 'Edit Category', 'admin-tour' ),
				'content' => __( 'You can hover the category and you will see the edit option, click on it to edit that category.', 'admin-tour' ),
				'position' => array(
					'edge' => 'top',
					'align' => 'left',
				),
			),
		);
		$pointers = apply_filters( 'wat_pointers', $pointers );
		if ( ! $return_all ) {
			$pointers = wat_remove_dismiss_pointers( $pointers );
		}
		return $pointers;
	}

	if ( ! function_exists( 'wat_remove_dismiss_pointers' ) ) {
		/**
		 * Remove dismiss pointers.
		 *
		 * @param array $pointers Pointers.
		 * @return array
		 */
		function wat_remove_dismiss_pointers( $pointers ) {
			$current_screen = 'general';
			if ( function_exists( 'atp_dokan_is_active' ) && atp_dokan_is_active() ) {
				if ( atp_dokan_screen() ) {
					if ( function_exists( 'apt_current_dokan_page_id' ) ) {
						$current_screen = apt_current_dokan_page_id();
					}
				}
			}
			if ( function_exists( 'atp_woocommerce_is_active' ) && atp_woocommerce_is_active() ) {
				if ( function_exists( 'is_account_page' ) && is_account_page() ) {
					$current_screen = 'woocommerce';
				}
			}
			if ( function_exists( 'get_current_screen' ) ) {
				$current_screen = get_current_screen();
				$current_screen = $current_screen ? $current_screen->id : $current_screen;
			}

			// Create transient key.
			$transient_key = 'wat_dismiss_pointers_general';
			if ( ! empty( $current_screen ) && isset( $pointers[ $current_screen ] ) ) {
				$wat_screen_id = array( 'woocommerce' );
				if ( function_exists( 'apt_current_dokan_page_id' ) ) {
					$wat_screen_id[] = apt_current_dokan_page_id();
				}

				if ( in_array( $current_screen, $wat_screen_id, true ) && is_user_logged_in() ) {
					$transient_key = 'wat_dismiss_pointers_' . $current_screen . '_' . get_current_user_id();
				} else {
					$transient_key = 'wat_dismiss_pointers_' . $current_screen;
				}
			}
			// Restart admin tour.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$start_tour = isset( $_GET['wat_start_tour'] ) ? (int) $_GET['wat_start_tour'] : 0;
			if ( $start_tour ) {
				delete_transient( $transient_key );
			}
			if ( get_transient( $transient_key ) ) {
				return array();
			}
			if ( is_admin() ) {
				if ( is_user_logged_in() && ! wat_allowed_roles() ) {
					return array();
				}
			}

			// Get current screen poiters.
			if ( ! empty( $current_screen ) && isset( $pointers[ $current_screen ] ) ) {
				$pointers = array(
					'screen_id' => $current_screen,
					'pointers'  => $pointers[ $current_screen ],
				);
				return $pointers;
			}
			if ( 'dashboard' === $current_screen ) {
				$pointers = array(
					'screen_id' => 'general',
					'pointers'  => isset( $pointers['general'] ) ? $pointers['general'] : array(),
				);
				return $pointers;
			}
			return array();
		}
	}

	if ( ! function_exists( 'wat_reorder_pointers' ) ) {
		/**
		 * Reoder pointers.
		 *
		 * @param array $pointers pointers.
		 * @param array $order reorder array index.
		 * @return array
		 */
		function wat_reorder_pointers( $pointers = array(), $order = array() ) {
			if ( empty( $pointers ) || empty( $order ) ) {
				return $pointers;
			}
			$reorder_pointers = array();
			foreach ( $pointers as $key => $pointer ) {
				$reoder_id = false;
				if ( isset( $pointer['id'] ) ) {
					$reoder_id = array_search( $pointer['id'], $order, true );
				}
				if ( false !== $reoder_id ) {
					$reorder_pointers[ $reoder_id ] = $pointer;
				} else {
					$reorder_pointers[ $key ] = $pointer;
				}
			}
			ksort( $reorder_pointers );
			return $reorder_pointers;
		}
	}

	if ( ! function_exists( 'wat_allowed_roles' ) ) {
		/**
		 * Allowed roles.
		 */
		function wat_allowed_roles() {
			$allowed = false;
			if ( function_exists( 'wp_get_current_user' ) ) {
				$current_user_info = wp_get_current_user();
				$roles = $current_user_info->roles;
				$allowed_roles = apply_filters( 'wat_allowed_roles', array( 'administrator' ) );
				$allowed_roles = array_intersect( $allowed_roles, $roles );
				$allowed = ! empty( $allowed_roles ) ? true : false;
			}
			return $allowed;
		}
	}
}
