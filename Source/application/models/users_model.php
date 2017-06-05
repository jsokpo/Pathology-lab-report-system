<?php

class Users_model extends CI_Model {

    /**
     * Validate the login's data with the database
     * @param string $username
     * @param string $password
     * @return void
     */
    function validate($username, $password) {
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        $query = $this->db->get('users');

        if ($query->num_rows == 1) {
            return true;
        }
    }

    /**
     * Validate the login's data with the database
     * @param string $fullname
     * @param string $passcode
     * @return void
     */
    function validatePatient($fullname, $passcode) {
        $this->db->where('fullname', $fullname);
        $this->db->where('passcode', $passcode);
        $query = $this->db->get('patients');

        if ($query->num_rows == 1) {
            return true;
        }
    }

    /**
     * Validate the login's data with the database
     * @param string $fullname
     * @param string $passcode
     * @return void
     */
    function getPatientDetails($fullname, $passcode) {
        $this->db->where('fullname', $fullname);
        $this->db->where('passcode', $passcode);
        $query = $this->db->get('patients');
        return $query->result_array();
    }

    /**
     * Serialize the session data stored in the database, 
     * store it in a new array and return it to the controller 
     * @return array
     */
    function get_db_session_data() {
        $query = $this->db->select('user_data')->get('ci_sessions');
        $user = array(); /* array to store the user data we fetch */
        foreach ($query->result() as $row) {
            $udata = unserialize($row->user_data);
            /* put data in array using username as key */
            $user['user_name'] = $udata['user_name'];
            $user['is_logged_in'] = $udata['is_logged_in'];
        }
        return $user;
    }

    /**
     * Store the new user's data into the database
     * @return boolean - check the insert
     */
    function create_member() {

        $this->db->where('username', $this->input->post('username'));
        $query = $this->db->get('users');

        if ($query->num_rows > 0) {
            echo '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>';
            echo "Username already taken";
            echo '</strong></div>';
        } else {

            $new_member_insert_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email_addres' => $this->input->post('email_address'),
                'user_name' => $this->input->post('username'),
                'pass_word' => md5($this->input->post('password'))
            );
            $insert = $this->db->insert('membership', $new_member_insert_data);
            return $insert;
        }
    }

//create_member
}

