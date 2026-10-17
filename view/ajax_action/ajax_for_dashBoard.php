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

        $lastMonthIncome = array();
        $lastMonthExpense = array();
        $finalData = array();

        for ($i = 0; $i < 12; $i++) {

            $lastMonthIncome[] = $obj->view_selected_field_by_cond('tbl_account', 'SUM(`acc_amount`) AS `acc_amount`,
            `entry_date` as inc_date', "acc_type != 1 AND MONTH(entry_date) = MONTH(CURRENT_DATE - INTERVAL '$i' MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE - INTERVAL '$i' MONTH)
            AND `entry_date`
             IS NOT NULL ORDER BY `entry_date` DESC");

            $lastMonthExpense[] = $obj->view_selected_field_by_cond('tbl_account', 'SUM(`acc_amount`) AS `exp_amount`,
            `entry_date` as exp_date', "acc_type = 1 AND MONTH(entry_date) = MONTH(CURRENT_DATE - INTERVAL '$i' MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE - INTERVAL '$i' MONTH)
            AND 
            `entry_date`
             IS NOT NULL ORDER BY `entry_date` DESC");

            $finalData[$i] = $lastMonthIncome[$i][0] + $lastMonthExpense[$i][0];
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

        $allIncomeThisMonth = $obj->get_all_income($pre_dateform, $pre_dateto);
        $allExpenseThisMonth = $obj->get_sum_expense($pre_dateform, $pre_dateto);

        $finalData[13] = array("incomeThisMonth" => $allIncomeThisMonth['amount'],
            "expenseThisMonth" => $allExpenseThisMonth['amount']);
        /*
         *  Ending Income and Expense  of Last month
         */

        echo json_encode($finalData);
    }