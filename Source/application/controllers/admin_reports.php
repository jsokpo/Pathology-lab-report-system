<?php

class Admin_reports extends CI_Controller {

    /**
     * name of the folder responsible for the views 
     * which are manipulated by this controller
     * @constant string
     */
    const VIEW_FOLDER = 'admin/reports';

    private $filename = '';

    /**
     * Responsable for auto load the model
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
        $patient_id = null;
        $patient_id = $this->input->get('id');


        //pagination settings
        $config['per_page'] = 5;

        $config['base_url'] = base_url() . 'admin/reports';
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
        }
        //load the view
        $data['main_content'] = 'admin/reports/list';
        $this->load->view('includes/template', $data);
    }

//index

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

    public function add() {
        $this->load->library('CKEditor');
        $this->ckeditor->basePath = base_url() . 'assets/ckeditor/';
//        $this->ckeditor->config['toolbar'] = array(
//                        array( 'Source', '-', 'Bold', 'Italic', 'Underline', '-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','-','NumberedList','BulletedList' )
//                                                            );
        $this->ckeditor->config['language'] = 'en';
        $this->ckeditor->config['width'] = '730px';
        $this->ckeditor->config['height'] = '300px';
        $this->ckeditor->config['filebrowserBrowseUrl'] = base_url() . 'application/third_party/kcfinder/browse.php?opener=ckeditor&type=files';
        $this->ckeditor->config['filebrowserImageBrowseUrl'] = base_url() . 'application/third_party/kcfinder/browse.php?opener=ckeditor&type=images';
        $this->ckeditor->config['filebrowserFlashBrowseUrl'] = base_url() . 'application/third_party/kcfinder/browse.php?opener=ckeditor&type=flash';
        $this->ckeditor->config['filebrowserUploadUrl'] = base_url() . 'application/third_party/kcfinder/upload.php?opener=ckeditor&type=files';
        $this->ckeditor->config['filebrowserBrowseUrl'] = base_url() . 'application/third_party/kcfinder/upload.php?opener=ckeditor&type=images';
        $this->ckeditor->config['filebrowserBrowseUrl'] = base_url() . 'application/third_party/kcfinder/upload.php?opener=ckeditor&type=flash';
        //       $this->ckeditor->config['extraPlugins '] = 'filebrowser';
        //  See more at: http://webeasystep.com/blog/view_article/Integrate_KCfinder_with_CKEditor_and_Codeigniter#sthash.3MrER2K4.dpuf
//
//        //Add Ckfinder to Ckeditor
//        $this->ckfinder->SetupCKEditor($this->ckeditor,'../../asset/ckfinder/'); 
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            //form validation
            $this->form_validation->set_rules('fullname', 'fullname', 'required');
            if ($this->input->post('type') == 'pdf') {
                $this->form_validation->set_rules('file', 'file', 'required');
                $this->form_validation->set_rules('file', 'file', 'callback_upload_validation');
            } else {
                $this->form_validation->set_rules('textarea_text', 'textarea_text', 'required');
            }

            $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissable"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');


            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                $data_to_store = array(
                    'patient_id' => $this->input->post('patient_id'),
                    'type' => $this->input->post('type'),
                    'file' => ($this->input->post('type') == 'pdf') ? $this->filename : '',
                    'text' => ($this->input->post('type') == 'text') ? $this->input->post('textarea_text') : '',
                    'date' => date("Y-m-d H:i:s"),
                    'entered_by' => $this->session->userdata('username')
                );
                //if the insert has returned true then we show the flash message
                if ($this->reports_model->store_report($data_to_store)) {
                    $data['flash_message'] = TRUE;
                } else {
                    $data['flash_message'] = FALSE;
                }
            }
        }
        //load the view
        $data['main_content'] = 'admin/reports/add';
        $id = $this->input->get('id');
        if (strlen($id) > 0) {
            $this->load->model('patients_model');
            $patient_data = $this->patients_model->get_patient_by_id($id);
            $data['data']['id'] = $id;
            $data['data']['patient_id'] = $patient_data[0]['id'];
            $data['data']['patient_fullname'] = $patient_data[0]['fullname'];
            $this->load->view('includes/template', $data);
        } else {
            redirect('admin/patients');
        }
    }

    function upload_validation() {
        $result = $this->do_upload();
        if ($result['status'] == 'success') {
            return true;
        } else {
            $this->form_validation->set_message('upload_validation', $result['message']);
            return false;
        }
    }

    function do_upload() {
        $name = time();
        $this->filename = $name . '.pdf';
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'pdf';
        $config['file_name'] = $name;
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $error = array('message' => $this->upload->display_errors());
            $error['status'] = 'failed';
            return $error;
        } else {
            $data = array('message' => $this->upload->data());
            $data['status'] = 'success';
            return $data;
        }
    }

    /**
     * Update item by his id
     * @return void
     */
    public function update() {

        $this->load->library('CKEditor');

        $this->ckeditor->basePath = base_url() . 'assets/ckeditor/';
//        $this->ckeditor->config['toolbar'] = array(
//                        array( 'Source', '-', 'Bold', 'Italic', 'Underline', '-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','-','NumberedList','BulletedList' )
//                                                            );
        $this->ckeditor->config['language'] = 'en';
        $this->ckeditor->config['width'] = '730px';
        $this->ckeditor->config['height'] = '300px';
        $this->ckeditor->config['filebrowserBrowseUrl'] = base_url() . 'application/third_party/kcfinder/browse.php?opener=ckeditor&type=files';
        $this->ckeditor->config['filebrowserImageBrowseUrl'] = base_url() . 'application/third_party/kcfinder/browse.php?opener=ckeditor&type=images';
        $this->ckeditor->config['filebrowserFlashBrowseUrl'] = base_url() . 'application/third_party/kcfinder/browse.php?opener=ckeditor&type=flash';
        $this->ckeditor->config['filebrowserUploadUrl'] = base_url() . 'application/third_party/kcfinder/upload.php?opener=ckeditor&type=files';
        $this->ckeditor->config['filebrowserBrowseUrl'] = base_url() . 'application/third_party/kcfinder/upload.php?opener=ckeditor&type=images';
        $this->ckeditor->config['filebrowserBrowseUrl'] = base_url() . 'application/third_party/kcfinder/upload.php?opener=ckeditor&type=flash';
        //patient id 
        $id = $this->uri->segment(4);
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            //form validation
            $this->form_validation->set_rules('fullname', 'fullname', 'required');
            if ($this->input->post('type') == 'pdf') {
                $this->form_validation->set_rules('file', 'file', 'required');
                $this->form_validation->set_rules('file', 'file', 'callback_upload_validation');
            } else {
                $this->form_validation->set_rules('textarea_text', 'textarea_text', 'required');
            }

            $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissable"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');


            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                $data_to_store = array(
                    'type' => $this->input->post('type'),
                    'file' => ($this->input->post('type') == 'pdf') ? $this->filename : '',
                    'text' => ($this->input->post('type') == 'text') ? $this->input->post('textarea_text') : '',
                    'entered_by' => $this->session->userdata('username')
                );
                //if the insert has returned true then we show the flash message
                if ($this->reports_model->update_report($id, $data_to_store) == TRUE) {
                    $this->session->set_flashdata('flash_message', 'updated');
                } else {
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/reports/update/' . $id . '');
            }//validation run
        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data
        //patient data 
        $data['report'] = $this->reports_model->get_joined_reports_by_id($id);
        //load the view
        $data['data']['id'] = $id;
        $data['data']['patient_id'] = $data['report'][0]['id'];
        $data['data']['patient_fullname'] = $data['report'][0]['fullname'];
        $data['main_content'] = 'admin/reports/edit';
        $this->load->view('includes/template', $data);
    }

//update

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
     * @return void
     */
    public function delete() {
        //patient id 
        $id = $this->uri->segment(4);
        $this->reports_model->delete_report($id);
        redirect('admin/reports');
    }

//edit
}
