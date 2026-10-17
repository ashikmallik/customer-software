<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$templates = $obj->view_all("tbl_complain_templates");

if (isset ($_POST['submit'])) {

    $form_template_data = array(
            'template' => $_POST['details']
    );
    $obj->Insert_data('tbl_complain_templates',$form_template_data);

    $obj->notificationStore('Complain Tempalte Added.','success');
    echo '<script> window.location="?q=complain_template"; </script>';

}

if (isset($_GET['dltoken'])){
    $obj->Delete_data('tbl_complain_templates', 'id='.$_GET['dltoken']);
    $obj->notificationStore('Complain Tempalte Deleted.','success');
    echo '<script> window.location="?q=complain_template"; </script>';
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
                <textarea class="form-control" name="details" id="ResponsiveDetelis" rows="6" required></textarea>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" name="submit">Add Complaine Template</button>
            </div>
        </div>

    </form>
</div>
<hr>

<div class="col-md-8 col-md-offset-2">
    <div class="table-responsive">
        <table style="margin-left:-15px;" class="table table-bordered table-hover table-striped" id="datatable">
            <thead>
            <tr>
                <th class="text-center">SL</th>
                <th class="text-center">Details</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            <?php
            $i = '0';
            foreach ($templates as $template) {
                $i++;
                ?>
                <tr>
                    <td class="text-center"><?php echo $i; ?></td>
                    <td class="text-center"><?php echo $template['template']; ?></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                <a class="btn btn-xs btn-info" style="padding:5px;"
                                   href="?q=edit_complain_template&token=<?php echo isset($template['id']) ? $template['id'] : NULL ?>">
                                    <span class="glyphicon glyphicon-edit"></span>
                                </a>
                            <?php }
                            if( $obj->userWorkPermission($acc,'delete')){ ?>
                                <a href="?q=complain_template&dltoken=<?php echo isset($template['id']) ? $template['id'] : NULL; ?>"
                                   class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this zone?');" style="padding:5px;">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php } ?>
        </table>
    </div>
</div>

<script>
    $('select[name="customer_id"]').select2({
        placeholder: "Select",
        allowClear: true,
    });
</script>