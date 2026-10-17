<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

if(isset($_GET['day']) && !empty($_GET['day'])){

    $allExpenses = $obj->get_daywise_expense(date($_GET['day']));
    $previewDay = $_GET['day'];

}else{

    $allExpenses = $obj->get_daywise_expense(date('Y-m-d'));
    $previewDay = date('Y-m-d');
}
?>

<div class="row">

    <div class="col-md-12 bg-slate-800" style="margin-top:20px; margin-bottom: 15px;">
        <div class="col-md-10">
            <h4><strong>View <span id="showDate"><?php echo date('d M Y', strtotime($previewDay)); ?></span> Expense Report Sheet </strong>
            </h4>
        </div>
        <div class="col-md-2">
            <a class="btn btn-success btn-sm pull-right" href="?q=expense_report" style="margin-top:3px;">Monthwise Expense Report</a>
        </div>
    </div>

    <div id="select_date" class="col-md-12 bg-slate-800" style="margin-bottom: 20px;padding:5px 0px">
        <div class="col-md-2">
            <label class="control-label" style="padding-top:5px" for="dateMonth">Select Date : </label>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm pull-right" id="previous_date"><span
                        class="glyphicon glyphicon-chevron-left"></span> Previous Date
            </button>
        </div>
        <div class="col-md-4">
            <input class="form-control" required="required" value="<?php echo date('m-d-Y', strtotime($previewDay)); ?>" type="text" name="date"
                   placeholder="Select Date">
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary btn-sm pull-left" id="next_date">Next Date <span
                        class="glyphicon glyphicon-chevron-right"></span></button>
        </div>
    </div>
</div>

<div class="row text-center">
     <?php if($obj->hasPermission($ty, 'expense_report_print')){ ?>
    <div class="col-md-2 pull-right">
        <button class="btn btn-primary btn-sm" onclick="printDiv('month_table')">Click to Print</button>
    </div>
    <?php } ?>
</div>
<div id="month_table" onmouseover="marginchange()">
    <div class="row" style="font-size:14px;">
        <div class="col-md-12">
            <h4 id="print_header" class="text-center"></h4>
            <table class="table table-responsive table-bordered table-hover table-striped" id="ajax_table_load">
                <thead>
                <tr>
                    <th>SL No.</th>
                    <th>Account Head</th>
                    <th>Description</th>
                    <th>Expense</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total_expense = 0;
                $i = 1;
                foreach ($allExpenses as $expense) {

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
                    <td><b><?php echo number_format($total_expense, 2, '.', '') ?></b></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function formateDate(dateObject){
        var monthNames = [
            "Jan", "Feb", "March",
            "April", "May", "June", "July",
            "Aug", "Sept", "Oct",
            "Nov", "Dec"
        ];

        return (dateObject.getDate() + " " + monthNames[(dateObject.getMonth())] + " " + dateObject.getFullYear());

    };
    $(document).ready(function () {
        $('input[name="date"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'mm-dd-yyyy'
        });

        $('input[name="date"]').on('change', function () {
            var date = $(this).val().replace(/-/g,'/');// the replace(/-/g,'/') code is given to support firefox date() function
            if (date) {
                var preDateobject = new Date(date);
                getDataFromDate(preDateobject);
                showDateInHeader(preDateobject);
            }

        });

        $('button#previous_date').on('click', function () {

            var oldPresentDate = $('#select_date input[name="date"]').val().replace(/-/g,'/');// the replace(/-/g,'/') code is given to support firefox date() function
            var dateObject = new Date(oldPresentDate);

            dateObject.setDate(dateObject.getDate() - 1); // set (or replace) the dateObject on day plus (next day)

            var previousDate = ((dateObject.getMonth() + 1) + "-" + dateObject.getDate() + "-" + dateObject.getFullYear());
            // formate the previous date for ajax work

            $('#select_date input[name="date"]').val(previousDate);
            getDataFromDate(dateObject);
            showDateInHeader(dateObject);

        });

        $('button#next_date').on('click', function () {

            var oldPresentDate = $('#select_date input[name="date"]').val().replace(/-/g,'/');// the replace(/-/g,'/') code is given to support firefox date() function
            var dateObject = new Date(oldPresentDate);

            dateObject.setDate(dateObject.getDate() + 1); // set (or replace) the dateObject on day plus (next day)

            var nextDate = ((dateObject.getMonth() + 1) + "-" + dateObject.getDate() + "-" + dateObject.getFullYear());
            // formate the previous date for ajax work

            $('#select_date input[name="date"]').val(nextDate)
            getDataFromDate(dateObject);
            showDateInHeader(dateObject);

        });


        function showDateInHeader(dateObject) {

            $('#showDate').html(formateDate(dateObject));
        }


        function getDataFromDate(preDateobject) {

            var formateDate = (preDateobject.getFullYear() + '-' + (preDateobject.getMonth() + 1) + "-" + preDateobject.getDate());

            $.get('view/ajax_action/ajax_daily_expense_report.php', {date: formateDate}, function (data) {
                $('table#ajax_table_load tbody').html(data);
            })
        }


    });

    function printDiv(divName) {
        $("table").find('a').each(function () {
            $(this).attr('href', '');
        });
        dateObject = new Date($('input[name="date"]').val().replace(/-/g,'/'));

        $('h4#print_header').html('Print expense report for date: ' + formateDate(dateObject));
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        $('h4#print_header').html('');
    }
    function marginchange() {
        $(".print").css("margin-left", "-30px");
    }
</script>