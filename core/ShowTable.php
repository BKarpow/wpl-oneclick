<?php


namespace OneClick;


class ShowTable extends WP_List_Table
{

    public array $example_data = [
        ['ID' => 1, 'product_id' => 1, 'phone' => '0979560001', 'ip'=>'127.0.0.1', 'date_order' => '2020-10-01 10:12:21'],
    ];
    private string $table = 'oneclick_order';
    private string $page = 'ok_menu';


    private static function getAllCountItems():int
    {
        global $wpdb;
        $sql = "select COUNT(*) as `count` from {$wpdb->prefix}oneclick_order ";
        $res = $wpdb->get_results($sql, 'ARRAY_A');
        if (!empty($arr[0]['count'])){
            return (int) $arr[0]['count'];
        }
        return 0;
    }

    /**
     * Повертає масив замовлень
     * @param string|null $order_by - вказати ім'я колонки та тип ASC чи DESC через пробіл
     * @param int $limit
     * @param int $offset
     * @return array
     */
    private function get_data_orders(string $order_by = null,
                                     int $limit = 10,
                                     int $offset = 0
    ):array
    {

        global $wpdb;
        $order_by = ($order_by) ? " ORDER BY {$order_by}" : " ORDER BY `date` DESC ";
        $limit = abs($limit);
        $offset = abs($offset);
        $limit_line = ($offset) ? " LIMIT {$limit}, {$offset} " : " LIMIT {$limit} ";
        $pref = $wpdb->prefix;
        $tabl = $pref . $this->table;
        $sql = "select
       {$tabl}.id as id,
       {$pref}posts.post_title as title,
       {$pref}wc_product_meta_lookup.max_price as price,
       {$tabl}.phone as phone,
       {$tabl}.ip as ip,
       {$tabl}.date_order as `date`
from {$tabl}
left join {$pref}posts
ON {$pref}posts.ID = {$tabl}.product_id
left join {$pref}wc_product_meta_lookup
on {$tabl}.product_id = {$pref}wc_product_meta_lookup.product_id ".
        " {$order_by} {$limit_line} ";
        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    function get_columns(){
        return [
            'title' => 'Product Title',
            'price' => 'Product Price',
            'phone'    => 'Phone',
            'ip'      => 'IP',
            'date'      => 'Date'
        ];
    }

    /**
     * Основний метод для відображення
     */
    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $per_page = 50;
        $current_page = $this->get_pagenum();
        $total_items = self::getAllCountItems();
        $res_data = $this->orders_wrapper((($current_page-1)*$per_page), $per_page);
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //Мы должны вычислить общее количество элементов
            'per_page'    => $per_page                     //Мы должны определить, сколько элементов отображается на странице
        ) );
        $res_data = (empty($res_data)) ? [['ID'=>'','title'=>'','price'=>'','phone'=>'','ip'=>'','date'=>'']] : $res_data;

        $this->items = $res_data; # $this->example_data;;
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'title':
            case 'price':
            case 'phone':
            case 'ip':
            case 'date':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ; //Мы отображаем целый массив во избежание проблем
        }
    }


    /**
     * Повертає масив даних сортування + пагінація
     * @param int $limit
     * @param int|null $offset
     * @return array
     */
    private function orders_wrapper(int $limit = 10, int $offset = null){
        if (isset($_GET['order'])){
            $ord_column = filter_input(INPUT_GET,
                'orderby',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $ord_type = filter_input(INPUT_GET,
                'order',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $order = " `{$ord_column}` {$ord_type}";
            return $this->get_data_orders($order, $limit, $offset);
        }else{
            return $this->get_data_orders(null, $limit, $offset);
        }
    }

    function get_sortable_columns() {
        return [
            'title'  => ['title',false],
            'price'  => ['price',false],
            'phone' => ['phone',false],
            'ip'   => ['ip',false],
            'date'   => ['date',false],
        ];
    }
}