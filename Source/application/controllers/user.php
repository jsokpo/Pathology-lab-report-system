<?php

class User extends CI_Controller {

    /**
     * Check if the user is logged in, if he's not, 
     * send him to the login page
     * @return void
     */
    function index() {
        if ($this->session->userdata('is_logged_in') || $this->session->userdata('type') == 'admin') {
            redirect('admin/dashboard');
        } else {
            $this->load->view('admin/login');
        }
    }

    /**
     * Showing user dashboard
     * @return void
     */
    function dashboard() {
        if (!$this->session->userdata('is_logged_in') || $this->session->userdata('type') != 'admin') {
            $this->load->view('admin/login');
        } else {
            $this->load->model('patients_model');
            $this->load->model('reports_model');
            $data['data']['patients_count'] = $this->patients_model->count_patients();
            $data['data']['reports_count'] = $this->reports_model->count_reports();
            $data['main_content'] = 'admin/dashboard';
            $this->load->view('includes/template', $data);
        }
    }

    /**
     * encript the password 
     * @return mixed
     */
    function __encrip_password($password) {
        return md5($password);
    }

    /**
     * check the username and the password with the database
     * @return void
     */
    function validate_credentials() {

        $this->load->model('Users_model');

        $username = $this->input->post('username');
        $password = $this->__encrip_password($this->input->post('password'));

        $is_valid = $this->Users_model->validate($username, $password);

        if ($is_valid) {
            $data = array(
                'username' => $username,
                'type' => 'admin',
                'is_logged_in' => true
            );
            $this->session->set_userdata($data);

            // Hotcode solution for KC Finder Security issue
            session_start();
            $_SESSION['kc'] = 'yes';

            redirect('admin/dashboard');
        } else { // incorrect username or password
            $data['message_error'] = TRUE;
            $this->load->view('admin/login', $data);
        }
    }

    /**
     * Destroy the session, and logout the user.
     * @return void
     */
    function logout() {
        $this->session->sess_destroy();
        // security hot coded for kcfinder
        session_start();
        $_SESSION['kc'] = 'no';
        redirect('admin');
    }

}

