<?php


//Debug mode
register_setting(OK_OPTION_GROUP, 'ok_debug_trigger', 'sanitize_callback');

add_settings_section('ok_debug', 'Для розробників', function(){
    echo '<p>Вмикає логування, вмикайте лише для розробки та налагодження плагіну!!</p>';
}, OK_OPTION_PAGE_NAME);

//debug mode
add_settings_field('ok_debug_trigger',
    'Режим розробника',
    function(){
        $val = get_option('ok_debug_trigger');
        $val = $val ? $val['checkbox']: null;
        echo '<label><input type="checkbox" 
                name="ok_debug_trigger[checkbox]"
                value="1"
                '. checked(1, $val, false) .'
            > перимикач</label>';
    },
    OK_OPTION_PAGE_NAME,
    'ok_debug'
);