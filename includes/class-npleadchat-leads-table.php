<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class NPLEADCHAT_Leads_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct( [
            'singular' => 'lead',
            'plural'   => 'leads',
            'ajax'     => false,
        ] );
    }

    public function get_columns() {
        return [
            'cb'      => '<input type="checkbox" />',
            'name'    => __( 'Name', 'np-lead-chatbot' ),
            'email'   => __( 'Email', 'np-lead-chatbot' ),
            'phone'   => __( 'Phone', 'np-lead-chatbot' ),
            'message' => __( 'Message', 'np-lead-chatbot' ),
            'date'    => __( 'Date', 'np-lead-chatbot' ),
        ];
    }

    public function get_sortable_columns() {
        return [
            'name' => [ 'name', false ],
            'date' => [ 'date', true ],
        ];
    }

    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="lead_ids[]" value="%d" />',
            absint( $item->id )
        );
    }

    protected function get_bulk_actions() {
        return [
            'delete' => __( 'Delete', 'np-lead-chatbot' ),
        ];
    }
    public function process_bulk_action() {
        if ( 'delete' !== $this->current_action() ) {
            return;
        }

        if ( empty( $_POST['lead_ids'] ) || ! is_array( $_POST['lead_ids'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        check_admin_referer( 'bulk-' . $this->_args['plural'] );

        global $wpdb;

        $ids = array_map( 'intval', $_POST['lead_ids'] );
        $ids = implode( ',', $ids );

        $wpdb->query(
            "DELETE FROM {$wpdb->prefix}npleadchat_leads WHERE id IN ($ids)"
        );
    }


    protected function get_searchable_columns() {
        return [ 'name', 'email', 'phone', 'message' ];
    }
    public function prepare_items() {
        $this->process_bulk_action();
        $per_page = 10;

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [ $columns, $hidden, $sortable ];

        $orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
        $order   = ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';
        $search  = ! empty( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';

        $data = NPLEADCHAT_DB::npleadchat_get_leads( $orderby, $order, $search );

        $current_page = $this->get_pagenum();
        $total_items  = count( $data );

        $this->items = array_slice( $data, ( $current_page - 1 ) * $per_page, $per_page );

        $this->set_pagination_args( [
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ] );
    }

    public function column_default( $item, $column_name ) {
        return esc_html( $item->$column_name ?? '' );
    }
}
