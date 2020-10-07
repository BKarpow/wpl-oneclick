<?php

register_setting(OK_OPTION_GROUP,
    'ok_trigger',
    'sanitize_callback');

add_settings_section('ok_main_settings',
    'Головні налаштування',
    function(){
    echo '<p>Головні налаштування плагіну</p>';
    },
    OK_OPTION_PAGE_NAME);

add_settings_field('ok_trigger',
    'Увімкнути форму',
    function(){
    $val = OneClick::getConfig('ok_trigger');
        echo '<label><input type="checkbox" 
                name="ok_trigger[checkbox]"
                value="1"
                '. checked(1, $val, false) .'
            > перимикач</label>';
    },
    OK_OPTION_PAGE_NAME,
    'ok_main_settings'
);