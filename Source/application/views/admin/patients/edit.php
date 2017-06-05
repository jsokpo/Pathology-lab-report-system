
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Update Patient</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <?php
        //flash messages
        if ($this->session->flashdata('flash_message')) {
            if ($this->session->flashdata('flash_message') == 'updated') {
                echo '<div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <strong>Well done!</strong> patient updated with success. <a href="' . base_url() . 'admin/patients/list"> Go to List </a>
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
                Enter Patient Details
            </div>


            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
<?php echo form_open('admin/patients/update/'.$id, $attributes); ?>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input class="form-control" name="fullname" value="<?php echo $patient[0]['fullname']; ?>" type="text">
                        </div>

                        <div class="form-group">
                            <label>Pass Code</label>
                            <input class="form-control" name="passcode" value="" type="text">
                            <p class="help-block">Example block-level help text here.</p>
                        </div>    

                        <div class="form-group">                                  
                            <label>Phone</label>
                            <input class="form-control" name="phone" value="<?php echo $patient[0]['phone']; ?>" type="text">
                        </div>

                        <div class="form-group">                                  
                            <label>Email</label>
                            <input class="form-control" name="email" value="<?php echo $patient[0]['email']; ?>" type="text">
                        </div>
                        <div class="form-group">                            
                            <button class="btn btn-default" type="submit"> Update </button>
                            <button class="btn btn-default" type="reset" onclick="javascript:location = '<?php echo base_url(); ?>admin/patients/list'"> Cancel </button>
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
//

