
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Patient</h1>
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
                                <strong>Well done!</strong> new patient created with success.  <a href="' . base_url() . 'admin/patients/list"> Go to List </a>
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
                        <?php echo form_open('admin/patients/add', $attributes); ?>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input class="form-control" name="fullname" value="<?php echo set_value('fullname'); ?>" type="text">
                        </div>

                        <div class="form-group">
                            <label>Pass Code</label>
                            <input class="form-control" name="passcode" value="<?php echo set_value('passcode'); ?>" type="text">
                        </div>    

                        <div class="form-group">                                  
                            <label>Phone</label>
                            <input class="form-control" name="phone" value="<?php echo set_value('phone'); ?>" type="text">
                            <p class="help-block"> Please add International code Ex. +91 9008427482</p>
                        </div>

                        <div class="form-group">                                  
                            <label>Email</label>
                            <input class="form-control" name="email" value="<?php echo set_value('email'); ?>" type="text">
                        </div>
                        <div class="form-group">                            
                            <button class="btn btn-default" type="submit"> Add </button>
                            <button class="btn btn-default" type="reset"  onclick="javascript:location = '<?php echo base_url(); ?>admin/patients/list'"> Cancel </button>
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