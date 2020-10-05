<?php

register_setting(OK_OPTION_GROUP,
    'ok_position_hook',
'sanitize_callback'
);

add_settings_section('ok_position',
    'Позиція форми замовлення',
    function(){
    echo '<p>Тут можна налаштування розміщення форми замовлення.</p>';
    },
    OK_OPTION_PAGE_NAME
);

add_settings_field('ok_position_hook',
    'Хук розміщення в WooCommerce',
    function(){
        $val = OneClick::getConfig('ok_position_hook');
        echo '
            <input 
                data-id="ok_position_hook"
                data-defaultValue="woocommerce_single_product_summary" 
                type="text" 
                name="ok_position_hook[input]" 
                value="'.$val.'"
            >
            <a data-optId="ok_position_hook" class="set-default">За замовчуванням</a>
            ';
    },
    OK_OPTION_PAGE_NAME,
    'ok_position'
);