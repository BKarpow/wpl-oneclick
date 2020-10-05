<?php

/**
 * Блок налаштувань для Telegram bot
 */

// параметры: $option_group, $option_name, $sanitize_callback
register_setting( OK_OPTION_GROUP, 'telegram_bot_token', 'sanitize_callback' );
register_setting( OK_OPTION_GROUP, 'telegram_chat_id', 'sanitize_callback' );
register_setting( OK_OPTION_GROUP, 'telegram_trigger', 'sanitize_callback' );

// параметры: $id, $title, $callback, $page
add_settings_section( 'telegram', 'Налаштування telegram', function(){
    echo '<p>Налаштування бота телеграм</p>';
}, OK_OPTION_PAGE_NAME );

//Telegram
add_settings_field('telegram_bot_token',
    'Токен для доступу до боту телеграм',
    function(){
        $val = OneClick::getConfig('telegram_bot_token');
        echo ' <input type="text" name="telegram_bot_token[input]" value="'. esc_attr( $val ) .'" />';
    },
    OK_OPTION_PAGE_NAME,
    'telegram' );
add_settings_field('telegram_chat_id',
    'ID чату тклкграм (якщо використовуєте alias то ставте перед іменем знак @).',
    function(){
        $val = OneClick::getConfig('telegram_chat_id');
        echo ' <input type="text" name="telegram_chat_id[input]" value="'. esc_attr( $val ) .'" />';
    },
    OK_OPTION_PAGE_NAME,
    'telegram' );
add_settings_field('telegram_trigger',
    'Увімкнути відправлення в Telegram',
    function(){
        $val = get_option('telegram_trigger');
        $val = $val ? $val['checkbox'] : null;
        echo '
        <label><input 
        type="checkbox" 
        name="telegram_trigger[checkbox]" 
        value="1" '.checked( 1, $val , false) .' /> отметить</label>
        ';
    },
    OK_OPTION_PAGE_NAME,
    'telegram' );




