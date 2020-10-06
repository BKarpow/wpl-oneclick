<?php
namespace OneClick;


trait Install{
    /**
     * Створює таблицю замовлення
     * @return bool|int
     */
    private static function install_orders_table(){
        global $wpdb;
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}oneclick_order (
            `id` int(11) not null AUTO_INCREMENT,
            `product_id` int(11) not null,
            `phone` varchar(25) not null,
            `ip` varchar(25) not null,
            `date_order` timestamp ,    
            PRIMARY KEY (`id`)
); ";
        return $wpdb->query($sql);
    }

    /**
     * Встановлює налаштування за замовчуванням
     */
    private static function set_default_options(){
        update_option('ok_alert_success', 'Успішно, чекайте дзвінка');
        update_option('ok_alert_error_number', 'Пимилковий номер телефону');
        update_option('ok_animate_title', 'animate__heartBeat');
        update_option('ok_animate_hide_button', 'animate__zoomOutUp');
    }
}