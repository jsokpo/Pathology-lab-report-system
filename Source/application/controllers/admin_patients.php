<?php

class Admin_patients extends CI_Controller {

    /**
     * Responsable for auto load the model
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('patients_model');
        $this->load->model('reports_model');

        if (!$this->session->userdata('is_logged_in') || $this->session->userdata('type') != 'admin') {
            redirect('admin/login');
        }
    }

    /**
     * Load the main view with all the current model model's data.
     * @return void
     */
    public function index() {

        //all the posts sent by the view

        $search_string = $this->input->post('search_string');
        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');

        //pagination settings
        $config['per_page'] = 5;
        $config['base_url'] = base_url() . 'admin/patients';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul class="pagination" >';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        //limit end
        $page = $this->uri->segment(3);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0) {
            $limit_end = 0;
        }

        //if order type was changed
        if ($order_type) {
            $filter_session_data['order_type'] = $order_type;
        } else {
            //we have something stored in the session? 
            if ($this->session->userdata('order_type')) {
                $order_type = $this->session->userdata('order_type');
            } else {
                //if we have nothing inside session, so it's the default "Asc"
                $order_type = 'Asc';
            }
        }
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type;


        //we must avoid a page reload with the previous session data
        //if any filter post was sent, then it's the first time we load the content
        //in this case we clean the session filter data
        //if any filter post was sent but we are in some page, we must load the session data
        //filtered && || paginated
        if ($search_string !== false && $order !== false || $this->uri->segment(3) == true) {

            /*
              The comments here are the same for line 79 until 99

              if post is not null, we store it in session data array
              if is null, we use the session data already stored
              we save order into the the var to load the view with the param already selected
             */

            $filter_session_data = array();

            if ($search_string) {
                $filter_session_data['search_string_selected'] = $search_string;
            } else {
                $search_string = $this->session->userdata('search_string_selected');
            }
            $data['search_string_selected'] = $search_string;

            if ($order) {
                $filter_session_data['order'] = $order;
            } else {
                $order = $this->session->userdata('order');
            }
            $data['order'] = $order;

            //save session data into the session
            $this->session->set_userdata($filter_session_data);

            //fetch reports data into arrays
            $data['reports'] = $this->reports_model->get_reports();

            $data['count_patients'] = $this->patients_model->count_patients($search_string, $order);
            $config['total_rows'] = $data['count_patients'];

            //fetch sql data into arrays
            if ($search_string) {
                if ($order) {
                    $data['patients'] = $this->patients_model->get_patients($search_string, $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['patients'] = $this->patients_model->get_patients($search_string, '', $order_type, $config['per_page'], $limit_end);
                }
            } else {
                if ($order) {
                    $data['patients'] = $this->patients_model->get_patients('', $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['patients'] = $this->patients_model->get_patients('', '', $order_type, $config['per_page'], $limit_end);
                }
            }
        } else {

            //clean filter data inside section
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays
            $data['reports'] = $this->reports_model->get_reports();
            $data['count_patients'] = $this->patients_model->count_patients();
            $config['total_rows'] = $data['count_patients'];
            $data['patients'] = $this->patients_model->get_patients('', '', $order_type, $config['per_page'], $limit_end);
        }//!isset($report_id) && !isset($search_string) && !isset($order)
        //initializate the panination helper 
        $this->pagination->initialize($config);

        //load the view
        $data['main_content'] = 'admin/patients/list';
        $this->load->view('includes/template', $data);
    }

//index

    public function add() {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            //form validation
            $this->form_validation->set_rules('fullname', 'fullname', 'required');
            $this->form_validation->set_rules('passcode', 'passcode', 'required');
            $this->form_validation->set_rules('fullname', 'fullname', 'callback_check_dublicate');
            $this->form_validation->set_rules('phone', 'phone', 'required');
            $this->form_validation->set_rules('email', 'email', 'valid_email');
            $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissable"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                $data_to_store = array(
                    'fullname' => $this->input->post('fullname'),
                    'email' => $this->input->post('email'),
                    'passcode' => $this->input->post('passcode'),
                    'phone' => $this->input->post('phone'),
                );
                //if the insert has returned true then we show the flash message
                if ($this->patients_model->store_patient($data_to_store)) {
                    $data['flash_message'] = TRUE;
                } else {
                    $data['flash_message'] = FALSE;
                }
            }
        }
        //fetch reports data to populate the select field
        $data['reports'] = $this->reports_model->get_reports();
        //load the view
        $data['main_content'] = 'admin/patients/add';
        $this->load->view('includes/template', $data);
    }

    public function check_dublicate() {
        $fullname = $this->input->post('fullname');
        $passcode = $this->input->post('passcode');
        $this->load->model('Users_model');
        $is_valid = $this->Users_model->validatePatient($fullname, $passcode);
        if ($is_valid) {
            $this->form_validation->set_message('check_dublicate', ' Same Patient name and passcode combination exist. Please try different. ');
            return false;
        } else {
            return true;
        }
    }

    /**
     * Update item by his id
     * @return void
     */
    public function update() {
        //patient id 
        $id = $this->uri->segment(4);

        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //form validation
            $this->form_validation->set_rules('fullname', 'fullname', 'required');
            $this->form_validation->set_rules('fullname', 'fullname', 'callback_check_dublicate');
            $this->form_validation->set_rules('phone', 'phone', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            //if the form has passed through the validation
            if ($this->form_validation->run()) {

                $data_to_store = array(
                    'fullname' => $this->input->post('fullname'),
                    'phone' => $this->input->post('phone'),
                    'email' => $this->input->post('email')
                );

                if (strlen($this->input->post('passcode')) > 0) {
                    $data_to_store['passcode'] = $this->input->post('passcode');
                }

                //if the insert has returned true then we show the flash message
                if ($this->patients_model->update_patient($id, $data_to_store) == TRUE) {
                    $this->session->set_flashdata('flash_message', 'updated');
                } else {
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/patients/update/' . $id . '');
            }//validation run
        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data
        //patient data 
        $data['patient'] = $this->patients_model->get_patient_by_id($id);
        $data['data']['id'] = $id;

        //load the view
        $data['main_content'] = 'admin/patients/edit';
        $this->load->view('includes/template', $data);
    }

//update

    public function sms() {
        $this->load->helper('twilio');
        $id = $this->uri->segment(4);
        $data = $this->patients_model->get_patient_by_id($id);
        send_sms($data[0]['phone'], 'Your Passcode is : ' . $data[0]['passcode'] . '   --- by Crossover Team');
    }

    /**
     * Delete patient by his id
     * @return void
     */
    public function delete() {
        //patient id 
        $id = $this->uri->segment(4);
        $this->patients_model->delete_patient($id);
        redirect('admin/patients');
    }

//edit
}
