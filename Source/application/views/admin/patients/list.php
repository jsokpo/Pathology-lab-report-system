    
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Patients</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-success alert-dismissable" id="message">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            SMS Sent !!! 
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                Patients List

                <div class="pull-right" > <button class="btn btn-primary fa  fa-plus-square" type="button" onclick="javascript:location = '<?php echo base_url(); ?>admin/patients/add'" > &nbsp; Add </button> </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <?php
                $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');

                //save the columns names in a array that we will use as filter         
                $options_patients = array();
                foreach ($patients as $array) {
                    foreach ($array as $key => $value) {
                        $options_patients[$key] = $key;
                    }
                    break;
                }

                echo form_open('admin/patients', $attributes);
                ?>
                <div class="row">
                    <div class="col-sm-4">
                        <div id="dataTables-example_filter" class="dataTables_filter">
                            <label>    <?php echo form_label('Search:', 'search_string');
                echo ' ' . form_input('search_string', $search_string_selected, 'class="form-control input-sm" aria-controls="dataTables-example"');
                ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-4">  </div>

                    <div class="col-sm-4 text-right">
                        <div class="dataTables_length" id="dataTables-example_length">
                            <?php
                            echo form_label('Order by:', 'order');
                            echo ' ' . form_dropdown('order', $options_patients, $order, 'aria-controls="dataTables-example" class="form-control input-sm"');
                            $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-default', 'value' => 'Go');
                            $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
                            echo ' ' . form_dropdown('order_type', $options_order_type, $order_type_selected, 'aria-controls="dataTables-example" class="form-control input-sm"');

                            echo ' ' . form_submit($data_submit);

                            echo form_close();
                            ?>

                        </div>
                    </div>


                </div>


                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Full Name </th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($patients as $row) {
                                echo '<tr>';
                                echo '<td>' . $row['fullname'] . '</td>';
                                echo '<td>' . $row['email'] . '</td>';
                                echo '<td>' . $row['phone'] . '</td>';
                                echo '<td>
                                                    <div class="dropdown">
                                                       <button class="btn btn-outline btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> Action
                                                       <span class="caret"></span></button>
                                                       <ul class="dropdown-menu">
                                                         <li><a href="' . site_url("admin") . '/reports/add/?id=' . md5($row['id'] . $row['id']) . '">Add Report</a></li>
                                                         <li><a href="' . site_url("admin") . '/reports/list/?id=' . md5($row['id'] . $row['id']) . '">View Report</a></li>
                                                         <li><a href="javascript:confirmSMS(\'' . md5($row['id'] . $row['id']) . '\',\'' . $row['fullname'] . '\')">Send Passcode SMS</a></li>
                                                         <li><a href="' . site_url("admin") . '/patients/update/' . md5($row['id'] . $row['id']) . '">Edit</a></li>
                                                         <li><a href="javascript:confirmDelete(\'' . md5($row['id'] . $row['id']) . '\')">Delete</a></li>
                                                        
                                                       </ul>
                                                     </div> 
                                              </td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>      
                        </tbody>
                    </table>
                    <?php echo '' . $this->pagination->create_links() . ''; ?>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


<!-- Modal -->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="delete" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 id="myModalLabel" class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body" id="model_body">
                Are you sure, you want delete this item permanently?
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="doDelete();">Delete</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->         
</div>    

<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="sms" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 id="myModalLabel" class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body" id="sms_model_body">
                Are you sure, you want send passcode to <span id='sms_name' style="color:green;"></span>?
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="sendSMS();">Send SMS</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->         
</div>                                   


<script>
    id = '';

    function confirmSMS(localid, fullname) {
        $('#sms_name').html(fullname);
        $('#sms').modal('show');
        id = localid;
    }

    function sendSMS() {
        $('#sms_model_body').html('<br> <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemax="100" style="width:100%"> Sending... </div> <br> ');
        $.ajax({url: "<?php echo site_url("admin"); ?>/patients/sms/" + id, success: function (result) {
                $('#sms').modal('hide');
                $('#message').show();
                $('#sms_model_body').html("Are you sure, you want send passcode to <span id='sms_name'></span>?");
            }});
    }
    function confirmDelete(localid) {
        $('#delete').modal('show');
        id = localid;
    }

    function doDelete() {
        $('#delete').modal('hide');
        window.location = '<?php echo site_url("admin") . '/patients/delete/'; ?>' + id;
    }

    $(document).ready(function () {
        $('#message').hide();
    });
</script> 

