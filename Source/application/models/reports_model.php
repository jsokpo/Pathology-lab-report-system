<?php

class reports_model extends CI_Model {

    /**
     * Responsable for auto load the database
     * @return void
     */
    public function __construct() {
        $this->load->database();
    }

    /**
     * Get patient by his is
     * @param int $patient_id 
     * @return array
     */
    public function get_reports_by_id($id) {
        $this->db->select('*');
        $this->db->from('reports');
        $this->db->where('md5(concat(id,date))', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get patient by his is
     * @param int $patient_id 
     * @return array
     */
    public function get_joined_reports_by_id($id) {
        $this->db->select('p.fullname as fullname, p.email as email, r.id as id, r.type as type, r.date as date, r.file as file, r.text as text');
        $this->db->from('reports r');
        $this->db->join('patients p', 'p.id = r.patient_id', 'left');
        $this->db->where('md5(concat(r.id,r.date))', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get patient by his is
     * @param int $patient_id 
     * @return array
     */
    public function get_report_text_by_id($id) {
        $this->db->select('text');
        $this->db->from('reports');
        $this->db->where('md5(concat(id,date))', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Fetch reports data from the database
     * possibility to mix search, filter and order
     * @param string $search_string 
     * @param strong $order
     * @param string $order_type 
     * @param int $limit_start
     * @param int $limit_end
     * @return array
     */
    public function get_reports($search_string = null, $order = null, $order_type = 'desc', $limit_start = null, $limit_end = null, $patient_id = null) {

        $this->db->select('p.fullname as fullname, p.email as email, r.id as id, r.type as type, r.date as date, r.file as file');
        $this->db->from('reports r');
        $this->db->join('patients p', 'p.id = r.patient_id', 'left');
        if ($search_string) {
            $this->db->like('p.fullname', $search_string);
        }
        if ($order) {
            $this->db->order_by($order, $order_type);
        } else {
            $this->db->order_by('r.id', $order_type);
        }

        if ($limit_start && $limit_end) {
            $this->db->limit($limit_start, $limit_end);
        }

        if ($limit_start != null) {
            $this->db->limit($limit_start, $limit_end);
        }

        if ($patient_id != null) {
            $this->db->where('md5(concat(p.id,p.id))', $patient_id);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Count the number of rows
     * @param int $search_string
     * @param int $order
     * @return int
     */
    function count_reports($search_string = null, $order = null, $patient_id = null) {
        $this->db->select('p.fullname as fullname, p.phone as phone,  r.id as id, r.type as type, r.date as date');
        $this->db->from('reports r');
        $this->db->join('patients p', 'p.id = r.patient_id', 'left');
        if ($search_string) {
            $this->db->like('fullname', $search_string);
            $this->db->or_like('phone', $search_string);
        }
        if ($order) {
            $this->db->order_by($order, 'Asc');
        } else {
            $this->db->order_by('id', 'Desc');
        }
        if ($patient_id != null) {
            $this->db->where('md5(concat(p.id,p.id))', $patient_id);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * Store the new item into the database
     * @param array $data - associative array with data to store
     * @return boolean 
     */
    function store_report($data) {
        $insert = $this->db->insert('reports', $data);
        return $insert;
    }

    /**
     * Update report
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function update_report($id, $data) {
        $this->db->where('md5(concat(id,date))', $id);
        $this->db->update('reports', $data);
        $report = array();
        $report['error'] = $this->db->_error_number();
        $report['message'] = $this->db->_error_message();
        if ($report !== 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete reportr
     * @param int $id - report id
     * @return boolean
     */
    function delete_report($id) {
        $this->db->where('md5(concat(id,date))', $id);
        $this->db->delete('reports');
    }

}
?>

