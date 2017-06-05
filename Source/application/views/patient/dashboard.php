<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Pathology Lab Reporting System</title>

        <!-- Bootstrap Core CSS -->
        <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="<?php echo base_url(); ?>assets/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="<?php echo base_url(); ?>assets/dist/css/sb-admin-2.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <!-- Custom CSS -->
        <link href="<?php echo base_url(); ?>assets/css/custom.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="<?php echo base_url(); ?>assets//bower_components/jquery/dist/jquery.min.js"></script>


    </head>

    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo base_url(); ?>/admin">Pathology Lab Reporting System</a>
                </div>
                <!-- /.navbar-header -->

                <ul class="nav navbar-top-links navbar-right">

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">

                            <li class="divider"></li>
                            <li><a href="<?php echo base_url(); ?>patient/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <!-- /.dropdown -->
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">

                            <li>
                                <a class="fa fa-th-list" href="<?php echo base_url(); ?>patient/dashboard"> Reports</a>

                            </li>
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>

            <div id="page-wrapper">

                <?php
                $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');

                //save the columns names in a array that we will use as filter         
                $options_reports = array();
                foreach ($reports as $array) {
                    foreach ($array as $key => $value) {
                        $options_reports[$key] = $key;
                    }
                    break;
                }
                ?>                
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Reports  <?php if (isset($patient_id)) echo " for <strong style='color:green'> $patient_name </strong>"; ?></h1>
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
                                Patients List


                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <?php
                                echo form_open('patient/dashboard', $attributes);
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
                                                    $url = site_url("patient") . '/pdf/' . md5($row['id'] . $row['date']);
                                                    $viewurl = site_url("patient") . '/view_pdf/' . md5($row['id'] . $row['date']);
                                                }

                                                echo '<td> <a href="' . $viewurl . '" target="_blank"> view </a> </td>';
                                                echo '<td> <a href="' . $url . '" download  target="_blank"> download </a> </td>';
                                                echo '<td> <a href="javascript:confirmMail(\'' . $row['email'] . '\',\'' . md5($row['id'] . $row['date']) . '\');" > email </a> </td>';

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


                    function sendMail() {
                        $('#model_body').html('<br> <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemax="100" style="width:100%"> Sending... </div> <br> ');
                        $.ajax({url: "<?php echo site_url("patient"); ?>/email/" + id, success: function (result) {
                                $('#myModal').modal('hide');
                                $('#message').show();
                            }});
                    }

                    $(document).ready(function () {
                        $('#message').hide();
                    });



                </script> 



            </div>
        </div>
        <!-- /#wrapper -->

        <!-- jQuery -->


        <!-- Bootstrap Core JavaScript -->
        <script src="<?php echo base_url(); ?>assets//bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

        <!-- Metis Menu Plugin JavaScript -->
        <script src="<?php echo base_url(); ?>assets//bower_components/metisMenu/dist/metisMenu.min.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="<?php echo base_url(); ?>assets//dist/js/sb-admin-2.js"></script>

    </body>

</html>

