<?php
/**
 * Создаем страницу настроек плагина
 */

define('OK_OPTION_PAGE_NAME', 'ok_settings');
define('OK_OPTION_GROUP', 'ok_options');
define('OK_PAGES_OPTIONS', ROOT_ONE_CLICK .DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'options' . DIRECTORY_SEPARATOR);


add_action('admin_menu', 'add_plugin_page');
function add_plugin_page(){
    add_options_page( 'Настройка One Click Bay',
        'OneClickBay', 'manage_options',
        'ok_settings',
        'ok_options_page_output' );
}

function ok_options_page_output(){
    ?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title() ?></h2>

        <form action="options.php" method="POST">
            <?php
            settings_fields( OK_OPTION_GROUP );     // скрытые защитные поля
            do_settings_sections( OK_OPTION_PAGE_NAME ); // секции с настройками (опциями). У нас она всего одна 'section_id'
            submit_button();
            ?>
        </form>

        <script>
            document.querySelectorAll('.set-default').forEach( item => {
                item.addEventListener('click', function(ev){
                   const data = ev.target.dataset
                    const input = document.querySelector('[data-id='+data.optid+']')
                    if (input){
                        input.value = input.dataset.defaultvalue
                    }
                })
            })
        </script>

    </div>
    <?php
}

/**
 * Регистрируем настройки.
 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
 */
add_action('admin_init', 'plugin_settings');
function plugin_settings(){

    // Main settings block
    include_once OK_PAGES_OPTIONS . 'enable.php';

    // Position settings block
    include_once OK_PAGES_OPTIONS . 'position.php';

    // Animate settings block
    include_once OK_PAGES_OPTIONS . 'animate.php';

    // Alert settings block
    include_once OK_PAGES_OPTIONS . 'alert.php';

    // Telegram settings block
    include_once OK_PAGES_OPTIONS . 'telegram.php';

    // Email settings block
    include_once OK_PAGES_OPTIONS . 'email.php';

    // Recaptcha settings block
    include_once OK_PAGES_OPTIONS . 'recaptcha.php';

    // Debug settings block
    include_once OK_PAGES_OPTIONS . 'debug_mod.php';

}

## Очистка данных
function sanitize_callback( $options ){
    // очищаем
    foreach( $options as $name => & $val ){
        if( $name == 'input' )
            $val = strip_tags( $val );

        if( $name == 'checkbox' )
            $val = intval( $val );
    }

    //die(print_r( $options )); // Array ( [input] => aaaa [checkbox] => 1 )

    return $options;
}

function ok_sanitize_string($option){
    return strip_tags(trim($option));
}