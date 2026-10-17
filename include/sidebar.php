<div class="col-md-2" style="padding:0px 10px 0px 0px;">

    <div class="col-md-12" style="padding:0px; width:100%;">

        <div id="body_service">
            <div id="body_service_list">

                <div id="body_service_list_text">
                    <ul class="box" style="margin:0px ! important;">

                        <li class="mactive">
                            <a href="?q=main_configuration" class="left_menu_bull"
                               style=" padding: 1px 0px 1px 30px; font-size:14px;  color:#FFFFFF; font-family:Arial, Helvetica, sans-serif; font-weight:600;">Dashboard</a>
                        </li>

                        <li class="menu">
                            <a href="?q=main_configuration" class="left_menu_bull mainli" style="color:white;">Configuration</a>
                            <ul>
                                <?php
                                    $obj->printSidebarElement( $ty, 'usercreate', 'Add New User');
                                    $obj->printSidebarElement( $ty, 'view_user', 'User Info');
                                ?>
                            </ul>
                        </li>

                        <li class="mactive">
                            <a href="?q=view_agent" class="left_menu_bull"
                               style=" padding: 1px 0px 1px 30px; font-size:14px;  color:#FFFFFF; font-family:Arial, Helvetica, sans-serif; font-weight:600;">Customer Info</a>
                            <ul>
                                <?php
                                    $obj->printSidebarElement( $ty, 'add_agent', 'Add link');
                                    $obj->printSidebarElement( $ty, 'view_agent', 'Link Information');
                                    $obj->printSidebarElement( $ty, 'view_all_zone', 'Add zone');
                                    $obj->printSidebarElement( $ty, 'view_all_sub_zone', 'Add sub zone');
                        
                                    $obj->printSidebarElement( $ty, 'view_all_company', 'Add company');
                                    // $obj->printSidebarElement( $ty, 'add_static_agent', 'Add Static Customer');
                                    
                                    $obj->printSidebarElement( $ty, 'view_new_join_customer', 'New Link');
                                    
                                echo '<li class="list">&nbsp;<a class="texta" href="?q=view_company_wise_link">Company Wise Link</a></li>';
                                // echo '<li class="list">&nbsp;<a class="texta" href="?q=view_all_package">View Package Info</a></li>';
                                // $obj->printSidebarElement( $ty, 'view_all_dues', 'Due Link');
                                // $obj->printSidebarElement( $ty, 'excel_invoice_company_wise', 'Excel Invoice Company Wise');
                                ?>
                            </ul>
                        </li>
                        <li class="menu">
                            <a href="?q=view_due_payment" class="left_menu_bull mainli"
                               style="color:white;">Income</a>
                            <ul>
                                <?php
                                    
                                    // $obj->printSidebarElement( $ty, 'view_unpaid_bill', 'Running('.date('M').') Bill Collection');
                                    // $obj->printSidebarElement( $ty, 'view_bill_collection', 'Bill Collection By User');
                                    $obj->printSidebarElement( $ty, 'bill_collection_company_wise', 'Bill Collection');
                                    // $obj->printSidebarElement( $ty, 'view_due_payment', 'Due Customer (Bill Collection)');
                                    // $obj->printSidebarElement( $ty, 'view_all_paid_client', 'Paid Customer (No Due)');
                                    // $obj->printSidebarElement( $ty, 'view_all_dues', 'Previous Due Customer');
                                    // $obj->printSidebarElement( $ty, 'view_running_paid_customer', 'Running('.date('M').') Paid Customer');
                                    // $obj->printSidebarElement( $ty, 'view_conn_charge', 'Connection Charge');
                                    $obj->printSidebarElement( $ty, 'view_income', 'Others Income');
                                    $obj->printSidebarElement($ty, 'view_all_bills', 'Project bills List');
                                    $obj->printSidebarElement($ty, 'bandthwith_bill', 'Bandthwith Bill');
                                    //$obj->printSidebarElement( $ty, 'view_bad_due_payment', 'Inactive Collection');
                                    // $obj->printSidebarElement( $ty, 'view_double_payment', 'Monthly Double Collection');
                                ?>
                            </ul>
                        </li>


                        <li class="menu">
                            <a href="?q=view_expense" class="left_menu_bull mainli"
                               style="color:white;">Expense</a>
                            <ul>
                                <?php
                                    $obj->printSidebarElement( $ty, 'view_account_head', 'Account Head');
                                    $obj->printSidebarElement( $ty, 'view_account_sub_head', 'Account Sub Head');
                                    // $obj->printSidebarElement( $ty, 'view_bonus', 'Discount');
                                    $obj->printSidebarElement( $ty, 'view_expense', 'Expense');
                                    $obj->printSidebarElement( $ty, 'panding_expence', 'Panding expence');
                                    // $obj->printSidebarElement( $ty, 'expense_report', 'Expense Report');
                                ?>
                            </ul>
                        </li>
                       
                        <li class="menu">
                            <a href="?q=monthly_new" class="left_menu_bull mainli" style="color:white;">Balance Sheet</a>
                            <ul>
                                <?php
                                    $obj->printSidebarElement( $ty, 'monthly_new', 'Monthly Balance Report'); 
                                    $obj->printSidebarElement( $ty, 'yearly', 'Yearly Balance Report');
                                    // $obj->printSidebarElement( $ty, 'salary_shit', 'Salary Sheet');
                                    
                                    // if($obj->hasPermission($ty, 'export_excel')){
                                    // echo '<li class="list">&nbsp;<a class="texta" href="excel/create_xlxs.php">Export in Excell File</a></li>';
                                    //  }
                                    // $obj->printSidebarElement( $ty, 'acc_statement', 'Account Statement');
                                    $obj->printSidebarElement( $ty, 'expense_report', 'Expense Report');
                                ?>
                            </ul>
                        </li>
                         <li class="menu">
                            <a href="?q=main_configuration" class="left_menu_bull mainli" style="color:white;">Employee</a>
                            <ul>
                                <?php
                                $obj->printSidebarElement($ty, 'add_employee', 'Add Employee');
                                $obj->printSidebarElement($ty, 'view_all_employee', 'Employee List');
                                $obj->printSidebarElement($ty, 'view_all_employee&status=1', 'Running Employee List');
                                 $obj->printSidebarElement($ty, 'view_all_employee&status=0', 'Resign Employee List');
                                $obj->printSidebarElement($ty, 'add_employee_salary', 'Edit Deduct / Bonus / Provident Fund');
                                $obj->printSidebarElement($ty, 'employee_transaction_monthly', 'Employee transaction');
                                
                                // $obj->printSidebarElement($ty, 'employee_transaction', 'Employee Transaction');
                                ?>
                            </ul>
                        </li>
                        
                        
                        <li class="menu">
                            <a href="?q=main_configuration" class="left_menu_bull mainli" style="color:white;">Employee Attendance System</a>
                            <ul>
                                <?php
                                $obj->printSidebarElement($ty, 'add_employee_attendance', 'Add Employee Attendance');
                                $obj->printSidebarElement($ty, 'view_employee_attendance', 'Attendance Report All Day');
                                $obj->printSidebarElement($ty, 'attendance_report', 'Attendance Report');
                                
                                // $obj->printSidebarElement($ty, 'employee_transaction', 'Employee Transaction');
                                ?>
                            </ul>
                        </li>
                        
                        
                        
                        <!-- Stock -->
                        <?php 
                        $stock = $obj->details_by_cond('stock_marketing_enable', "stock=1");
                        if ($stock) { ?>
                        <li class="menu">
                            <a href="?q=view_expense" class="left_menu_bull mainli"
                               style="color:white;">Stock</a>
                            <ul>
                                <?php
                                    /*$obj->printSidebarElement( $ty, 'view_stock_in', 'Available Stock');
                                    $obj->printSidebarElement( $ty, 'all_stock_out', 'All Stock Out');
                                    $obj->printSidebarElement( $ty, 'view_stock_category', 'Stock Category');
                                    $obj->printSidebarElement( $ty, 'view_stock_unit', 'Stock Unit');
                                    $obj->printSidebarElement( $ty, 'view_stock_report', 'Stock Report');*/
                                    $obj->printSidebarElement( $ty, 'view_all_warehouse', 'All Warehouse');
                                    $obj->printSidebarElement( $ty, 'view_stock_category', 'Stock Category');
                                    $obj->printSidebarElement( $ty, 'view_all_items', 'All Item');
                                    $obj->printSidebarElement( $ty, 'view_stock_in', 'Stock Item');
                                    $obj->printSidebarElement( $ty, 'all_stock_in', 'All Stock in');
                                    $obj->printSidebarElement( $ty, 'all_stock_out', 'All Stock Out');
                                    $obj->printSidebarElement( $ty, 'link_wise_stock_out', 'Link Wise Stock Out');
                                    $obj->printSidebarElement( $ty, 'stock_by_unit', 'Unit Wise Available Stock');
                                    
                                ?>
                            </ul>
                        </li>
                        <?php } ?>
                        <!-- Stock End -->
                        
                        
                        
                        <li class="menu">
                            <a href="?q=main_configuration" class="left_menu_bull mainli" style="color:white;">Complain</a>
                            <ul>
                                <?php
                                $obj->printSidebarElement( $ty, 'add_complain', 'Add Complain');
                               // $obj->printSidebarElement( $ty, 'add_complain_imminent_user', 'Add Complain For Imminent User');
                                $obj->printSidebarElement( $ty, 'view_complains', 'View All Complain');
                                $obj->printSidebarElement( $ty, 'complain_template', 'Complain Template');
                                ?>
                            </ul>
                        </li>
                        <!-- <li class="menu">
                            <a href="?q=due_sms" class="left_menu_bull mainli" style="color:white;">SMS
                                SERVICE</a>
                            <ul>
                                <?php
                                $obj->printSidebarElement( $ty, 'add_customize_sms', 'Add Custome SMS');
                                $obj->printSidebarElement( $ty, 'due_sms', 'Due SMS');
                                $obj->printSidebarElement( $ty, 'occation', 'Occational SMS');
                                $obj->printSidebarElement( $ty, 'inactive_customer_sms', 'Inactive Customer SMS');
                                $marketing_sms = $obj->details_by_cond('stock_marketing_enable', "marketing=1");
                                if($marketing_sms){
                                $obj->printSidebarElement( $ty, 'marketing_sms', 'Marketing SMS');
                                }
                                ?>


                            </ul>
                        </li> -->
                        <!-- Marketing -->
                        <?php 
                        $marketing = $obj->details_by_cond('stock_marketing_enable', "marketing=1");
                        if ($marketing) { ?> <?php } ?>
                        <li class="menu">
                            <a href="?q=main_configuration" class="left_menu_bull mainli" style="color:white;">Survey</a>
                            <ul>
                                <?php
                                $obj->printSidebarElement($ty, 'add_client', 'Add Survey');
                                $obj->printSidebarElement($ty, 'view_all_client', 'View all Survey');
                                $obj->printSidebarElement($ty, 'view_all_rectification_list', 'Rectification List');
                                
                                ?>
                            </ul>
                        </li>
                        
                        <!-- End marketing -->
                        <li class="menu">
                            <?php echo ($obj->hasPermission($ty,'software_setting')) ? '<a href="?q=software_setting" class="left_menu_bull mainli" style="color:white;"> Setting </a>' : '' ?>
                        </li>
                        <?php if ($ty == 'SA') {?>
                        <li class="menu">
                            <a href="?q=deleted_agent" class="left_menu_bull mainli" style="color:white;"> Deleted Link </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>