<?php

//reCaptcha
register_setting(OK_OPTION_GROUP, 'ok_recaptcha_public_key', 'sanitize_callback');
register_setting(OK_OPTION_GROUP, 'ok_recaptcha_secret_key', 'sanitize_callback');
register_setting(OK_OPTION_GROUP, 'ok_recaptcha_trigger', 'sanitize_callback');


add_settings_section('ok_recaptcha', 'Налаштування Google reCaptcha', function(){
    echo '<p>Налаштуйте невидиму капчу (reCaptcha v3) для надійного захисту від спам-ботів</p>';
}, OK_OPTION_PAGE_NAME);

//reCaptcha
add_settings_field('ok_recaptcha_public_key',
    'Публічний ключ (public key)',
    function(){
        $val = get_option('ok_recaptcha_public_key');
        $val = $val ? $val['input']: null;
        echo '<input type="text" 
                name="ok_recaptcha_public_key[input]"
                value="'.esc_attr( $val).'"
            >';
    },
    OK_OPTION_PAGE_NAME,
    'ok_recaptcha'
);
add_settings_field('ok_recaptcha_secret_key',
    'Серкетний ключ (secret key)',
    function(){
        $val = get_option('ok_recaptcha_secret_key');
        $val = $val ? $val['input']: null;
        echo '<input type="text" 
                name="ok_recaptcha_secret_key[input]"
                value="'. esc_attr( $val).'"
            >';
    },
    OK_OPTION_PAGE_NAME,
    'ok_recaptcha'
);
add_settings_field('ok_recaptcha_trigger',
    'Увімкнути захист від спаму',
    function(){
        $val = get_option('ok_recaptcha_trigger');
        $val = $val ? $val['checkbox']: null;
        echo '<label><input type="checkbox" 
                name="ok_recaptcha_trigger[checkbox]"
                value="1"
                '. checked(1, $val, false) .'
            > перемикач</label>';
    },
    OK_OPTION_PAGE_NAME,
    'ok_recaptcha'
);
