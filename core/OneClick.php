<?php


class OneClick
{
    use \OneClick\TelegramTrait,
        \OneClick\ReCaptcha,
        \OneClick\Install,
        \OneClick\AddToDb;

    /**
     * Лог для режиму розробки
     * @var array
     */
    public static array $log = [];

    private static array $product_info;
    /**
     * @var string Група налаштувань 
     */
    private string $option_group = 'one_click';

    /**
     * Масив в якому інформація про активний товар
     * @var array
     */
//    private array $product_info;

    public function __construct()
    {
        OneClick::set_table();
    }

    /**
     * Повертає маси логів
     * @return array
     */
    public static function getLog():array
    {
        if (OneClick::getConfig('ok_debug_trigger')){
            return  (array)self::$log;
        }else{
            return [];
        }
    }

    /**
     * Додає до масиву логів новий запис
     * @param string $key - група логів
     * @param $value
     */
    private static function setLog(string $key, $value):void
    {
        OneClick::$log[$key] = $value;
    }
    /**
     * Повертає ім'я головного файлу css з хешем для відключення кешування браузером
     * @return string
     */
    private function getAntiCacheCssName():string
    {
        $file_style_md5 = md5_file(ROOT_ONE_CLICK .'css'.DIRECTORY_SEPARATOR.'style.css');
        return CSS_ONE_CLICK . 'style.css?hash=' . $file_style_md5;
    }

    /**
     * Підключає стилі та скрипти плагінв
     */
    public function includeCssJs():void
    {
        $file_style_md5 = md5_file(ROOT_ONE_CLICK .'css'.DIRECTORY_SEPARATOR.'style.css');
        wp_enqueue_style( 'style-name', CSS_ONE_CLICK .  'style.css', [], $file_style_md5);
        wp_enqueue_script( 'script-name', JS_ONE_CLICK . 'app.js', array(), '1.0.0', true );
    }

    /**
     * Підключає сторінку з папки pages
     * @param string $page_name - ім'я фвйлу
     */
    public function load_page(string $page_name):void
    {
        $page_name = str_replace(['.php'], '', trim($page_name));
        $file = ROOT_ONE_CLICK .'pages' . DIRECTORY_SEPARATOR . $page_name . '.php';
        if (file_exists($file)){
            include $file;
        }else{
            echo 'Page '. $page_name . 'not found!';
        }

    }

    /**
     * Повертає параметри товару
     * @return array
     */
    public function getProductInfo(): array
    {
        return self::$product_info;
    }

    /**
     * Відображає блок з формою замовлення
     */
    public function render_block(){
        global $post;
        $this->init_product_info($post);
        $this->load_page('form');
    }

    /**
     * Наповнює масив this->product_info інформацією про вибраеий (активний) товар.
     * @param WP_Post $post
     */
    public function init_product_info(WP_Post $post):void
    {
        $product = new WC_Product($post->ID);
        $info['id'] = $post->ID;
        $info['name'] = $product->get_name();
        $info['price'] = $product->get_price();
        $info['image'] = wp_get_attachment_image_src($product->get_image_id());
        $info['url'] = get_permalink($post->ID);
        self::$product_info = (array) $info;
    }

    /**
     * Поаертає конфігурацію з бвзи wordpress
     * @param string $config_name
     * @return bool|string
     */
    public static function getConfig(string $config_name)
    {
        $conf = get_option($config_name);
        if ($conf){
            if (isset($conf['input'])) return (string) $conf['input'];
            if (isset($conf['checkbox'])) return (bool) $conf['checkbox'];
        }else{
            return false;
        }
        return false;
    }

    /**
     * Створює рядок повідомлення замовлення
     * @param array $data
     * @return string
     */
    private static function parse_message(array $data):string
    {
        $message = 'Замовлено товар '. PHP_EOL;
        foreach ($data as $key => $item){
            if ($key === 'token') continue;
            if ($key === 'id') continue;
            $item = strip_tags(trim( $item));
            $key = strip_tags(trim( $key));
            if (strlen($key) > 20) $key = 'XSS attack sanitize!';
            if (strlen($item) > 250) $item = 'XSS attack sanitize!';
            $message .= "{$key}: {$item} " . PHP_EOL;
        }
        return $message;
    }


    /**
     * Перевіряє каптчу якщо та увімкнена
     * @param string $token
     * @return bool
     */
    private static function reCaptchaField(string $token):bool
    {
        if (OneClick::getConfig('ok_recaptcha_trigger')){
            self::setRecaptchaSecret(OneClick::getConfig('ok_recaptcha_secret_key'));
            if (!self::reVerify($token)){
                self::setLog('recaptcha', 'error');
                return false;
            }else{
                self::setLog('recaptcha', 'ok');
                return true;
            }
        }
        return true;
    }

    /**
     * Відправляє лист на пошту
     * @param string $message
     */
    private static function send_email(string $message):void
    {
        if ($to = get_option('ok_email_to')){
            self::setLog('email', wp_mail($to,
                OneClick::getConfig('ok_email_subject'),
                $message)
            );
        }
    }

    private static function send_telegram(string $message):void
    {
        if (OneClick::getConfig('telegram_trigger')){
            self::setLog('telegram', self::sendTelegram($message));
        }
    }

    /**
     * Стартава функція для відправки диних
     * @param array $request_data
     * @return array
     */
    static public function send(array $request_data)
    {
        $message = OneClick::parse_message($request_data);
        if (!OneClick::reCaptchaField((string)$request_data['token']))
            return self::getLog();
        self::setLog('database_save', self::add_order((int)$request_data['id'], $request_data['phone']));
        self::setLog('message', $message);
        self::setLog('request', $request_data);
        self::send_email($message);
        self::send_telegram($message);
        return self::getLog();
    }

    /**
     * Метод встановлює налаштування за замовчуванням та таблиці в базу!
     */
    public static function install_plugin(){
        OneClick::install_orders_table();
        OneClick::set_default_options();
    }


}