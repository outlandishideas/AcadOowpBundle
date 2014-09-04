<?php


namespace Outlandish\AcadOowpBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;

class OutlandishAcadOowpBundle extends Bundle {


	function __construct() {

		add_theme_support( 'menus' );

        add_image_size( 'avatar-square', '300', '300', array('center', 'center'));

        register_nav_menus(
			array(
				'header' => __( 'Header Navigation' ),
				'footer' => __( 'Footer Navigation' ),
                'footer_about'=> __( 'Footer About' )
			)
		);

		add_action(
			'admin_menu', function () {
				remove_menu_page( 'themes.php' );
			}
		);
		add_action(
			'admin_menu', function () {
				add_menu_page('Menus', 'Menus', 'manage_options', 'nav-menus.php', '', 'dashicons-admin-appearance', 64);
			}
		);
	}
}