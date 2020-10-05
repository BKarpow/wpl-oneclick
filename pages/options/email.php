<?php

//Email
register_setting(OK_OPTION_GROUP, 'ok_email_to', 'sanitize_callback');
register_setting(OK_OPTION_GROUP, 'ok_email_subject', 'sanitize_callback');


add_settings_section('ok_email', 'Налаштування Email', function(){
    echo '<p>Налаштування відправки листів</p>';
}, OK_OPTION_PAGE_NAME);

//Email section
add_settings_field('ok_email_to',
    'Куди відправляти (email)',
    function(){
        $val = get_option('ok_email_to');
        $val = $val ? $val['input']: null;
        echo '<input type="email" 
                name="ok_email_to[input]"
                value="'.esc_attr( $val).'"
            >';
    },
    OK_OPTION_PAGE_NAME,
    'ok_email'
);
add_settings_field('ok_email_subject',
    'Тема листа',
    function(){
        $val = get_option('ok_email_subject');
        $val = $val ? $val['input']: null;
        echo '<input type="text" 
                name="ok_email_subject[input]"
                value="'.esc_attr( $val).'"
            >';
    },
    OK_OPTION_PAGE_NAME,
    'ok_email'
);
