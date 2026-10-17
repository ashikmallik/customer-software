    <?php
    session_start();


    if (!empty($_SESSION['UserId'])) {
        /**
         * Created by mehedi
         * Date: 25/02/17
         * Reason: for calculating total bill for form date and to date in modal.
         */

        $user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
        $FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
        $UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
        $PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
        $ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;


        //========================================
        include '../../model/oop.php';
        $obj = new Controller();
        include '../../model/Bill.php';
        $bill = new Bill();
        //========================================


        date_default_timezone_set('Asia/Dhaka');
        $date_time = date('Y-m-d g:i:sA');
        $date = date('Y-m-d');
        $ip_add = $_SERVER['REMOTE_ADDR'];
        $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
        $notification = "";
        //taking month and years
        $day = date('M-Y');

        $formDateMysql = date('Y-m-d H:i:s', strtotime($_POST['formDate']));
        $toDateMysql = date('Y-m-d H:i:s', strtotime($_POST['todate']));

        $formDate = new DateTime($formDateMysql);
        $toDate = new DateTime($toDateMysql);
        $id = $_POST['id'];

        $diffOfDate = $formDate->diff($toDate);
        $diffInDays = $diffOfDate->d;
        $diffInMonth = $diffOfDate->m;

        $totalDayDiff = ($diffInMonth * 30) + $diffInDays;

        $agentDetails = $obj->details_by_cond("tbl_agent", "ag_id='$id'");
        extract($agentDetails);


        $countBillAmountChange = $obj->Total_Count('tbl_bill_amount_change', "agent_id = $id");
        if ($countBillAmountChange == 0) {

            $billForSelectDays = ceil(($agentDetails['taka'] * $diffInMonth) + ($agentDetails['taka'] / 30) * $diffInDays);

        } else {

            $billAmountChangeDataByDate = $obj->view_all_by_cond('tbl_bill_amount_change', "agent_id = $id AND 
        `created_at` BETWEEN '" . $formDateMysql . "' AND '" . $toDateMysql . "'  ORDER BY `tbl_bill_amount_change`.`created_at` DESC");

            $billAmountChangeDataFromDate = $obj->details_by_cond('tbl_bill_amount_change', "agent_id = $id AND 
        `created_at` > '" . $toDateMysql . "'  ORDER BY `tbl_bill_amount_change`.`created_at` ASC");

            $to = new DateTime($toDateMysql);
            $from = 0;
            $previousBill = 0;
            $bill = 1;

            if ($billAmountChangeDataByDate) {
                foreach ($billAmountChangeDataByDate as $value) {

                    $from = new DateTime($value['created_at']);
                    $previousBill = $value['previous_bill_amount'];

                    $diff = $to->diff($from);
                    $bill += (($value['bill_amount'] * $diff->m) + ($value['bill_amount'] / 30) * $diff->d);
                    $to = $from;
                }
            } else if ($billAmountChangeDataFromDate) {

                $previousBill = $billAmountChangeDataFromDate['previous_bill_amount'];
            } else {

                $previousBill = $agentDetails['taka'];
            }

            $from = new DateTime($formDateMysql);
            $diff = $to->diff($from);

            $bill += ($previousBill / 30) * $diff->d;
            $billForSelectDays = ceil($bill);

        }


        echo json_encode(array("billForDays" => $billForSelectDays, "days" => $totalDayDiff));

    }