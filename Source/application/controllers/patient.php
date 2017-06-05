<?php

class Patient extends CI_Controller {

    /**
     * Responsable for auto load the model
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('reports_model');
    }

    /**
     * Check if the user is logged in, if he's not, 
     * send him to the login page
     * @return void
     */
    function index() {
        if ($this->session->userdata('is_logged_in')) {
            redirect('patient/dashboard');
        } else {
            $this->load->view('patient/login');
        }
    }

    /**
     * Showing user dashboard
     * @return void
     */
    function dashboard() {
        if (!$this->session->userdata('is_logged_in')) {
            redirect('patient/login');
        }
        //all the posts sent by the view
        $search_string = $this->input->post('search_string');
        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');
        $patient_id = null;
        $patient_id = $this->session->userdata('id');

        //pagination settings
        $config['per_page'] = 5;

        $config['base_url'] = base_url() . 'patient/dashboard';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        if (count($_GET) > 0) {
            $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'] . '?' . http_build_query($_GET);
        }

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
                $order_type = 'desc';
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
            if (isset($filter_session_data)) {
                $this->session->set_userdata($filter_session_data);
            }

            //fetch sql data into arrays
            $data['count_patients'] = $this->reports_model->count_reports($search_string, $order, $patient_id);
            $config['total_rows'] = $data['count_patients'];

            //fetch sql data into arrays
            if ($search_string) {
                if ($order) {
                    $data['reports'] = $this->reports_model->get_reports($search_string, $order, $order_type, $config['per_page'], $limit_end, $patient_id);
                } else {
                    $data['reports'] = $this->reports_model->get_reports($search_string, '', $order_type, $config['per_page'], $limit_end, $patient_id);
                }
            } else {
                if ($order) {
                    $data['reports'] = $this->reports_model->get_reports('', $order, $order_type, $config['per_page'], $limit_end, $patient_id);
                } else {
                    $data['reports'] = $this->reports_model->get_reports('', '', $order_type, $config['per_page'], $limit_end, $patient_id);
                }
            }
        } else {

            //clean filter data inside section
            $filter_session_data['report_selected'] = null;
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays
            $data['count_patients'] = $this->reports_model->count_reports(null, null, $patient_id);
            $data['reports'] = $this->reports_model->get_reports('', '', $order_type, $config['per_page'], $limit_end, $patient_id);
            $config['total_rows'] = $data['count_patients'];
        }//!isset($search_string) && !isset($order)
        //initializate the panination helper 
        $this->pagination->initialize($config);
        if ($patient_id != null) {
            $data['patient_id'] = $patient_id;
            $data['patient_name'] = $this->session->userdata('username');
        }

        $this->load->view('patient/dashboard', $data);
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

        $fullname = $this->input->post('fullname');
        $passcode = $this->input->post('passcode');

        $is_valid = $this->Users_model->validatePatient($fullname, $passcode);

        if ($is_valid) {
            $patient = $this->Users_model->getPatientDetails($fullname, $passcode);
            $data = array(
                'username' => $fullname,
                'id' => md5($patient[0]['id'] . $patient[0]['id']),
                'type' => 'patient',
                'is_logged_in' => true
            );
            $this->session->set_userdata($data);

            redirect('patient/dashboard');
        } else { // incorrect username or password
            $data['message_error'] = TRUE;
            $this->load->view('patient/login', $data);
        }
    }

    public function pdf($id) {
        $data = $this->reports_model->get_report_text_by_id($id);
        $html = $data[0]['text'];
        $this->load->helper(array('dompdf', 'file'));
        // page info here, db calls, etc.     
        //$html = $this->load->view('controller/viewfile', $data, true);
        $data = pdf_create($html, '', false);
        write_file('uploads/report.pdf', $data);
        $path = FCPATH    . "/uploads/report.pdf";
        $filename = "report.pdf";
        header('Content-Transfer-Encoding: binary');  // For Gecko browsers mainly
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
        header('Accept-Ranges: bytes');  // For download resume
        header('Content-Length: ' . filesize($path));  // File size
        header('Content-Encoding: none');
        header('Content-Type: application/pdf');  // Change this mime type if the file is not PDF
        header('Content-Disposition: attachment; filename=' . $filename);  // Make the browser display the Save As dialog
        readfile($path);  //this is necessary in order to get it to actually download the file, otherwise it will be 0Kb
        exit;
        //if you want to write it to disk and/or send it as an attachment    
    }

    public function view_pdf($id) {
        $data = $this->reports_model->get_report_text_by_id($id);
        $html = $data[0]['text'];
        $this->load->helper(array('dompdf', 'file'));
        // page info here, db calls, etc.     
        //$html = $this->load->view('controller/viewfile', $data, true);
        $data = pdf_create($html, '', false);
        write_file('uploads/report.pdf', $data);
        $path = FCPATH    . "/uploads/report.pdf";
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=filename.pdf");
        @readfile($path);
    }

    public function save_pdf($id) {
        $data = $this->reports_model->get_report_text_by_id($id);
        $html = $data[0]['text'];
        $this->load->helper(array('dompdf', 'file'));
        // page info here, db calls, etc.     
        //$html = $this->load->view('controller/viewfile', $data, true);
        $data = pdf_create($html, '', false);
        write_file('uploads/report.pdf', $data);
    }

    public function email($id) {
        $this->load->library('email');
        $data = $this->reports_model->get_joined_reports_by_id($id);
        print_r($data);
        $html = $data[0]['text'];
        $subject = 'Your Report';
        $message = '<p>Hi ' . $data[0]['fullname'] . ', '
                . '<br>'
                . '<p>'
                . ' Please find your report as attachment. '
                . '</p>'
                . '<br>'
                . '<br>'
                . 'Thanks & Regards,'
                . '<br>'
                . 'CrossOver Team'
                . '</p>';

        // Get full html:
        $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=' . strtolower(config_item('charset')) . '" />
                    <title>' . html_escape($subject) . '</title>
                    <style type="text/css">
                        body {
                            font-family: Arial, Verdana, Helvetica, sans-serif;
                            font-size: 16px;
                        }
                    </style>
                </head>
                <body>
                ' . $message . '
                </body>
                </html>';
        // Also, for getting full html you may use the following internal method:
        //$body = $this->email->full_html($subject, $message);

        if ($data[0]['type'] == 'pdf') {
            $result = $this->email
                    ->from('sridhar.posnic@gmail.com')
                    ->reply_to('sridhar.posnic@gmail.com')    // Optional, an account where a human being reads.
                    ->to($data[0]['email'])
                    ->attach(FCPATH . 'uploads/' . $data[0]['email'])
                    ->subject($subject)
                    ->message($body)
                    ->send();
        } else {
            $this->save_pdf($id);
            $result = $this->email
                    ->from('sridhar.posnic@gmail.com')
                    ->reply_to('sridhar.posnic@gmail.com')    // Optional, an account where a human being reads.
                    ->to($data[0]['email'])
                    ->attach(FCPATH . 'uploads/report.pdf')
                    ->subject($subject)
                    ->message($body)
                    ->send();
        }



        var_dump($result);
        echo '<br />';
        echo $this->email->print_debugger();

        exit;
    }

    /**
     * Delete patient by his id
     * @return json
     */
    public function api() {
        $this->load->model('patients_model');
        $query = $this->input->get('query');
        $json = $this->patients_model->get_json($query);
        $result = array();
        foreach ($json as $index => $value) {
            $result[] = $value['fullname'];
        }
        echo json_encode($result);
    }

//edit

    /**
     * Destroy the session, and logout the user.
     * @return void
     */
    function logout() {
        $this->session->sess_destroy();
        redirect('patient');
    }

}
