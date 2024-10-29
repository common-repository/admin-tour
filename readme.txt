=== Admin Tour ===
Plugin Name: Admin Tour
Plugin URI: https://wordpress.org/plugins/admin-tour
Contributors: krishaweb, dilipbheda, pratikgandhi
Tags: walkthrough, introduction, tutorial, user-onboarding, admin guide
Requires at least: 5.0
Tested up to: 6.1
Stable tag: 1.3
Copyright: (c) 2012-2022 KrishaWeb Technologies PVT LTD (info@krishaweb.com)
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Admin Tour helps you to create a tour for admin. Admin user can go through the tour and they will get the knowledge about how to use the admin panel.

== Description ==

Admin Tour is used to create a tour for the non technial admin user. Generally they do not have idea on how to operate the admin panel even though the developer has given them the detailed demo. After sometime, the admin logs into the panel, they find it quite difficult to operate. Admin Tour will make them feel comfortable.

There is more for developers on this plugin. There are hooks available so that they can add other tour steps as per the customization in the admin side. There is an option in the wp admin bar in the admin side for starting a tour manually. There is also a Dashboard widget available which will have all the tours list and a button to start that tour. By default, when admin logs into the admin panel after 30 days then the tour will start automatically. If you want to change this limit, you can do it using a filter.


Features
•   Easy installation
•   Show default steps like Posts, Pages, Media, Users and category
•   Multi-lingual support
•   Free support

== Checkout the advanced features of Admin Tour Pro: ==

•   Default steps applied for vendor user of Dokan.
•   Dashboard widget added for all the tours within the vendor.
•   Compatible with WooCommerce customer.
•   Easily customizable.

<a href="https://store.krishaweb.com/product/admin-tour-pro/" target="_blank">Checkout the Admin Tour Pro</a>

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Install the plugin via WordPress or download and upload the plugin to the /wp-content/plugins/
2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Can I add a new step in a tour? =

Yes, you can add it using this filter:

`add_filter( 'wat_pointers', function( $pointers ) {
    // Register pointer for contact form 7.
    $pointers['toplevel_page_wpcf7'] = array(
        'screen_info' => array(
            'name' => __( 'Contact form List', 'admin-tour' ),
            'url'  => add_query_arg( array( 'page' => 'wpcf7' ), admin_url( 'admin.php' ) ),
        ),
        array(
            'id'             => 'add_new',
            'tagget_element' => '.page-title-action', // jQuery selector ID, class or any other method.
            'title'          => __( 'Add new', 'admin-tour' ),
            'content'        => __( 'Add new form.', 'admin-tour' ),
            'position'       => array(
                'edge'  => 'left',
                'align' => 'left',
            ),
            'url' => add_query_arg( array( 'page' => 'wpcf7-new' ), admin_url( 'admin.php' ) ),
        ),
        array(
            'id'             => 'edit',
            'tagget_element' => '#the-list tr:eq(0) .title', // jQuery selector ID, class or any other method.
            'title'          => __( 'Edit form', 'admin-tour' ),
            'content'        => __( 'Edit contact form.', 'admin-tour' ),
            'position'       => array(
                'edge'  => 'bottom',
                'align' => 'left',
            ),
        ),
    );
    return $pointers;
} );`

screen_info array is used for the dahboard widget, what ever text you wish to keep there, you can keep it here.

= Can I reorder the steps? =

Yes, you can do it easily using this filter:

`add_filter( 'wat_pointers', function( $pointers ) {
    // default the below pointer will be added to the last
    $pointers['general'][] = array(
        'id'    => 'menu_comments',
        'tagget_element' => '#menu-comments', // jQuery selector ID, class or any other method.
        'title' => __( 'Comments', 'admin-tour' ),
        'content' => __( 'You can hover the category and you will see the edit option, click on it to edit that category.', 'admin-tour' ),
        'next_pointer' => '',
        'position' => array(
            'edge' => 'left',
            'align' => 'left',
        ),
    );
    // Change pointer ordering as per your wish
    $reorder  = array( 'menu_posts', 'menu_media', 'menu_pages', 'menu_comments', 'menu_users', 'wat_widget' );
    $pointers['general'] = wat_reorder_pointers( $pointers['general'] );
    return $pointers;
} );`

= How can I change the default login interval? =

You can do it using the below filter. You need to add it in the function file.

`add_filter( 'wat_dismiss_expiration_time', function( $expiration ) {
    return 60 * DAY_IN_SECONDS;
} );`

= Is there a way to display tour for other user role? =

Yes, you can do it easily using this filter:

`add_filter( 'wat_allowed_roles', function( $roles ) {
    $roles[] = 'shop_manager';
    return $roles;
} );`

= How can I remove current screen option in admin bar? =

You can do it using this constant in wp-config.php:

`define( 'WAT_SHOW_ADMIN_BAR_OPTION', false );`

= I have an idea for a great way to improve this plugin. =

Great! I’d love to hear from you at support@krishaweb.com

== Screenshots ==

1. First step of the default tour
2. Step comes in Category screen.
3. You can manually start the tour of Category page.
4. You can manually start any tour available in site from the dashboard widget.

== Changelog ==
= 1.3 =
* Bug fixed.

= 1.2 =
* Added Pro features.

= 1.1 =
* Added wat_allowed_roles filter.

= 1.0 =
* Initial Release