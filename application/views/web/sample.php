<?php
public function get_payment_amount_summary() {
    $brand_name = $this->session->userdata('brand_name');
    if (!$brand_name) {
        echo json_encode(['status' => 'error', 'message' => '브랜드명이 없습니다.']);
        return;
    }

    $this->load->database();

    $query = $this->db->select('shopping_mall, order_date, SUM(payment_amount) AS total_payment')
        ->from('product_info')
        ->where('brand_name', $brand_name)
        ->group_by(['shopping_mall', 'order_date'])
        ->order_by('order_date', 'DESC')
        ->get();

    $result = $query->result_array();

    echo json_encode(['status' => 'success', 'data' => $result]);
}
?>