<?php

register_setting(OK_OPTION_GROUP,
    'ok_alert_success',
    'sanitize_callback'
);
register_setting(OK_OPTION_GROUP,
    'ok_alert_error_number',
    'sanitize_callback'
);


add_settings_section('ok_alert',
    'Повідомлення під блоком',
    function(){
    echo '<p>Повідомлення помилок або вдалого відправлення</p>';
    },
    OK_OPTION_PAGE_NAME
);


add_settings_field('ok_alert_success',
    'Успішне відправлення замовлення',
    function(){
        $val = OneClick::getConfig('ok_alert_success');
        echo '
            <input 
                data-id="ok_alert_success"
                data-defaultValue="Успішно, чекайте Вам зателефонують." 
                type="text" 
                name="ok_alert_success[input]" 
                value="'.$val.'"
            >
            <a data-optId="ok_alert_success" class="set-default">За замовчуванням</a>
            ';
    },
    OK_OPTION_PAGE_NAME,
    'ok_alert'
);

add_settings_field('ok_alert_error_number',
    'Помилка відправлення замовлення',
    function(){
        $val = OneClick::getConfig('ok_alert_error_number');
        echo '<input 
        type="text"
        data-id="ok_alert_error_number"
        data-defaultValue="Помилка, номер некоректний."
        name="ok_alert_error_number[input]"
        value="'.$val.'"> <a data-optId="ok_alert_error_number" class="set-default">За замовчуванням</a>';
    },
    OK_OPTION_PAGE_NAME,
    'ok_alert'
);