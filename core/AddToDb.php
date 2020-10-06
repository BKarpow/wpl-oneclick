<?php


namespace OneClick;


trait AddToDb
{
    protected static string $table = 'oneclick_order';

    private static function set_table(string $table_name = 'oneclick_order'):void{
        global $wpdb;
        self::$table = $wpdb->prefix . $table_name;
    }

    /**
     * Додає нове замовлення в таблицю
     * @param int $product_id
     * @param string $phone
     * @return bool
     */
    private static  function add_order(int $product_id, string $phone):bool
    {
        self::set_table();
        global $wpdb;
        $data = [
            'product_id' => $product_id,
            'phone' => $phone,
            'ip' => ok_getIp(),
            'date_order' => wp_date('Y-m-d H:i:s')
        ];
        // todo Відлягодження додавання до бази
//        var_dump($data);
//        var_dump(self::$table);
//        die();
        return (bool) $wpdb->insert(self::$table, $data);
    }


}