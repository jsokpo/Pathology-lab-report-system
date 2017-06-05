<?php $this->load->view('includes/header'); ?>

<?php $data = isset($data) ? $data : array();
$this->load->view($main_content, $data); ?>

<?php $this->load->view('includes/footer'); ?> 