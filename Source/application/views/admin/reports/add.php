<script type="text/javascript" src="<?php echo base_url(); ?>assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-typeahead.min.js"></script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Report</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <?php
        //flash messages
        if (isset($flash_message)) {
            if ($flash_message == TRUE) {
                echo '<div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <strong>Well done!</strong> new report created with success.  <br> To add another report for same patient <a href="' . base_url() . 'admin/reports/add/?id=' . $id . '"> Click Here </a>
                                  <br>   or <a href="' . base_url() . 'admin/patients/list"> Go to Patients List </a>
                                  or <a href="' . base_url() . 'admin/reports/list"> Go to Reports List </a>
                                </div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                        <strong>Oh snap!</strong> change a few things up and try submitting again.
                                        </div>';
            }
        }

        //form data
        $attributes = array('role' => 'form', 'id' => '');
        //form validation
        echo validation_errors();
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                Enter Report Details for <?php echo $patient_fullname; ?>
            </div>


            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
<?php echo form_open_multipart('admin/reports/add?id=' . $id, $attributes); ?>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input class="form-control" name="fullname" id="fullname" value="<?php echo $patient_fullname; ?>" type="text" autocomplete="off" readonly="">
                            <input  name="patient_id"  value="<?php echo $patient_id; ?>" type="hidden">
                            <p class="help-block">This field is read only for avoiding wrong patient name. </p>
                        </div>

                        <div class="form-group">
                            <label> Report Type </label>
                            <label class="radio-inline">
                                <input class="report_type" type="radio" checked="" value="pdf" id="pdf" name="type"> PDF
                            </label>
                            <label class="radio-inline">
                                <input class="report_type" type="radio" value="text" id="text" name="type"> Text
                            </label>

                        </div>   

                        <div class="form-group" id="pdf_field">
                            <label>File input</label>
                            <input type="file" name="file">
                        </div>
                        <div class="form-group" id="text_field">
                            <label>Text Editor</label>
<?php
echo $this->ckeditor->editor("textarea_text", "Enter report details");
?>
                        </div>

                        <div class="form-group">                            
                            <button class="btn btn-default" type="submit"> Add </button>
                            <button class="btn btn-default" type="reset"  onclick="javascript:location = '<?php echo base_url(); ?>admin/reports/list'"> Cancel </button>
                        </div>    
<?php echo form_close(); ?>
                    </div>
                    <!-- /.col-lg-6 (nested) -->

                </div>
                <!-- /.row (nested) -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
    $('#fullname').typeahead({
        ajax: '<?php echo base_url(); ?>admin/patients/api'
    });
    $(document).ready(function () {
        $('#text_field').hide();
        $('.report_type').click(function () {

            if ($(this).prop("checked")) {
                if (this.value == 'pdf') {
                    $('#pdf_field').show();
                    $('#text_field').hide();
                } else {
                    $('#pdf_field').hide();
                    $('#text_field').show();
                }
            }
        });


    });

</script>   

