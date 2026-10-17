<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$template = $obj->details_by_cond("tbl_complain_templates",'id='.$_GET['token']);

if (isset ($_POST['submit'])) {

    $form_template_data = array(
            'template' => $_POST['details']
    );
    $obj->Update_data('tbl_complain_templates',$form_template_data,'id='.$_POST['template_id']);

    $obj->notificationStore('Complain Tempalte Edited.','success');
    echo '<script> window.location="?q=edit_complain_template&token='.$_POST['template_id'].'"; </script>';

}
?>

<div class="row">
    <div class="col-md-12 bg-teal-800">
        <h4>Add Customer Complane</h4>
    </div>
</div>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<?php $obj->notificationShowRedirect();?>

<div class="row">
    <form role="form" enctype="multipart/form-data" method="post">
        <div class="col-md-6 col-md-offset-3">

            <div class="form-group">
                <label>Complain Template Details</label>
                <textarea class="form-control" name="details" id="ResponsiveDetelis" rows="6" required><?= $template['template']?></textarea>
            </div>
            <input type="hidden" name="template_id" value="<?= $_GET['token']?>">

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" name="submit">Edit Complaine Template</button>
            </div>
        </div>

    </form>
</div>
<hr>



<script>
    $('select[name="customer_id"]').select2({
        placeholder: "Select",
        allowClear: true,
    });
</script>