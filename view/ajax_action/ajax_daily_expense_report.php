<?php
session_start();
if (!empty($_SESSION['UserId'])) {
//========================================
include '../../model/oop.php';
include '../../model/Bill.php';

$obj = new Controller();
$bill = new Bill();
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



if(isset($_GET['date']) && !empty($_GET['date'])){
    $date = date('Y-m-d', strtotime($_GET['date']));

}else{
    die();
}
$total_expense = 0;
$i = 1;
foreach ($obj->get_daywise_expense($date) as $expense) {

    $total_expense += $expense['payments'];
    ?>
    <tr>
        <td><?php echo $i++ ?></td>
        <td>
            <?php echo $expense['name'] ?>
        </td>

        <td>
            <?php echo $expense['acc_description'] ?>
        </td>
        <td><?php echo number_format($expense['payments'], 2, '.', ''); ?></td>
    </tr>
    <?php
}
?>
<tr>
    <td colspan="3" class="text-center"><b>Total Expense</b></td>
    <td>
        <b><?php echo number_format($total_expense, 2, '.', '') ?></b>
    </td>
</tr>

<?php

} else {
    header("location: include/login.php");
}
?>