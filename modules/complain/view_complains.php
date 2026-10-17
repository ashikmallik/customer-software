<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$complains = $obj->view_all("tbl_complains");
$complains_new      = $obj->view_new_customer_complain_by_cond('tbl_complains_new_user');
$pendingComplains   = $obj->view_all_by_status('1');
$reviewedComplains  = $obj->view_all_by_status('2');
$solvedComplains    = $obj->view_all_by_status('3');
$notSolvedComplains = $obj->view_all_by_status('4');

$pendingComplainsNew   = $obj->view_new_customer_by_status('1');
$reviewedComplainsNew  = $obj->view_new_customer_by_status('2');
$solvedComplainsNew    = $obj->view_new_customer_by_status('3');
$notSolvedComplainsNew = $obj->view_new_customer_by_status('4');


$pendingCount       = sizeof($pendingComplains)+ sizeof($pendingComplainsNew);
$reviewedCount      = sizeof($reviewedComplains)+ sizeof($reviewedComplainsNew);
$solvedCount        = sizeof($solvedComplains)+ sizeof($solvedComplainsNew);
$notSolvedCount     = sizeof($notSolvedComplains)+ sizeof($notSolvedComplainsNew);

if ($_REQUEST['datefrom'] && $_REQUEST['dateto']) {
    $d_f = $_REQUEST['datefrom'];
    $d_t = $_REQUEST['dateto'];

    $complains = $obj->view_all_by_cond("tbl_complains", "entry_date BETWEEN '$d_f' AND '$d_t'");
   
}elseif($_REQUEST['company_name']) {
    $c_id = $_REQUEST['company_name'];
    $complains = $obj->view_all_by_cond("tbl_complains", "company_id = '$c_id'");
}

if ($obj->userWorkPermission($acc, 'delete')) {
    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {
        if ($obj->Delete_data("tbl_complains", "id='$dltoken'")) {
            $obj->notificationStore('Deleted Successfully', 'success');
            echo '<script>window.location = "?q=view_complains";</script>';
        } else {
            $obj->notificationStore('Delete Failed', 'danger');
            echo '<script>window.location = "?q=view_complains";</script>';
        }
    }
}


if( isset($_POST['change_status'])){
    if($_POST['status'] == 3)
    {
        $obj->Update_data('tbl_complains', ['status'=>$_POST['status'],'solve_date'=>date('Y-m-d H:i:s', strtotime($date_time))],'id='.$_POST['complain_id']);
    }
    if($_POST['status'] != 0 && $_POST['status'] != 3)
    {
    $obj->Update_data('tbl_complains', ['status'=>$_POST['status'],'solve_date'=> 0],'id='.$_POST['complain_id']);
    }
    $obj->notificationStore('Status Changed','success');
    echo '<script> window.location="?q=view_complains"; </script>';
}

if( isset($_POST['change_status_new'])){
    $obj->Update_data('tbl_complains_new_user', ['status'=>$_POST['status']],'id='.$_POST['complain_new_id']);
    $obj->notificationStore('Status Changed','success');
    echo '<script> window.location="?q=view_complains"; </script>';
}

 


?>
<style>
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table thead {
        background: #343a40;
        color: white;
    }
    .btn-xs {
        padding: 2px 5px;
        font-size: 12px;
    }
    
    .impact-link-container {
    max-width: 150px;
    word-break: break-all;
}
.impact-link-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    max-height: 40px;
    line-height: 1.2;
}
.impact-link-text.expanded {
    -webkit-line-clamp: unset;
    max-height: none;
    overflow: visible;
}
.impact-link-more-btn {
    background: none;
    border: none;
    color: #007bff;
    cursor: pointer;
    font-size: 11px;
    padding: 0 5px;
    text-decoration: underline;
}

.complain-details-container {
    max-width: 180px;
    word-break: break-word;
    text-align: left;
}
</style>


<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>

</div>
<div class="row">
    <div class="col-md-12 bg-slate-800">
        <h4>View All Complains</h4>
    </div>

    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="client_list" class="btn-group btn-group-justified">
                    <form action="" method="post">

                        <button type="button" onclick="getComplains(this.value)" class="btn btn-primary" value="all">ALL (<?php echo sizeof($complains);?>)</button>

                        <button type="button" onclick="getComplains(this.value)" class="btn btn-success" value="3">Solved (<?php echo $solvedCount;?>)</button>

                        <button type="button" onclick="getComplains(this.value)" class="btn btn-warning" value = "1">Pending (<?php echo $pendingCount;?>)</button>

                        <button type="button" onclick="getComplains(this.value)" class="btn btn-info" value="2">Reviewed (<?php echo $reviewedCount;?>)</button>

                        <button type="button" onclick="getComplains(this.value)"  class="btn btn-danger" value="4">Not Solved (<?php echo $notSolvedCount;?>)</button>

                        <button style="background: #FF3327;" type="button" onclick="getComplains(this.value)" class="btn btn-info" value="im_user_all">Imminent Customers (<?php echo sizeof($complains_new);?>)</button>
                        <?php if($obj->hasPermission($ty, 'add_complain')) { ?>
                        <a href="index.php?q=add_complain"><button type="button" class="btn btn-primary" >Add  Complain</button></a>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
   
</div>
        <form name="frmcontent" id="date_search" action="" method="POST">
          <div class="col-md-3">
            <div class="form-group">
              <input 
                type="text" 
                class="form-control datepicker" 
                placeholder="From Date" 
                name="datefrom" 
                onchange="loading();"
              >
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              <input 
                type="text" 
                class="form-control datepicker" 
                placeholder="To Date" 
                name="dateto" 
                onchange="loading();"
              >
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <select class="form-control" name="company_name" onchange="loading();">
                <option value="">Select Company Name</option>
                <?php foreach ($obj->view_all('tbl_company') as $singleZone) { ?>
                    <option value="<?php echo $singleZone['company_id']; ?>">
                      <?php echo $singleZone['company_name']; ?>
                    </option>
                <?php } ?>
              </select>
            </div>
          </div>
        </form>



<hr>
<div class="row" style="font-size:12px">
    <div class="col-md-12">
        <div id="complain-list" class="table-responsive">
            <table class="table table-bordered table-hover" id="example">
                <thead>
                    <tr class="bg-slate-800">
                        <th class="col-md-1">SL</th>
                        <th class="col-md-1">Out Qty</th>
                        <th class="col-md-1">TT Number</th>
                        <th class="col-md-1">Company Name</th>
                        <th class="col-md-1">Link Name</th>
                        <th class="col-md-1">Sub Zone</th>
                        <th class="col-md-1"> Mobile No.</th>
                        <th class="col-md-1">Complain Details</th>
                        <th class="col-md-1">Complain Time</th>
                        <th class="col-md-1">Duration</th>
                        <!--<th class="col-md-1">Solve By</th>-->
                        <th class="col-md-1">Solve Date</th>
                        <th class="col-md-1">Status</th>
                         <th class="col-md-1">Impact link</th>
                        <th class="col-md-1">Billing Length</th>
                        <th class="col-md-1">Share Distance</th>
                        <th class="col-md-1">Total Distance(OTDR)</th>
                        <td class="col-md-1"> 
                            Image
                        </td>
                        <th class="col-md-1">Entry Date</th>
                         <?php if ($obj->userWorkPermission($acc, 'edit') || $obj->userWorkPermission($acc, 'delete')) {
                        echo '<th class="col-md-1" >Action</th>';
                    } ?>
                    </tr>
                </thead>
                <tbody>

                    <?php 
                    $image_upload_dir = 'modules/complain/images/';
                    $i = 0;
                    foreach ($complains as $complain) {
                                 $eid=$complain['solve_by'];
                                $employees = $obj->details_by_cond("tbl_employee","id='$eid'");
                                $customerID = $complain['customer_id'];
                                $agent = $obj->details_by_cond("vw_agent","ag_id='$customerID'");
                                
                                
                                $out_qty_sql = "SELECT SUM(item_qty) as total_out_qty 
                                        FROM tbl_stock 
                                        WHERE ag_id = " . (int)$customerID . " 
                                        AND status = 0";
                        $out_result = $obj->raw_sql($out_qty_sql);
                        $total_out_qty = isset($out_result[0]['total_out_qty']) ? (int)$out_result[0]['total_out_qty'] : 0;
                                
                                /*var_dump($complain['id']);
                                die;*/ 

                        ?>
                        <tr>
                            <td class="text-center"> <?php echo ++$i ?> </td>
                            <!-- নতুন Out Qty কলাম -->
                            <td class="text-center">
                                
                                <a href="?q=view_individual_cus&token=<?php echo $customerID; ?>">
                                <strong style="color: #d9534f;">view out item</strong>
                            </a>
                            </td>
                             <td class="text-center"><?php echo $complain['tt_name'] ?>  </td>
                            <td class="text-center"> <a href="?q=customer_complain_details&id=<?= $complain['customer_id'];?>"><?php echo $agent['company_name'] ?></a>  </td>
                            <td class="text-center"> <?php echo $agent['link_name'] ?> </td>
                            <td class="text-center"> <?php echo $agent['sub_zone_name'] ?> </td>
                            <td class="text-center"> <?php echo $agent['phone'] ?> </td>
                            <td class="text-center">
                                <?php
                                $details_full = $complain['details'];
                                ?>
                                <div class="complain-details-container">
                                    <span class="impact-link-text" id="complain-details-<?php echo $complain['id']; ?>">
                                        <?php echo htmlspecialchars($details_full); ?>
                                    </span>
                                    <?php if(strlen($details_full) > 50): ?>
                                    <button class="impact-link-more-btn" onclick="toggleImpactLink('complain-details-<?php echo $complain['id']; ?>', this)">
                                        More
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center"> <?php echo date('d M Y',strtotime($complain['complain_date'])) ?> <br> <?php echo date('h:i A',strtotime($complain['complain_date'])) ?></td>
                            <td class="text-center">
                                <?php
                                $complain_date = strtotime($complain['complain_date']);
                                $current_time = time();  // Get current timestamp
                                
                                // Calculate the time difference
                                $time_difference = $current_time - $complain_date;
                                
                                // Calculate hours, minutes, and seconds
                                $hours = floor($time_difference / 3600); // 1 hour = 3600 seconds
                                $minutes = floor(($time_difference % 3600) / 60); // 1 minute = 60 seconds
                                $seconds = $time_difference % 60; // Remaining seconds
                                
                                // Output the result
                                echo "$hours:$minutes:$seconds";
                                
                                ?>
                            </td>
                            
                            <!--<td class="text-center"> <?php echo $employees['employee_name']; ?></td>-->
                            <td class="text-center"> <?php if ($complain['solve_date']!='0000-00-00 00:00:00'){echo date('h:i A', strtotime($complain['solve_date'])).'<br>'.date('d, M Y', strtotime($complain['solve_date']));} ?> </td>
                            <td class="text-center"> <?php
                            if ($complain['status']==1){echo '<buttton class="btn-warning btn-xs">Pending</buttton>';}
                            elseif ($complain['status']==2){echo '<buttton class="btn-warning btn-xs">Reviewed</buttton>';}
                            elseif ($complain['status']==3){echo '<buttton class="btn-success btn-xs">Solved</buttton>';}
                            elseif ($complain['status']==4){echo '<buttton class="btn-danger btn-xs">Not Solved</buttton>';}
                            ?> </td>
                            <td class="text-center">
                                <?php 
                                $share_link = isset($agent['share_link']) ? $agent['share_link'] : '';
                                if(!empty($share_link)):
                                ?>
                                <div class="impact-link-container">
                                    <span class="impact-link-text" id="impact-link-<?php echo $complain['id']; ?>">
                                        <?php echo htmlspecialchars($share_link); ?>
                                    </span>
                                    <?php if(strlen($share_link) > 50): ?>
                                    <button class="impact-link-more-btn" onclick="toggleImpactLink('impact-link-<?php echo $complain['id']; ?>', this)">
                                        More
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <?php else: echo 'N/A'; endif; ?>
                            </td>
                            <td class="text-center"> <?php echo $agent['actual_distance']; ?></td>
                            <td class="text-center"> <?php echo $agent['share_distance']; ?></td>
                            <td class="text-center"> <?php echo $agent['total_distant']; ?></td>
                            <td class="text-center"> 
                            <img src="<?= $image_upload_dir.$complain['image'] ?>" 
                                 alt="Complain Image" 
                                 class="view-image-trigger" 
                                 style="max-width:100px; max-height:100px; border:1px solid #ddd; padding:4px; cursor: pointer;"> 
                        </td>
                            <td class="text-center"> <?php echo date('d, M Y', strtotime($complain['entry_date'])) ?> </td>
                            <?php if ($obj->userWorkPermission($acc, 'edit') || $obj->userWorkPermission($acc, 'delete')) { ?>
                            <td class="text-center action-btn">
                                <button class="edit btn btn-success btn-xs change-status" data-toggle="modal" data-target="#change-status"
                                data-id="<?php echo $complain['id'];?>">Change Status
                            </button>
                            
                            <a href="?q=edit_complain&id=<?php echo $complain['id'];?>" class="edit btn btn-success btn-xs change-status">Edit</a>
                            <?php if ($obj->userWorkPermission($acc, 'delete')) { ?>

                            <a href="?q=view_complains&dltoken=<?= $complain['id']; ?>"
                               onclick="return confirm('Are you sure?')"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </a>
                        <?php } ?>
                        </td>
                            <?php } ?>
                    </tr>
                <?php } 

                foreach ($complains_new as $complain_new) {?>
                    <tr>
                        <td class="text-center"> <?php echo ++$i ?> </td>
                        <td class="text-center"> <a style="color: #f30b0b;font-weight: bold;" href="?q=customer_complain_details&id=<?= $complain_new['customer_id'];?>"><?php echo $complain_new['customer_name'];?></a>  </td>
                        <td class="text-center"> <?php echo $complain_new['customer_address'] ?> </td>
                        <td class="text-center"> <?php echo $complain_new['customer_mobile'] ?> </td>
                        <td class="text-center">
                            <?php
                            $details_new_full = $complain_new['details'];
                            ?>
                            <div class="complain-details-container">
                                <span class="impact-link-text" id="complain-details-new-<?php echo $complain_new['id']; ?>">
                                    <?php echo htmlspecialchars($details_new_full); ?>
                                </span>
                                <?php if(strlen($details_new_full) > 50): ?>
                                <button class="impact-link-more-btn" onclick="toggleImpactLink('complain-details-new-<?php echo $complain_new['id']; ?>', this)">
                                    More
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center"> <?php echo date('h:i A',strtotime($complain_new['complain_date'])) ?> <br> <?php echo date('d, M Y',strtotime($complain_new['complain_date'])) ?>
                        </td>
                        <td class="text-center"> <?= !empty($employee['employee_name']) ? $employee['employee_name']:'N/A';?></td>
                        <td class="text-center"> <?php if ($complain_new['solve_date']!='0000-00-00 00:00:00'){echo date('h:i A', strtotime($complain_new['solve_date'])).'<br>'.date('d, M Y', strtotime($complain_new['solve_date']));} ?> </td>
                        <td class="text-center"> <?php
                        if ($complain_new['status']==1){echo '<buttton class="btn-warning btn-xs">Pending</buttton>';}
                        elseif ($complain_new['status']==2){echo '<buttton class="btn-warning btn-xs">Reviewed</buttton>';}
                        elseif ($complain_new['status']==3){echo '<buttton class="btn-success btn-xs">Solved</buttton>';}
                        elseif ($complain_new['status']==4){echo '<buttton class="btn-danger btn-xs">Not Solved</buttton>';}
                        ?> </td>
                        
                        <td class="text-center"> <?php echo date('d, M Y', strtotime($complain_new['entry_date'])) ?> </td>

                        <td class="text-center action-btn">
                            <button class="edit btn btn-success btn-xs change-status-new" data-toggle="modal" data-target="#change-status-new" data-id="<?php echo $complain_new['id'];?>">
                                Change Status
                            </button>
                            <a href="?q=edit_complain_imminent_user&id=<?php echo $complain_new['id']?>" class="edit btn btn-success btn-xs change-status">Edit</a>
                            
                            <?php if ($obj->userWorkPermission($acc, 'delete')) { ?>

                            <a href="?q=view_complains&id=<?= $complain_new['id']; ?>"
                               onclick="return confirm('Are you sure?')"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </a>
                        <?php } ?>
                        </td>
                    </tr>
                <?php  } ?>

            </tbody>
        </table>

    </div>

</div>
</div>

<hr>

<div class="modal fade" id="change-status" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Change Status</h4>
            </div>

            <form id="editDueForm" action="" method="post" class="form-horizontal">

                <div class="modal-body">

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-5 control-label">Change Status</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="0">Select</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Reviewed</option>
                                    <option value="3">Solved</option>
                                    <option value="4">Not Solved</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="complain_id" value="">

                </div>
                <div class="modal-footer text-center">
                    <button type="submit" name="change_status" class="btn btn-primary">Change Status</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Image Viewer Modal -->
<div class="modal fade" id="image-viewer-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Complain Image View</h4>
            </div>
            <div class="modal-body text-center">
                <img id="modal-preview-img" src="" alt="Preview" style="max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="change-status-new" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Change Status</h4>
            </div>

            <form id="editDueForm" action="" method="post" class="form-horizontal">

                <div class="modal-body">

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-5 control-label">Change Status</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="0">Select</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Reviewed</option>
                                    <option value="3">Solved</option>
                                    <option value="4">Not Solved</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="complain_new_id" value=""/>

                </div>
                <div class="modal-footer text-center">
                    <button type="submit" name="change_status_new" class="btn btn-primary">Change Status</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Get complains_data -->
<link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/cr-2.0.4/r-3.0.3/datatables.min.css" rel="stylesheet">
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/cr-2.0.4/r-3.0.3/datatables.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script>

<script>


function toggleImpactLink(elementId, btnElement) {
    var element = document.getElementById(elementId);
    if (element) {
        element.classList.toggle('expanded');
        if (element.classList.contains('expanded')) {
            btnElement.textContent = 'Less';
        } else {
            btnElement.textContent = 'More';
        }
    }
}
        function loading() {
          const dateFrom = document.getElementsByName('datefrom')[0].value;
          const dateTo = document.getElementsByName('dateto')[0].value;
          const companyName = document.getElementsByName('company_name')[0].value;
        
          // Check if both date fields are filled
          if (dateFrom && dateTo) {
            document.getElementById('date_search').submit();
          } 
          // If only company_name is selected, reload the form
          else if (companyName) {
            document.getElementById('date_search').submit();
          }
        }

    function getComplains(value){
        if (value != '') {
            $.ajax({
                url:"modules/complain/getcomplain.php",
                method:"POST",
                data:{complains_data:value},
                dataType:"text",
                success:function(data){
                    $('#complain-list').html(data);
                }
            });
        }
    }
</script>


<script>
    function numbersOnly(e)
    {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) {
            if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
                return false;
            }
        }
    }

    $(document).ready(function () {
        
        // ইমেজে ক্লিক করলে মডালে বড় হয়ে দেখাবে
        $(document).on('click', '.view-image-trigger', function() {
            var imgSrc = $(this).attr('src');
            $('#modal-preview-img').attr('src', imgSrc);
            $('#image-viewer-modal').modal('show');
        });

        $('.change-status').on('click',function () {
            var complain_id = $(this).data('id');
            console.log(complain_id);

            $('input[name="complain_id"]').val(complain_id);
        });
        
        $('#example').DataTable( {
        "lengthMenu": [[200, 250, 300, -1], [200, 250, 300, "All"]],
        dom:"Bflrtip",
        buttons: [ { 
            extend: 'excel', 
            className: 'btn-light', 
            text: 'Export to Excel', 
            exportOptions: { 
                columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15] 
              },
             
            
            }],
        
          });
    
    });
    
    
    $(document).on("click", ".change-status-new", function () {

        var complain_new_id = $(this).data('id');
            $('input[name="complain_new_id"]').val(complain_new_id);
    });
    
document.addEventListener('DOMContentLoaded', function() {
  flatpickr('.datepicker', {
    dateFormat: "Y-m-d",
  });
});

     
</script>