<?php

register_setting(OK_OPTION_GROUP,
    'ok_animate_title',
    'sanitize_callback'
);
register_setting(OK_OPTION_GROUP,
    'ok_animate_hide_button',
    'sanitize_callback'
);

add_settings_section('ok_animate', 'Налаштування анімацій', function(){
    echo '<p>Налаштуйте анімацію блоку замовлення</p>';
}, OK_OPTION_PAGE_NAME);

add_settings_field('ok_animate_title', 'Клас анімації до заголовку (https://animate.style)',
    function(){
        $val = OneClick::getConfig('ok_animate_title');
        echo ' 
        <input 
            data-id="ok_animate_title"
            data-defaultvalue="animate__heartBeat"
            type="text"
            name="ok_animate_title[input]"
             value="'.$val.'"
        /> 
        <a data-optid="ok_animate_title" class="set-default" > Зп замовчуванням </a>
        ';
    }, OK_OPTION_PAGE_NAME,
'ok_animate');

add_settings_field('ok_animate_hide_button', 'Клас анімації для ховання форми після вдалого відправлення (https://animate.style)',
    function(){
        $val = OneClick::getConfig('ok_animate_hide_button');
        echo ' 
        <input 
            data-id="ok_animate_hide_button"
            data-defaultvalue="animate__zoomOutUp"
            type="text"
            name="ok_animate_hide_button[input]"
             value="'.$val.'"
        /> 
        <a data-optid="ok_animate_hide_button" class="set-default" > Зп замовчуванням </a>
        
         ';
    }, OK_OPTION_PAGE_NAME,
    'ok_animate');