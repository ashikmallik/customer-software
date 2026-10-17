<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$flag = isset($_GET['flag']) ? $_GET['flag'] : null;

$invoicePage = 'index.php';

if($obj->getSettingValue('invoice') == 3){

    $invoicePage = 'index.php';

}else if($obj->getSettingValue('invoice') == 1){

    $invoicePage = 'index_2_in_1.php';
}


?>
    <div class="col-md-12"
         style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
        <?php
        $total_row = $obj->Total_Count("tbl_agent", "ag_status='1'");
        $start = 0;
        $limit = 50;
        $end = 0;
        while ($total_row >= $end) {
            $end = $start + $limit;
            if ($end > $total_row) {
                ?>
                <a href="./pdf/<?php echo $invoicePage; ?>?&flag=<?php echo $flag; ?>&start=<?php echo $start; ?>" target="_blank"
                   class="btn btn-primary">Print <?php echo $start; ?> to <?php echo $end; ?></a>
                <?php
            } else {
                ?>
                <a href="./pdf/<?php echo $invoicePage; ?>?&flag=<?php echo $flag; ?>&start=<?php echo $start; ?>" target="_blank"
                   class="btn btn-primary">Print <?php echo $start; ?> to <?php echo $end; ?></a>
                <?php
            }
            echo "<br>";
            $start = $end;
        }
        ?>
    </div>