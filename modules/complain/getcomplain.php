<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../../model/oop.php');

$obj = new Controller();


if (isset( $_POST['complains_data'])) {
    $complains_data  = $_POST['complains_data'];
    if ($complains_data == "3") {
        $complains_new = array();
        $status = 3;
        $complains = $obj->view_all_by_status($status);
        $complains_new = $obj->view_new_customer_by_status($status);

    }elseif ($complains_data == "1") {
        $complains_new = array();
        $status = 1;
        $complains = $obj->view_all_by_status($status);
        $complains_new = $obj->view_new_customer_by_status($status);

    }elseif ($complains_data == "2") {
        $complains_new = array();
        $status = 2;
        $complains = $obj->view_all_by_status($status);
        $complains_new = $obj->view_new_customer_by_status($status);

    }
    elseif ($complains_data == "4") {
        $complains_new = array();
        $status = 4;
        $complains = $obj->view_all_by_status($status);
        $complains_new = $obj->view_new_customer_by_status($status);
    }elseif ($complains_data == "im_user_all") {
        $complains = array();
        $complains_new = $obj->view_new_customer_complain_by_cond('tbl_complains_new_user');
    }else{
        $complains = $obj->view_all('tbl_complains');
        $complains_new = $obj->view_new_customer_complain_by_cond('tbl_complains_new_user');
    }
}

?>
<table class="table table-responsive table-bordered table-hover " id="example">
    <thead>
        <tr class="bg-slate-800">
            <th class="col-md-1">SL</th>
                        <th class="col-md-1">TT Name</th>
                        <th class="col-md-1">Company Name</th>
                        <th class="col-md-1"> Sub Zone</th>
                        <th class="col-md-1"> Mobile No.</th>
                        <th class="col-md-2">Complain Details</th>
                        <th class="col-md-2">Complain Date</th>
                        <th class="col-md-1">Duration</th>
                        <th class="col-md-1">Solve By</th>
                        <th class="col-md-1">Solve Date</th>
                        <th class="col-md-2">Status</th>
                        
                        <th class="col-md-1">Action</th>
        </tr>
    </thead>
    <tbody>

        <?php $i = 0;
        if (!empty($complains)) {
            foreach ($complains as $complain) {
            
            $eid=$complain['solve_by'];
$employees = $obj->details_by_cond("tbl_employee","id='$eid'");
$cus_id=$complain['customer_id'];
$mac = $obj->details_by_cond("vw_agent","ag_id='$cus_id'");
            
            ?>

                <tr>
                    <td class="text-center"> <?php echo ++$i ?> </td>
                <td class="text-center"><?php echo $complain['tt_name'] ?>  </td>
                               <td class="text-center"> <a href="?q=customer_complain_details&id=<?= $complain['customer_id'];?>"><?php echo $mac['company_name'] ?></a>  </td>
                            <td class="text-center col-md-3"> <?php echo $mac['sub_zone_name'] ?> </td>
                            <td class="text-center"> <?php echo $mac['phone'] ?> </td>
                            <td class="text-center"> <?php echo $complain['details'] ?> </td>
                    <td class="text-center"> <?php echo date('h:i A',strtotime($complain['complain_date'])) ?> <br> <?php echo date('d, M Y',strtotime($complain['complain_date'])) ?></td>
                    <td class="text-center">
                            <?php
                            // Get the complain_date for calculating the duration
                            $complain_date = strtotime($complain['complain_date']);
                            $current_time = time();  // Get current timestamp
                            $time_difference = $current_time - $complain_date;  // Calculate difference in seconds
                        
                            // Calculate duration
                            if ($time_difference < 60) {
                                // Less than 1 minute
                                echo $time_difference . " second" . ($time_difference > 1 ? "s" : "") . " ago";
                            } elseif ($time_difference < 3600) {
                                // Less than 1 hour
                                $minutes = floor($time_difference / 60);
                                echo $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
                            } elseif ($time_difference < 86400) {
                                // Less than 1 day
                                $hours = floor($time_difference / 3600);
                                echo $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
                            } elseif ($time_difference < 2592000) { // Less than 1 month
                                $days = floor($time_difference / 86400);
                                echo $days . " day" . ($days > 1 ? "s" : "") . " ago";
                            } elseif ($time_difference < 31536000) { // Less than 1 year
                                $months = floor($time_difference / 2592000); // 30 days in a month approx
                                echo $months . " month" . ($months > 1 ? "s" : "") . " ago";
                            } else {
                                // More than 1 year
                                $years = floor($time_difference / 31536000); // 365 days in a year approx
                                echo $years . " year" . ($years > 1 ? "s" : "") . " ago";
                            }
                            ?>
                        </td>
                    <td class="text-center"> <?= !empty($employees['employee_name']) ? $employees['employee_name']:'N/A';?></td>
                    <td class="text-center"> <?php if ($complain['solve_date']!='0000-00-00 00:00:00'){echo date('h:i A', strtotime($complain['solve_date'])).'<br>'.date('d, M Y', strtotime($complain['solve_date']));} ?> </td>
                    <td class="text-center"> <?php
                    if ($complain['status']==1){echo '<buttton class="btn-warning btn-xs">Pending</buttton>';}
                    elseif ($complain['status']==2){echo '<buttton class="btn-warning btn-xs">Reviewed</buttton>';}
                    elseif ($complain['status']==3){echo '<buttton class="btn-success btn-xs">Solved</buttton>';}
                    elseif ($complain['status']==4){echo '<buttton class="btn-danger btn-xs">Not Solved</buttton>';}
                    ?> </td>
                  

                    <td class="text-center action-btn">
                        <button class="edit btn btn-success btn-xs change-status" data-toggle="modal" data-target="#change-status"
                        data-id="<?php echo $complain['id'];?>">Change Status
                    </button>
                    <a href="?q=edit_complain&id=<?php echo $complain['id']?>" class="edit btn btn-success btn-xs change-status">Edit</a>
                </td>
            </tr>

        <?php } }

        if (!empty($complains_new)) {
            foreach ($complains_new as $complain_new) {?>
                <tr>
                    <td class="text-center"> <?php echo ++$i ?> </td>
                    <td class="text-center"> <a style="color: #f30b0b;font-weight: bold;" href="?q=customer_complain_details&id=<?= $complain_new['customer_id'];?>"><?php echo $complain_new['customer_name'];?></a>  </td>
                    <td class="text-center"> 0<!-- <?php echo $complain_new['ip'] ?> --> </td>
                    <td class="text-center"> 0 </td>
                    <td class="text-center"> <?php echo $complain_new['customer_address'] ?> </td>
                    <td class="text-center"> <?php echo $complain_new['customer_mobile'] ?> </td>
                    <td class="text-center"> <?php echo $complain_new['details'] ?> </td>
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
               

                <td class="text-center action-btn">
                    <button class="edit btn btn-success btn-xs change-status-new" data-toggle="modal" data-target="#change-status-new" data-id="<?php echo $complain_new['id'];?>">
                        Change Status
                    </button>
                    <a href="?q=edit_complain_imminent_user&id=<?php echo $complain_new['id']?>" class="edit btn btn-success btn-xs change-status">Edit</a>
                </td>
            </tr>
        <?php } }?>
    </tbody>
</table>


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

        $('.change-status').on('click',function () {
            var complain_id = $(this).data('id');
            $('input[name="complain_id"]').val(complain_id);
        })

    });

    $(document).on("click", ".change-status-new", function () {

        var complain_new_id = $(this).data('id');
        $('input[name="complain_new_id"]').val(complain_new_id);
    });

</script>

<script>

    $(document).ready( function () {
        $('#example').DataTable( {
        "lengthMenu": [[200, 250, 300, -1], [200, 250, 300, "All"]]
    } );
    } );


</script>
