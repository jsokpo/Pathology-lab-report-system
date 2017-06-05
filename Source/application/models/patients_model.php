<?php

class Patients_model extends CI_Model {

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
    public function get_patient_by_id($id) {
        $this->db->select('*');
        $this->db->from('patients');
        $this->db->where('md5(concat(id,id))', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_json($query) {
        $this->db->select('DISTINCT(fullname)');
        $this->db->from('patients');
        $this->db->like('fullname', $query);
        $this->db->limit(10, 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Fetch patients data from the database
     * possibility to mix search, filter and order
     * @param string $search_string 
     * @param strong $order
     * @param string $order_type 
     * @param int $limit_start
     * @param int $limit_end
     * @return array
     */
    public function get_patients($search_string = null, $order = null, $order_type = 'desc', $limit_start, $limit_end) {

        $this->db->select('patients.id');
        $this->db->select('patients.fullname');
        $this->db->select('patients.email');
        $this->db->select('patients.phone');
        $this->db->from('patients');

        if ($search_string) {
            $this->db->like('fullname', $search_string);
        }


        if ($order) {
            $this->db->order_by($order, $order_type);
        } else {
            $this->db->order_by('id', $order_type);
        }


        $this->db->limit($limit_start, $limit_end);
        //$this->db->limit('4', '4');


        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Count the number of rows
     * @param int $search_string
     * @param int $order
     * @return int
     */
    function count_patients($search_string = null, $order = null) {
        $this->db->select('*');
        $this->db->from('patients');

        if ($search_string) {
            $this->db->like('fullname', $search_string);
        }
        if ($order) {
            $this->db->order_by($order, 'Asc');
        } else {
            $this->db->order_by('id', 'Asc');
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * Store the new item into the database
     * @param array $data - associative array with data to store
     * @return boolean 
     */
    function store_patient($data) {
        $insert = $this->db->insert('patients', $data);
        return $insert;
    }

    /**
     * Update patient
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function update_patient($id, $data) {
        $this->db->where('md5(concat(id,id))', $id);
        $this->db->update('patients', $data);
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
     * Delete patient
     * @param int $id - patient id
     * @return boolean
     */
    function delete_patient($id) {
        $this->db->where('md5(concat(id,id))', $id);
        $this->db->delete('patients');
    }

}
?>

