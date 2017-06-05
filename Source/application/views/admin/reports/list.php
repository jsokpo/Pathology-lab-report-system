<?php
$attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');

//save the columns names in a array that we will use as filter         
$options_reports = array();
$name = '';
foreach ($reports as $array) {
    foreach ($array as $key => $value) {
        $options_reports[$key] = $key;
        if ($key == 'fullname') {
            $name = $value;
        }
    }
    break;
}
?>                
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Reports  <?php if (isset($patient_id)) echo "only for <strong style='color:green'> $name </strong>"; ?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-success alert-dismissable" id="message">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            Email Sent !!! 
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                Report List


            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <?php
                echo form_open('admin/reports', $attributes);
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
                            echo ' ' . form_dropdown('order', $options_reports, $order, 'aria-controls="dataTables-example" class="form-control input-sm"');
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
                                <th>Patient Name</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>View</th>
                                <th>Download</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($reports as $row) {
                                echo '<tr>';
                                echo '<td>' . $row['fullname'] . '</td>';
                                echo '<td>' . $row['date'] . '</td>';
                                echo '<td>' . $row['type'] . '</td>';

                                if ($row['type'] == 'pdf') {
                                    $url = base_url() . 'uploads/' . $row['file'];
                                    $viewurl = $url;
                                } else {
                                    $url = site_url("admin") . '/reports/pdf/' . md5($row['id'] . $row['date']);
                                    $viewurl = site_url("admin") . '/reports/view_pdf/' . md5($row['id'] . $row['date']);
                                }

                                echo '<td> <a href="' . $viewurl . '" target="_blank"> view </a> </td>';
                                echo '<td> <a href="' . $url . '" download  target="_blank"> download </a> </td>';
                                echo '<td> <a href="javascript:confirmMail(\'' . $row['email'] . '\',\'' . md5($row['id'] . $row['date']) . '\');" > email </a> </td>';
                                echo '<td>
                                                    <div class="dropdown">
                                                       <button class="btn btn-outline btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> Action
                                                       <span class="caret"></span></button>
                                                       <ul class="dropdown-menu">
                                                         <li><a href="' . site_url("admin") . '/reports/update/' . md5($row['id'] . $row['date']) . '">Edit</a></li>
                                                         <li><a href="javascript:confirmDelete(\'' . md5($row['id'] . $row['date']) . '\')">Delete</a></li>
                                                        
                                                       </ul>
                                                     </div> 
                                              </td>';
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
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 id="myModalLabel" class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body" id="model_body">
                Are you sure, you want send email report to <span id="confirm_email"></span>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="sendMail();">Send Email</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>   
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

<script>
    id = '';
    function confirmMail(email, localid) {
        $('#confirm_email').html(email);
        id = localid;
        $('#myModal').modal('show');
    }

    function confirmDelete(localid) {
        $('#delete').modal('show');
        id = localid;
    }

    function doDelete() {
        $('#delete').modal('hide');
        window.location = '<?php echo site_url("admin") . '/reports/delete/'; ?>' + id;
    }

    function sendMail() {
        $('#model_body').html('<br> <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemax="100" style="width:100%"> Sending... </div> <br> ');
        $.ajax({url: "<?php echo site_url("admin"); ?>/reports/email/" + id, success: function (result) {
                $('#myModal').modal('hide');
                $('#message').show();
            }});
    }

    $(document).ready(function () {
        $('#message').hide();
    });


</script> 

