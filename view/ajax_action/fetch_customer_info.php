
<?php
  //========================================
  include '../../model/oop.php';
  include '../../model/Bill.php';
  $obj = new Controller();
  $bill = new Bill();
//========================================

if (isset($_GET['company_id'])) {
    $companyId = $_GET['company_id'];
    // Query to fetch customers associated with the selected company
    // Make sure the customers table has a company_id field that relates to the company table
    $customers = $obj->view_all_by_cond("tbl_agent", "company_id='$companyId'");

    // Check if customers exist and generate the options
    if ($customers) {
        echo '<option value="">Select</option>';  // Default prompt option
        foreach ($customers as $customer) {
            echo '<option value="' . $customer['ag_id'] . '">' . $customer['link_name'] . '</option>';
        }
    } else {
        // If no customers are found, display a message
        echo '<option value="">No customers found</option>';
    }
}
if (isset($_GET['customer_id'])) {
    $customerId = $_GET['customer_id'];

    // Query to fetch customer data
    $customer = $obj->view_all_by_cond("vw_agent", "ag_id='$customerId'");
    

    // If customer data exists, return it as JSON
    if ($customer) {
        echo json_encode($customer);
    } else {
        echo json_encode([]);
    }
}
?>
