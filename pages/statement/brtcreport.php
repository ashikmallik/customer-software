<?php
$allAgentData = $obj->view_all_agent_for_excel('tbl_agent', 'ag_status = 1');
$distributionLocationPoint = $obj->getSettingValue('distributionLocationPoint', 'distributionLocationPoint');
$brtaDistrict = $obj->getSettingValue('brtaDistrict', 'brtaDistrict');
$brtaThana = $obj->getSettingValue('brtaThana', 'brtaThana');
if (isset($_POST['submit'])) {
    $location = $_POST['location'];
    $district = $_POST['district'];
    $thana = $_POST['thana'];
    if ($brtaDistrict) {
        $obj->updateSettingValue('brtaDistrict', $district, 'brtaDistrict');
    } else {
       $obj->insertData('tbl_setting', ['value' => $district, 'field' => 'brtaDistrict', 'other_parameter' => 'brtaDistrict', 'description' => 'brtaDistrict']);
    }
    if ($brtaThana) {
        $obj->updateSettingValue('brtaThana', $thana, 'brtaThana');
    } else {
        $obj->insertData('tbl_setting', ['value' => $thana, 'field' => 'brtaThana', 'other_parameter' => 'brtaThana', 'description' => 'brtaThana']);
    }
    if ($distributionLocationPoint) {
        $obj->updateSettingValue('distributionLocationPoint', $location, 'distributionLocationPoint');
    } else {
        $obj->insertData('tbl_setting', ['value' => $location, 'field' => 'distributionLocationPoint', 'other_parameter' => 'distributionLocationPoint', 'description' => 'Distribution Location Point']);
    }
    echo "<script> window.location=\"?page=brtcreport\"; </script>";
}
?>
<div class="card basic-data-table">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="text-md text-neutral-500">BTRC – Comprehensive Network and Client Data Report</h5>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#locationModal">distribution Location</button>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped bordered-table mb-0" id="report-table" data-page-length="10">
            <thead>
                <!-- Header Information -->
                <!-- <tr class="header-row">
                    <td colspan="4"><b>Category ISP Name:</b> BSD</td>
                    <td colspan="4"><b>Reporting Month:</b> <?php echo date("F"); ?></td>
                </tr>
                <tr class="header-row">
                    <td colspan="4"><b>Type of Category:</b> CAT-C</td>
                    <td colspan="4"><b>Year:</b> <?php echo date("Y"); ?></td>
                </tr>
                <tr class="header-row">
                    <td colspan="4"><b>Permitted Area/Thana:</b> Dhakshin Khan</td>
                    <td colspan="4"></td>
                </tr> -->

                <!-- Main Table Headers -->
                <tr>
                    <th scope="col">SL</th>
                    <th scope="col">Name of Operator</th>
                    <th scope="col">Type of Clients</th>
                    <th scope="col">Distribution Location</th>
                    <th scope="col">Name of Clients</th>
                    <th scope="col">Type of Connection</th>
                    <th scope="col">Type of Connectivity</th>
                    <th scope="col">Activation Date</th>
                    <th scope="col">Bandwidth Allocation (MB)</th>
                    <th scope="col">Allocated IP</th>
                    <th scope="col">House No</th>
                    <th scope="col">Road No</th>
                    <th scope="col">Area</th>
                    <th scope="col">District</th>
                    <th scope="col">Thana</th>
                    <th scope="col">Client Phone</th>
                    <th scope="col">Mail</th>
                    <th scope="col">Selling Bandwidth BDT (Excl. VAT)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($allAgentData as $agent) {
                    echo "<tr>";
                    echo "<td>" . $i++ . "</td>";
                    echo "<td>" . ($agent['Name_of_Operator'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Type_of_Clients'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($distributionLocationPoint ?? $agent['Distribution_Location_Point'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Name_of_Clients'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Type_of_Connection'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Type_of_Connectivity'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Activation_Date'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Bandwidth_Allocation_MB'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Allowcated_Ip'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['House_No'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Road_No'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Area'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($brtaDistrict ?? $agent['District'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($brtaThana ?? $agent['Thana'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Client_Phone'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Mail'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($agent['Selling_BandwidthBDT_Excluding_VAT'] ?? 'N/A') . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="locationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">Enter Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Distribution Location</label>
                        <input type="text" name="location" class="form-control" placeholder="Enter location" value="<?= $distributionLocationPoint ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">District</label>
                        <input type="text" name="district" class="form-control" placeholder="Enter district" value="<?= $brtaDistrict ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thana</label>
                        <input type="text" name="thana" class="form-control" placeholder="Enter thana" value="<?= $brtaThana ?>" required />
                    </div>
                    <button type="submit" name="submit" class="btn btn-success">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$categoryIspName = "BSD";
$reportingMonth = date("F");
$typeOfCategory = "CAT-C";
$year = date("Y");
$permittedArea = "Uttara";
?>

<?php $obj->start_script(); ?>



<script>
    $(document).ready(function() {
        let messageTopContent = `
        Category ISP Name: <?php echo $categoryIspName; ?>\n
        Type of Category: <?php echo $typeOfCategory; ?>\n
        Reporting Month: <?php echo $reportingMonth; ?>\n
        Year: <?php echo $year; ?>\n
        Permitted Area/Thana: <?php echo $permittedArea; ?>
    `;

        $('#report-table').DataTable({
            dom: '<"row"<"col-sm-6"l><"col-sm-6 text-end"f>>' +
                '<"row"<"col-sm-12 text-end"B>>' +
                '<"row dt-layout-row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-5"i><"col-sm-7 text-end"p>>',
            keys: true,
            stateSave: true,
            lengthChange: true,
            responsive: true,
            pagingType: "full_numbers",
            buttons: [{
                extend: "excel",
                className: "btn-light",
                title: "BTRC Report",
                messageTop: messageTopContent
            }]
        });
    });
</script>


<?php $obj->end_script(); ?>