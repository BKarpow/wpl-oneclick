<?php

/*
Plugin Name: One Click (WooCommerce)
Plugin URI: https://github.com/BKarpow/wpl-oneclick
Description: Плагін додає можливість купівлі в один клік в WooCommerce. Додає форму вказання номеру. Має захист від спаму (reCaptcha v3) та відправку в Telegram.
Version: 1.2
Author: XemerOne <xymerone@gmail.com>
Author URI: http://karpow.site
License:  GPL2
*/

define('ROOT_ONE_CLICK', plugin_dir_path(__FILE__));
define('CORE_ONE_CLICK', ROOT_ONE_CLICK. 'core' . DIRECTORY_SEPARATOR);
define('CSS_ONE_CLICK', plugin_dir_url(__FILE__) . 'css/');
define('JS_ONE_CLICK', plugin_dir_url(__FILE__) . 'js/');

//Підключення стилів та скриптів
require_once CORE_ONE_CLICK . 'bootstrap.php';



$OneClick = new OneClick();



function my_add_menu_items(){
    add_menu_page( 'One Click',
        'Замовлення One Click',
        'activate_plugins',
        'ok_menu',
        'ok_render_main' );
}
add_action( 'admin_menu', 'my_add_menu_items' );

function ok_render_main(){
    $myListTable = new \OneClick\ShowTable();
    echo '<div class="wrap"><h2>Список замовлень</h2>';
    $myListTable->prepare_items();
    $myListTable->display();
    echo '</div>';
}

register_activation_hook(__FILE__, ['OneClick', 'install_plugin']);

add_action('wp_enqueue_scripts', [$OneClick, 'includeCssJs']);
add_action(OneClick::getConfig('ok_position_hook'), [$OneClick, 'render_block']);

$OneClick->load_page('options');

