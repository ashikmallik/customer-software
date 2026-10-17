<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$customers   = $obj->view_all_by_cond("tbl_agent", "ag_status=1");
$templates   = $obj->view_all("tbl_complain_templates");
$employees   = $obj->view_all_by_cond('tbl_employee','employee_status=1');
$tbl_company = $obj->view_all("tbl_company");
$warehouses  = $obj->view_all("tbl_warehouse");

// ==================== Form Submit ====================
if (isset($_POST['submit'])) {

    $customer_id  = $_POST['customer_id'] ?? '';
    $employee_id  = $_POST['employee_id'] ?? '';
    $complain     = $_POST['details'] ?? '';
    $tt_name      = $_POST['tt_name'] ?? '';

    // Complain Insert
    $form_complain = array(
        'tt_name'       => $tt_name,
        'details'       => $complain,
        'company_id'    => $_POST['company_id'] ?? '',
        'customer_id'   => $customer_id,
        'status'        => 1,
        'solve_by'      => $employee_id,
        'entry_by'      => $userid,
        'update_by'     => $userid,
        'complain_date' => date('Y-m-d H:i:s'),
        'entry_date'    => date('Y-m-d h:i:s')
    );

    $complain_id = $obj->Insert_data('tbl_complains', $form_complain);

    $notification = 'Complain Added Successfully.';

    // ==================== Multiple Stock Out ====================
    if ($complain_id && isset($_POST['warehouse_id']) && is_array($_POST['warehouse_id'])) {

        foreach ($_POST['warehouse_id'] as $key => $warehouse_id) {
            $item_id   = $_POST['item_id'][$key] ?? '';
            $quantity  = (int)($_POST['quantity'][$key] ?? 0);

            if (!empty($warehouse_id) && !empty($item_id) && $quantity > 0) {

                $in  = $obj->get_total_stock_in($item_id, $warehouse_id);
                $out = $obj->get_total_stock_out($item_id, $warehouse_id);
                $available = $in - $out;

                if ($quantity <= $available) {
                    $stock_out_data = array(
                        'ag_id'        => $customer_id,
                        'item_id'      => $item_id,
                        'warehouse_id' => $warehouse_id,
                        'item_qty'     => $quantity,
                        'description'  => "Complain #$complain_id - $complain",
                        'added_by'     => $userid,
                        'entry_date'   => date("Y/m/d"),
                        'status'       => 0,
                    );

                    $obj->Insert_data("tbl_stock", $stock_out_data);
                }
            }
        }
        $notification .= " & Multiple Stock Issued.";
    }

    // SMS Part (আগের মতো রাখুন)
    $solve_employee_info = $obj->details_by_cond("tbl_employee", "id=" . $employee_id);
    $employee_mobile = $solve_employee_info['employee_mobile_no'] ?? '';

    $customer_info   = $obj->details_by_cond('tbl_agent', "ag_id=" . $customer_id);
    $customer_mobile = $customer_info['ag_mobile_no'] ?? '';
    $customer_ip     = $customer_info['link_name'] ?? $customer_info['ip'] ?? '';

    $sms_check = isset($_POST['sms_check']) ? true : false;

    if ($sms_check && $customer_mobile) {
        $smsbody_cus = "Welcome to ".$obj->getSettingValue('sms', 'company_name'). ". Link : $customer_ip, Complain : $complain has been noted. Assigned to $employee_mobile.";
        $obj->sendsms("88".$customer_mobile, $smsbody_cus);
    }

    if ($sms_check && $employee_mobile) {
        $smsbody_emp = $obj->getSettingValue('sms', 'company_name'). " - New Complain. Link : $customer_ip, Details: $complain. Customer: $customer_mobile";
        $obj->sendsms("88".$employee_mobile, $smsbody_emp);
    }

    $obj->notificationStore($notification, 'success');
    echo '<script> window.location="?q=add_complain"; </script>';
    exit;
}
?>


<div class="row">
    <div class="col-md-12  bg-teal-800">
        <h4>Add Customer Complane</h4>
    </div>

</div>


<div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
<b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<?php $obj->notificationShowRedirect();?>

<div class="row">
    
    <form role="form" enctype="multipart/form-data" method="post">
         <div class="col-md-3">
         </div>
        <div class="col-md-6">
                <h4 class="text-center">Add Complain</h4>
       

            <div class="form-group">

                <?php if (isset($_GET['token2'])) {
                    $ag_id          = $_GET['token2'];
                    $customerData   = $obj->view_all_by_cond("tbl_agent", "ag_id=$ag_id");
                    $customerInfo   = $customerData[0];

                ?>
                    <label>link Name</label>

                    <input type="text" name="customer_name" class="form-control" autocomplete="off" value="<?php echo $customerInfo['link_name']; ?>" readonly="readonly"/>

                    <input type="hidden" name="customer_id" class="form-control" autocomplete="off" value="<?php echo $customerInfo['ag_id']; ?>"/>
                    

                <?php }else{ ?>

                    <label>Select Company</label>
                    <select name="company_id" id="companySelect" class="form-control select2" required>
                        <option value="">Select</option>
                        <?php foreach ($tbl_company as $company) { ?>
                            <option value="<?= $company['company_id'] ?>"><?= $company['company_name']; ?></option>
                        <?php } ?>
                    </select>

                    <?php } ?>
                </div>
 
                <div class="form-group">
                    <label>Select Link</label>
                    <select name="customer_id" id="customerSelect" class="form-control select2" required>
                        <option value="">Select</option>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                        <label>Total Link (KMZ)</label><br>
                        <input type="text"
                               name="map_link"
                               id="linkName"
                               class="form-control"
                               readonly
                               style="cursor:pointer; color:blue; text-decoration:underline;">
                    </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                        <label>Actuail Link (KMZ)</label>
                            
                        <input type="text"
                               name="actual_link"
                               id="linkAddress"
                               class="form-control"
                               readonly
                               style="cursor:pointer; color:blue; text-decoration:underline;">
                    </div>
                    </div>
                  
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Zone</label>
                            <input type="text" name="zone" id="zone" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sub Zone</label>
                            <input type="text" name="sub_zone" id="subZone" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!--<div class="col-md-6">-->
                    <!--    <div class="form-group">-->
                    <!--        <label>Phone Number</label>-->
                    <!--        <input type="text" name="phone_number" id="phoneNumber" class="form-control" disabled>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Total Distance(OTDR)</label>
                            <input id="total_distance" type="text" name="total_distance" class="form-control"   disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Actual Distance</label>
                            <input id="actual_distance" type="text" name="actual_distance" class="form-control"   disabled>
                        </div>
                    </div>
                    <!--<div class="col-md-6">-->
                    <!--    <div class="form-group">-->
                    <!--        <label>Latitude</label>-->
                    <!--        <input type="text" name="latitude" id="latitude" class="form-control" disabled>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>



                <div class="form-group">
                    <label>Complain Template</label>
                    <select name="template" class="form-control select2">
                        <option value="">Select Template</option>
                        <?php foreach ($templates as $template) {?>
                            <option><?= $template['template'];?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>TT Number</label>
                    <input type="text" name="tt_name" class="form-control"/>
                </div>

                <div class="panel panel-info" style="margin-top:25px;">
                <div class="panel-heading">
                    <strong>Issue Multiple Stock / Material</strong>
                    <button type="button" class="btn btn-xs btn-success pull-right" id="addMoreStock">
                        <span class="glyphicon glyphicon-plus"></span> Add More Item
                    </button>
                </div>
                <div class="panel-body" id="stockItemsContainer">
                    <!-- Default First Row -->
                    <div class="stock-row row" style="margin-bottom:15px;">
                        <div class="col-md-4">
                            <label>Warehouse</label>
                            <select name="warehouse_id[]" class="form-control select2 warehouse-select">
                                <option value="">Select Warehouse</option>
                                <?php foreach ($warehouses as $wh) { ?>
                                    <option value="<?php echo $wh['warehouse_id']; ?>"><?php echo $wh['warehouse_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Stock Item</label>
                            <select name="item_id[]" class="form-control select2 item-select">
                                <option value="">Select Item</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Available</label>
                            <input type="text" class="form-control available-qty" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Issue Qty</label>
                            <input type="text" name="quantity[]" onkeypress="return numbersOnly(event)" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

                <div class="form-group">
                    <label>Complain Details</label>
                    <textarea class="form-control" name="details" id="complain-details" rows="6" required></textarea>
                </div>



                <div class="form-group">
                    <label>Employee For Solve</label>
                    <select name="employee_id" class="form-control select2">
                        <option value="">Select Employee</option>
                        <?php foreach ($employees as $employee) {?>
                            <option value="<?= $employee['id'];?>"><?= $employee['employee_name'];?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="sms_check" checked value="1"><b>SMS Notification</b>
                        </label>
                    </div>
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary" name="submit">Add Complaine</button>
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

        $('select[name="template"]').on('change',function () {
            var tempalte = $(this).val();
            $('#complain-details').html(tempalte);
        });

        /*$('input[name="complain_date"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });*/
        $('input[name="complain_time"]').timepicker();
    </script>



<script>
  $(document).ready(function() {
    // Event listener for when a company is selected
    $('#companySelect').on('change', function() {
        var companyId = $(this).val();  // Get selected company ID
        
        if (companyId) {
            // Make an AJAX request to fetch customers for the selected company
            $.ajax({
                url: 'view/ajax_action/fetch_customer_info.php',  // PHP file that will return the customer options
                type: 'GET',
                data: { company_id: companyId },
                success: function(response) {
                    // Populate the customer select dropdown
                    $('#customerSelect').html(response);
                },
                error: function() {
                    alert('Error fetching customer data');
                }
            });
        } else {
            // If no company is selected, reset the customer dropdown
            $('#customerSelect').html('<option value="">Select Link</option>');
        }
    });

    // Event listener for when a customer (link) is selected
    $('#customerSelect').on('change', function() {
        var customerId = $(this).val();  // Get selected customer ID
        if (customerId) {
            // Make an AJAX request to fetch the customer data
            $.ajax({
                url: 'view/ajax_action/fetch_customer_info.php',  // PHP file that returns customer data
                type: 'GET',
                data: { customer_id: customerId },
                success: function(response) {
                    console.log(response);
                    var customerData = JSON.parse(response);

                    // Check if customer data exists
                    if (customerData && customerData.length > 0) {
                        var customer = customerData[0]; // Assuming a single customer is returned

                        // Populate the disabled input fields with customer data
                        $('#linkName').val(customer.map_link);
                        $('#linkAddress').val(customer.actual_link);
                        $('#zone').val(customer.zone_name);
                        $('#subZone').val(customer.sub_zone_name);
                        $('#latitude').val(customer.customar_latitude);
                        $('#longitude').val(customer.customar_longitude);
                        $('#phoneNumber').val(customer.ag_mobile_no);
                        $('#actual_distance').val(customer.actual_distance);
                        $('#total_distance').val(customer.total_distant);
                        
                    } else {
                        // Reset fields if no customer data is found
                        $('#linkName').val('');
                        $('#linkAddress').val('');
                        $('#zone').val('');
                        $('#subZone').val('');
                        $('#latitude').val('');
                        $('#longitude').val('');
                        $('#phoneNumber').val('');
                    }
                },
                error: function() {
                    alert('Error fetching customer details');
                }
            });
        } else {
            // If no customer is selected, reset all input fields
            $('#linkName').val('');
            $('#linkAddress').val('');
            $('#zone').val('');
            $('#subZone').val('');
            $('#latitude').val('');
            $('#longitude').val('');
            $('#phoneNumber').val('');
        }
    });
    
    $('#linkName').on('click', function () {
    let url = $(this).val();

    if (url && url !== '') {
        window.open(url, '_blank');
    } else {
        alert('No link available');
    }
});

    $('#linkAddress').on('click', function () {
        let url = $(this).val();
    
        if (url && url !== '') {
            window.open(url, '_blank');
        } else {
            alert('No link available');
        }
    });



});



</script>
<script>
// Warehouse + Item Dependent Dropdown + Available Stock
$(document).ready(function() {
    $('#warehouse_id').select2();
    $('#item_id').select2();

    $('#warehouse_id').change(function() {
        var warehouse_id = $(this).val();
        
        if (warehouse_id) {
            $.ajax({
                url: 'view/ajax_action/fetch_stock_by_warehouse.php',
                type: 'POST',
                data: { warehouse_id: warehouse_id },
                success: function(response) {
                    $('#item_id').html(response);
                }
            });
        }
    });

    $('#item_id').change(function() {
        var item_id = $(this).val();
        var warehouse_id = $('#warehouse_id').val();

        if (item_id && warehouse_id) {
            $.ajax({
                url: 'view/ajax_action/ajax_get_available_stock.php',
                type: 'POST',
                data: { item_id: item_id, warehouse_id: warehouse_id },
                success: function(response) {
                    $('#available_qty').val(response);
                }
            });
        }
    });
});

function numbersOnly(e) {
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if (unicode != 8) {
        if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
            return false;
        }
    }
}
</script>
<script>
// Add More Row
$('#addMoreStock').click(function() {
    var newRow = `
        <div class="stock-row row" style="margin-bottom:15px; border-top:1px solid #ddd; padding-top:15px;">
            <div class="col-md-4">
                <label>Warehouse</label>
                <select name="warehouse_id[]" class="form-control select2 warehouse-select">
                    <option value="">Select Warehouse</option>
                    <?php foreach ($warehouses as $wh) { ?>
                        <option value="<?php echo $wh['warehouse_id']; ?>"><?php echo $wh['warehouse_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Stock Item</label>
                <select name="item_id[]" class="form-control select2 item-select">
                    <option value="">Select Item</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Available</label>
                <input type="text" class="form-control available-qty" readonly>
            </div>
            <div class="col-md-2">
                <label>Issue Qty</label>
                <input type="text" name="quantity[]" onkeypress="return numbersOnly(event)" class="form-control">
                <button type="button" class="btn btn-xs btn-danger remove-row" style="margin-top:5px;">Remove</button>
            </div>
        </div>`;
    
    $('#stockItemsContainer').append(newRow);
    $('.select2').select2();
});

// Remove Row
$(document).on('click', '.remove-row', function() {
    $(this).closest('.stock-row').remove();
});

// Warehouse Change → Load Items
$(document).on('change', '.warehouse-select', function() {
    var warehouse_id = $(this).val();
    var $row = $(this).closest('.stock-row');
    
    if (warehouse_id) {
        $.post('view/ajax_action/fetch_stock_by_warehouse.php', {warehouse_id: warehouse_id}, function(res) {
            $row.find('.item-select').html(res);
        });
    }
});

$(document).on('change', '.item-select', function() {
    var $row = $(this).closest('.stock-row');
    var item_id = $(this).val();
    var warehouse_id = $row.find('.warehouse-select').val();

    if (!item_id) return;

    var isDuplicate = false;
    $('.stock-row').not($row).each(function() {
        var otherItem = $(this).find('.item-select').val();
        var otherWarehouse = $(this).find('.warehouse-select').val();
        if (otherItem == item_id && otherWarehouse == warehouse_id) {
            isDuplicate = true;
        }
    });

    if (isDuplicate) {
        alert('এই item টি আগে থেকেই আরেকটি row-এ নেওয়া হয়েছে। একই item দুইবার সিলেক্ট করা যাবে না।');
        $(this).val('').trigger('change.select2');
        $row.find('.available-qty').val('').removeData('original');
        $row.find('input[name="quantity[]"]').val('');
        return;
    }

    if (item_id && warehouse_id) {
        $.post('view/ajax_action/ajax_get_available_stock.php', {
            item_id: item_id,
            warehouse_id: warehouse_id
        }, function(res) {
            $row.find('.available-qty').val(res).data('original', parseInt(res) || 0);
        });
    }
});

// NEW: Quantity type korle live available update
$(document).on('input', 'input[name="quantity[]"]', function() {
    var $row = $(this).closest('.stock-row');
    var original = parseInt($row.find('.available-qty').data('original')) || 0;
    var entered  = parseInt($(this).val()) || 0;

    // available quantity theke beshi issue korte parbe na
    if (entered > original) {
        entered = original;
        $(this).val(original);
    }

    var remaining = original - entered;
    $row.find('.available-qty').val(remaining);
});

function numbersOnly(e) {
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if (unicode != 8) {
        if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
            return false;
        }
    }
}
</script>