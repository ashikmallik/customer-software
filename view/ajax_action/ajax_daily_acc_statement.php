<?php
session_start();


if (!empty($_SESSION['UserId'])) {
//========================================
include '../../model/oop.php';
include '../../model/Bill.php';
include '../../model/FormateHelper.php';

$obj = new Controller();
$bill = new Bill();
$formater = new FormateHelper();
//========================================


$user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
$UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
$PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;


/* =============================================
 * =============================================
 *  Here request come from daily_expense_report
 * This is for daily basis expense report
 * =============================================
 * =============================================*/

    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $date = date('Y-m-d', strtotime($_GET['date']));
        $accountData = $obj->view_all_by_cond("vw_account", "entry_date='" . $date . "' ORDER BY `vw_account`.`entry_date` ASC");

    } else {
        die();
    }
    $i = 0;
    $totalin = 0;
    $balance = 0;
    $debit_total = 0;
    $credit_total = 0;
    foreach ($accountData as $acount) {
        extract($acount);
        $i++;
        $totalin += $acount['acc_amount'];

        $cus_id = isset($acount['agent_id']) ? $acount['agent_id'] : 0;
        if ($cus_id != 0) {
            $agentDetails = $obj->details_by_cond("tbl_agent", "ag_id=$cus_id");
            if ($agentDetails) {
                $customerName = $agentDetails['ag_name'];
                $customerIp = $agentDetails['ip'];
                $pointerClass = 'pointer';
                $linkStart = '<a href="?q=view_customer_payment_individual&token2=' . $cus_id . '">';
                $linkEnd = '</a>';
            } else {
                $customerName = '--';
                $customerIp = '--';
                $pointerClass = '';
                $linkStart = '';
                $linkEnd = '';
            }
        } else {
            $customerName = '--';
            $customerIp = '--';
            $pointerClass = '';
            $linkStart = '';
            $linkEnd = '';
        }

        ?>
        <tr id="<?php echo $cus_id; ?>" class="<?php echo $pointerClass; ?>">
            <td><?php echo $i; ?></td>
            <td><?php echo date("d-m-Y", strtotime(isset($acount['entry_date']) ? $acount['entry_date'] : "2016-02-1")); ?></td>
            <td class="text-right"><?php echo $linkStart; ?><?php echo isset($acount['acc_description']) ? $acount['acc_description'] : NULL; ?><?php echo $linkEnd; ?></td>
            <td class="text-center"><?php echo $linkStart; ?><?php echo $customerName . $linkEnd; ?></td>
            <td class="text-center"><?php echo $linkStart; ?><?php echo $customerIp . $linkEnd; ?></td>
            <td style="text-align: right;"><?php echo isset($acount['FullName']) ? $acount['FullName'] : NULL; ?></td>
            <?php
            $get_acc_type = isset($acount['acc_type']) ? $acount['acc_type'] : NULL;
            $amount = isset($acount['acc_amount']) ? $acount['acc_amount'] : NULL;
            $debit = 0;
            $credit = 0;

            if ($get_acc_type == 1) {
                $debit = $amount;
                $balance -= $debit;
                $debit_total += $debit;
            } else {
                $credit = $amount;
                $balance += $credit;
                $credit_total += $credit;
            }

            ?>
            <td style="text-align: right;"><?php echo $credit; ?></td>
            <td style="text-align: right;"><?php echo $debit; ?></td>
            <td style="text-align: right;"><?php echo $balance; ?></td>

            </tr>
        <?php
    }
    ?>

    <?php
    $word = $formater->convert_number_to_words($balance);
    ?>

    </tr>
    <tr style="font-size:14px;">
        <td colspan="5" class="text-center"><b>Total</b></td>
        <td class="text-center"><b><?php echo $credit_total; ?></b></td>
        <td class="text-center"><b><?php echo $debit_total; ?></b></td>
        <td class="text-center"><b><?php echo $credit_total - $debit_total; ?></b></td>
    </tr>

    <?php

} else {
    header("location: include/login.php");
}
?>