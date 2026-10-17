    <?php
    session_start();


    if (!empty($_SESSION['UserId'])) {

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
        //======= Object Created from Class ======
        //========================================


        date_default_timezone_set('Asia/Dhaka');
        $date_time = date('Y-m-d g:i:sA');
        $date = date('Y-m-d');
        $ip_add = $_SERVER['REMOTE_ADDR'];
        $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
        $notification = "";
        //taking month and years
        $day = date('M-Y');

        /*
         * Starting last 12 month income
         */

        $lastmonthNewCus = array();
        $lastmonthOffCus = array();
        $finalData = array();

        for ($i = 0; $i < 12; $i++) {

            $lastmonthNewCus[] = $obj->view_selected_field_by_cond('tbl_agent', 'COUNT(`ag_id`) AS `acc_ag_id`,
            `entry_date` as inc_date', "ag_status = 1 AND MONTH(entry_date) = MONTH(CURRENT_DATE - INTERVAL '$i' MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE - INTERVAL '$i' MONTH)
            AND `entry_date`
             IS NOT NULL ORDER BY `entry_date` DESC");
            $lastmonthOffCus[] = $obj->view_selected_field_by_cond('tbl_agent', 'COUNT(`ag_id`) AS `off_ag_id`,
            `entry_date` as exp_date', "ag_status = 0 AND MONTH(entry_date) = MONTH(CURRENT_DATE - INTERVAL '$i' MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE - INTERVAL '$i' MONTH)
            AND 
            `entry_date`
             IS NOT NULL ORDER BY `entry_date` DESC");
/*            var_dump($lastmonthOffCus);
            die;*/

            $finalData[$i] = $lastmonthNewCus[$i][0] + $lastmonthOffCus[$i][0];
        }

//    echo "<pre>"; print_r($finalData);

        /*
         * Ending last 12 month income
         */

        /*
         * Total number of active and inactive user
         */
        $activeUser = $obj->Total_Count('tbl_agent', 'ag_status=1');
        $inactiveUser = $obj->Total_Count('tbl_agent', 'ag_status=0');

        $finalData[12] = array("active" => "$activeUser", "inactive" => "$inactiveUser");

        /*
         *  Ending of Total number of active and inactive user
         */

        /*
         *  Starting Income and Expense of Last month
         */
        $pre_dateform = date("n", strtotime("-0 months"));
        $pre_dateto = date("Y", strtotime("-0 months"));

        $allCusThisMonth = $obj->get_all_cus($pre_dateform, $pre_dateto);
        $allOffThisMonth = $obj->get_off_cus($pre_dateform, $pre_dateto);

        $finalData[13] = array("CusThisMonth" => $allCusThisMonth['ag_id'],
            "OffThisMonth" => $allOffThisMonth['ag_id']);
        /*
         *  Ending Income and Expense  of Last month
         */

        echo json_encode($finalData);
    }