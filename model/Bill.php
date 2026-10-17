<?php

if (!class_exists('Controller')) {
    include 'oop.php';
}

/**
 * Created by Mehedi.
 * Date: 2/20/17
 * Cause : To calculate total bill for a customer
 */
class Bill {

// get every customer dues query by id
//This method calculate all the dues pass by customer numeric id
//return a dues

    function get_customer_dues($id) {
        GLOBAL $obj;
        $details = $obj->details_by_cond("vw_agent", "company_id='$id'");
        extract($details);
        $diffMonth = $details['diff_month'];
        $billAmount = $details['taka'];

        $countBillAmountChange = $obj->Total_Count('vw_bill_amount_change', "company_id = $id");

        if ($countBillAmountChange != 0) {

            $billAmountChangeData = $obj->details_by_cond('vw_bill_amount_change', "company_id = $id ORDER BY `vw_bill_amount_change`.`bill_amount_id` DESC");

            $diffrenceFormBillChangeMonth = $billAmountChangeData['bill_change_diff_month'];

            if ($diffrenceFormBillChangeMonth != 0) {

                $total_due = ($billAmount *  ($diffrenceFormBillChangeMonth - 1)) + $billAmountChangeData['dueTillEdit'];

            } else {

                $total_due = $billAmountChangeData['dueTillEdit'];

            }
        } else {

            if ($diffMonth != 0) {

                $total_due = $billAmount * $diffMonth ;
            } else {
                $total_due = 0; //$billAmount;
            }
        }

        $total_due += $details['previous_due_amount'];
        $sql = "SELECT sum(acc_amount) as total_amount FROM `vw_account` WHERE company_id=$id AND acc_type != 2 AND acc_type != 4 AND acc_type != 5";
        $q = $obj->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        $total_pay = $data['total_amount'];
        $bonus = 0;
        foreach ($obj->view_all_by_cond("bonus", "customerID='$id' ") as $customer_bonus) {
            extract($customer_bonus);
            $bonus += $customer_bonus['amount'];
        }
        // Inactive month Bill Count
        $ag_details = $obj->details_by_cond("tbl_agent", "company_id='$id'");
        // $inactive_bill = $obj->details_by_cond("tbl_agent_activity", "agent_id='$id'"); 
        // $dueamount = $total_due - ($total_pay + $bonus + $inactive_bill['inactive_ammount']);
        // $dueamount = $total_due - ($total_pay + $bonus + isset($inactive_bill)? isset($inactive_bill['inactive_ammount'])?$inactive_bill['inactive_ammount']:0:0);


        // return $dueamount;
    }
    function bad_customer_dues($id) {
        GLOBAL $obj;
        $details = $obj->details_by_cond("vw_agent", "company_id='$id'");
        extract($details);
        $diffMonth = $details['in_diff_month'];
        $billAmount = $details['taka'];

        $countBillAmountChange = $obj->Total_Count('vw_bill_amount_change', "company_id = $id");

        if ($countBillAmountChange != 0) {

            $billAmountChangeData = $obj->details_by_cond('vw_bill_amount_change', "company_id = $id ORDER BY `vw_bill_amount_change`.`bill_amount_id` DESC");

            $diffrenceFormBillChangeMonth = $billAmountChangeData['bill_change_diff_month'];

            if ($diffrenceFormBillChangeMonth != 0) {

                $total_due = ($billAmount *  ($diffrenceFormBillChangeMonth - 1)) + $billAmountChangeData['dueTillEdit'];

            } else {

                $total_due = $billAmountChangeData['dueTillEdit'];

            }
        } else {

            if ($diffMonth != 0) {

                $total_due = $billAmount * $diffMonth ;
            } else {
                $total_due = 0; //$billAmount;
            }
        }

        $total_due += $details['previous_due_amount'];
        $sql = "SELECT sum(acc_amount) as total_amount FROM `vw_account` WHERE agent_id=$id AND acc_type != 2 AND acc_type != 4 AND acc_type != 5";
        $q = $obj->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        $total_pay = $data['total_amount'];
        $bonus = 0;
        foreach ($obj->view_all_by_cond("bonus", "customerID='$id' ") as $customer_bonus) {
            extract($customer_bonus);
            $bonus += $customer_bonus['amount'];
        }
        // Inactive month Bill Count
        $ag_details = $obj->details_by_cond("tbl_agent", "ag_id='$id'");
        $inactive_bill = $obj->details_by_cond("tbl_agent_activity", "agent_id='$id'"); 
        $dueamount = $total_due - ($total_pay + $bonus + $inactive_bill['inactive_ammount']);

        return $dueamount;
    }


    function get_customer_total_pay($id) {
        GLOBAL $obj;
        $total_pay = 0;
        foreach ($obj->view_all_by_cond("vw_account", "agent_id='$id' order by acc_id") as $customer_info) {
            extract($customer_info);
            $total_pay += $customer_info['acc_amount'];
        }
        return $total_pay;
    }


    function get_customer_only_total_dues($id, $runningAmount) {
        GLOBAL $obj;
        $details = $obj->details_by_cond("vw_agent", "ag_id='$id'");
        extract($details);
        $diffMonth = $details['diff_month'];
        $bill_amount = ($details['taka']);

        $countBillAmountChange = $obj->Total_Count('tbl_bill_amount_change', "agent_id = $id");

        if ($countBillAmountChange != 0) {

            $billAmountChangeData = $obj->details_by_cond('vw_bill_amount_change', "agent_id = $id ORDER BY `vw_bill_amount_change`.`bill_amount_id` DESC");

            $diffrenceFormBillChangeMonth = $billAmountChangeData['bill_change_diff_month'] - 1;

            if ($diffrenceFormBillChangeMonth != 0) {

                $total_pay_due = ($bill_amount *  ($diffrenceFormBillChangeMonth - 1)) + $billAmountChangeData['dueTillEdit'] + $runningAmount;;

            } else {
                $total_pay_due = $billAmountChangeData['dueTillEdit'];
            }
        } else {

            if ($diffMonth != 0) {

                $total_pay_due = ($bill_amount * ($diffMonth - 1)) + $runningAmount;
            } else {
                $total_pay_due = 0; //$bill_amount;
            }
        }

        $total_pay_due += $details['previous_due_amount'];

        return $total_pay_due;
    }




    function billForSelectedDays($formDateParameter, $toDateParameter, $id) {

        $obj = new Controller();
        $formDateMysql = date('Y-m-d H:i:s', strtotime($formDateParameter));
        $toDateMysql = date('Y-m-d H:i:s', strtotime($toDateParameter));

        $formDate = new DateTime($formDateMysql);
        $toDate = new DateTime($toDateMysql);


        $diffOfDate = $formDate->diff($toDate);
        $diffInDays = $diffOfDate->d;
        $diffInMonth = $diffOfDate->m;

        $agentDetails = $obj->details_by_cond("tbl_agent", "ag_id='$id'");
        extract($agentDetails);

        $countBillAmountChange = $obj->Total_Count('tbl_bill_amount_change', "agent_id = $id");
        if ($countBillAmountChange == 0) {

            $billForSelectDays = ceil(($agentDetails['taka'] * $diffInMonth) + ($agentDetails['taka'] / 30) * $diffInDays);
        } else {

            $billAmountChangeDataByDate = $obj->view_all_by_cond('tbl_bill_amount_change', "agent_id = $id AND `created_at` BETWEEN '" . $formDateMysql . "' AND '" . $toDateMysql . "'  ORDER BY `tbl_bill_amount_change`.`created_at` DESC");

            $billAmountChangeDataFromDate = $obj->details_by_cond('tbl_bill_amount_change', "agent_id = $id AND `created_at` > '" . $toDateMysql . "'  ORDER BY `tbl_bill_amount_change`.`created_at` ASC");

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
        return $billForSelectDays;
    }
}