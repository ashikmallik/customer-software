<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
if( $obj->year_duration_accounts_from_first_entry() > 1){

    $totalYear = $obj->year_duration_accounts_from_first_entry();
}else{

    $totalYear = '';
}
?>
<style>

    .pointer:hover{
        text-decoration:underline;
        -webkit-text-decoration-color: #83aff7; /* Safari */
        text-decoration-color: #83aff7;
        color: #1548ff;
    }
</style>

<div class="row bg-grey-800" style="margin-bottom: 15px;">
    <div class="col-md-8">
        <h4>Last <?php echo $totalYear ;?> Years Report</h4>
    </div>
    <div class="col-md-4">
        <?php if($obj->hasPermission($ty, 'print_statement')){ ?>
        <button class="btn btn-primary btn-sm pull-right" style="margin-top:3px;"
                onclick="printDiv('year_table')">Print Statement
        </button>
        <?php } ?>
        <a href="?q=yearly" class="btn btn-success btn-sm pull-right" style="margin-top:3px;" >Yearly Balance Report</a>

    </div>

</div>

<div class="row" id="year_table">
    <div class="col-md-12">
        <div id="printHeader"></div>
    </div>

    <div class="col-md-12">
        <table class="table table-responsive table-bordered table-hover table-striped" id="yearlyAccounts">
            <thead>
            <tr>
                <th>#</th>
                <th>Year</th>
                <th>Opening Balance</th>
                <th>Customer Payment</th>
                <th>Others Payment</th>
                <th>Connection Charge</th>
                <th>Opening Amount</th>
                <th>Total</th>
                <th>Expense Statement</th>
                <th>Closing Balance</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $dateYear = date('Y');
            $lastestYear = $dateYear - $obj->year_duration_accounts_from_first_entry();

            $i = 0;
            $invoice = 0;
            $year_total = 0;
            $year_expense = 0;
            $month_digit = date('n', strtotime(($date)));
            $year_digit = date('Y', strtotime(($date)));

            for ($j = $lastestYear; $j <= $dateYear; $j++) {

                $other_totals = 0;
                $agent_totals = 0;
                $connection_totals = 0;
                $expensive_totals = 0;
                $i++;

                //Expense calculation
                $expensive_totals_data = $obj->details_selected_field_by_cond('vw_account','SUM(acc_amount) as total_expense',"YEAR(entry_date)='$j' and acc_type='1'");
                $expensive_totals = $expensive_totals_data['total_expense'];

                //Other calculate
                $other_totals_data = $obj->details_selected_field_by_cond('vw_account','SUM(acc_amount) as total_other_income',"YEAR(entry_date)='$j' and acc_type='2'");
                $other_totals = $other_totals_data['total_other_income'];


                //Agent bill calculate
                $agent_totals_data = $obj->details_selected_field_by_cond('vw_account','SUM(acc_amount) as total_agent_payment',"YEAR(entry_date)='$j' and acc_type='3'");
                $agent_totals = $agent_totals_data['total_agent_payment'];


                //Connection charge
                $connection_charge_data = $obj->details_selected_field_by_cond('tbl_agent','SUM(connect_charge) as total_connection_charge',"YEAR(entry_date)='$j'");
                $connection_totals = $connection_charge_data['total_connection_charge'];

                //opening Amount
                $opening_amount_data = $obj->details_selected_field_by_cond('vw_account','SUM(acc_amount) as total_opening_amount',"YEAR(entry_date)='$j' and acc_type='5'");
                $opening_amount = $opening_amount_data['total_opening_amount'];

                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td id="year" style="text-align: center;">
                        <?php

                        echo '<span class="pointer" data-year="'.$j.'">'.$j.'</span>';

                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo $invoice ?>
                    </td>

                    <td style="text-align: right;">
                        <?php echo $agent_totals ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo $other_totals ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo $connection_totals ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo $opening_amount ?>
                    </td>
                    <td style="text-align: right;"><b>
                            <?php
                            $temp_total = $other_totals + $agent_totals + $connection_totals + $invoice;
                            $temp_total_without_invoice = $other_totals + $agent_totals + $connection_totals;

                            $year_total += $temp_total_without_invoice;
                            echo $temp_total;
                            ?>
                        </b>
                    </td>
                    <td style="text-align: right;"><b>
                        <?php
                        $year_expense += $expensive_totals;
                        echo $expensive_totals;
                        ?></b>
                    </td>
                    <td style="text-align: right;"><b>
                            <?php
                            $profit_month = ($other_totals + $agent_totals + $connection_totals + $invoice) - $expensive_totals;
                            echo $profit_month;
                            $invoice = $profit_month;
                            ?>
                        </b>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr><td colspan="10" style="background: #ddd;"></td></tr>
            <tr>
                <td colspan="8" style="text-align: right;">Total Income of Last <?php echo $totalYear ;?>  Years:</td>
                <td colspan="2" style="text-align: right;"><b><?php echo $year_total ?></b></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right;">Total Expense of Last <?php echo $totalYear ;?>  Years:</td>
                <td colspan="2" style="text-align: right;"><b><?php echo $year_expense ?></b></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right;">Profit of Last <?php echo $totalYear ;?>  Years:</td>
                <td colspan="2" style="text-align: right;"><b><?php echo $year_total - $year_expense; ?></b></td>
            </tr>

            <tr>
                <?php
                $totalw = $formater->convert_number_to_words($year_total - $year_expense);
                ?>
                <th colspan="10" style="text-align: center;"><span style="color: green;">Total Ammount In Word:&nbsp;<span style="color: red;"><?PHP echo ucwords($totalw); ?></span></span></th>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script>

    $(document).ready(function () {

        $('table#yearlyAccounts tbody tr td#year span').on('click', function () {
            month = $(this).data('month');
            window.location = '?q=monthly_new&month=' + month + '&year=<?php echo $dateYear ?>';
        });
    });
    function printDiv(divName) {
        $('#printHeader').html('<h2 class="text-center">The Yearly Balance report of <?php echo $dateYear ?></h2>');
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        $('#printHeader').html('');
    }
</script>