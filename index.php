<?php
session_start();

$user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
$UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
$PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;

if (!empty($_SESSION['UserId'])) {

    include 'model/oop.php';
    include 'model/Bill.php';
    include 'model/FormateHelper.php';

    $obj = new Controller();
    $bill = new Bill();
    $formater = new FormateHelper();

    $wpdata = $obj->details_by_cond('vw_user_info', "UserId='$user_id'");
    $acc = [];
    if ($wpdata) {
        if( isset($wpdata['WorkPermission']) && !empty($wpdata['WorkPermission']) ){
            if( $formater->is_serialized($wpdata['WorkPermission'])){
                $acc = unserialize($wpdata['WorkPermission']);
            }
        }
    }
    define("home", "#");

    $q = isset( $_GET['q'] ) ? $_GET['q'] : null;
    ?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
        <title>
            <?php
            if (isset($_GET['q']) && !empty($_GET['q'])) {
                echo ucwords(str_replace("_", " ", $q)) . '  ';
            } else {
                echo 'ISP Soft';
            }
            ?>
        </title>
        <?PHP include 'include/js_cs_link.php'; ?>
    </head>

    <body>
    <div class="container" id="content"
         style="width:100% !important; padding:0px; border:0px; background-color:#EAEAEA;">
        <div class="col-md-12" style="background:#100000; padding:0px; min-height:50px;">

            <!-- ============== header ========================== -->
            <?php include 'include/header.php'; ?>
            <!-- ============== header close ========================== -->
        </div>
        <div class="col-md-12" style="margin:15px 0px 15px 0px; padding:0px 15px 0px 0px;">

            <!-- ============== Start Menu ========================== -->
            <?php include 'include/sidebar.php'; ?>
            <!-- ============== End Menu ========================== -->

            <div class="col-md-10" style="background:#FFFFFF; border:1px solid #999999;">

                <?php
                if ($q == 'home') {
                    include 'include/body.php'; // menu page for permission check
                } elseif ($q == 'usercreate') {
                    ($obj->hasPermission($ty, 'usercreate')) ? include 'add/usercreate.php' : include 'include/body.php';
                } elseif ($q == 'view_user') {
                    ($obj->hasPermission($ty, 'view_user')) ? include 'view/view_user.php' : include 'include/body.php';
                } elseif ($q == 'add_agent') {
                    ($obj->hasPermission($ty, 'add_agent')) ? include 'add/add_agent.php' : include 'include/body.php';
                }  elseif ($q == 'add_static_agent') {
                    ($obj->hasPermission($ty, 'add_static_agent')) ? include 'add/add_static_agent.php' : include 'include/body.php';
                } 
                
                elseif ($q == 'view_agent') {
                    ($obj->hasPermission($ty, 'view_agent')) ? include 'view/view_agent.php' : include 'include/body.php';
                } elseif ($q == 'view_company_wise_link') {
                    ($obj->hasPermission($ty, 'view_company_wise_link')) ? include 'view/company_wise_link.php' : include 'include/body.php';
                }
                elseif ($q == 'view_new_join_customer') {
                    ($obj->hasPermission($ty, 'view_new_join_customer')) ? include 'view/view_new_join_customer.php' : include 'include/body.php';
                } elseif ($q == 'view_other_due') {
                    ($obj->hasPermission($ty, 'view_other_due')) ? include 'view/view_other_due.php' : include 'include/body.php';
                } elseif ($q == 'view_due_payment') {
                    ($obj->hasPermission($ty, 'view_due_payment')) ? include 'view/view_due_payment.php' : include 'include/body.php';
                }
                elseif ($q == 'excel_invoice_company_wise') {
                    ($obj->hasPermission($ty, 'excel_invoice_company_wise')) ? include 'view/company_wise_excel_invoice.php' : include 'include/body.php';
                }
                elseif ($q == 'view_bad_due_payment') {
                    ($obj->hasPermission($ty, 'view_bad_due_payment')) ? include 'view/view_bad_due_payment.php' : include 'include/body.php';
                } elseif ($q == 'view_all_dues') {
                    ($obj->hasPermission($ty, 'view_all_dues')) ? include 'view/view_all_dues.php' : include 'include/body.php';
                } elseif ($q == 'view_all_paid_client') {
                    ($obj->hasPermission($ty, 'view_all_paid_client')) ? include 'view/view_all_paid_client.php' : include 'include/body.php';
                }elseif ($q == 'view_double_payment') {
                    ($obj->hasPermission($ty, 'view_all_paid_client')) ? include 'view/view_double_payment.php' : include 'include/body.php';
                }
                elseif ($q == 'view_conn_charge') {
                    ($obj->hasPermission($ty, 'view_conn_charge')) ? include 'view/view_conn_charge.php' : include 'include/body.php';
                }elseif ($q == 'show_billing_compnay_list') {
                    ($obj->hasPermission($ty, 'show_billing_compnay_list')) ? include 'view/show_billing_compnay_list.php' : include 'include/body.php';
                }
                elseif ($q == 'company_bill_print') {
                        include 'pdf/company_bill_print.php';
                }
                 elseif ($q == 'view_income') {
                    ($obj->hasPermission($ty, 'view_income')) ? include 'view/view_income.php' : include 'include/body.php';
                } elseif ($q == 'income_report') {
                    ($obj->hasPermission($ty, 'income_report')) ? include 'view/report/income_report.php' : include 'include/body.php';
                } elseif ($q == 'view_account_head') {
                    ($obj->hasPermission($ty, 'view_account_head')) ? include 'view/view_account_head.php' : include 'include/body.php';
                }elseif ($q == 'view_account_sub_head') {
                    ($obj->hasPermission($ty, 'view_account_sub_head')) ? include 'view/view_account_sub_head.php' : include 'include/body.php';
                }elseif ($q == 'view_bonus') {
                    ($obj->hasPermission($ty, 'view_bonus')) ? include 'view/view_bonus.php' : include 'include/body.php';
                } elseif ($q == 'view_expense') {
                    ($obj->hasPermission($ty, 'view_expense')) ? include 'view/view_expense.php' : include 'include/body.php';
                }
                elseif ($q == 'panding_expence') {
                    ($obj->hasPermission($ty, 'panding_expence')) ? include 'view/panding_expence.php' : include 'include/body.php';
                } elseif ($q == 'expense_report') {
                    ($obj->hasPermission($ty, 'expense_report')) ? include 'view/report/expense_report.php' : include 'include/body.php';
                } 
                elseif ($q == 'salary_shit') {
                    ($obj->hasPermission($ty, 'salary_shit')) ? include 'view/salary_shit.php' : include 'include/body.php';
                }
                elseif ($q == 'monthly_new') {
                    ($obj->hasPermission($ty, 'monthly_new')) ? include 'view/statement/monthly_new.php' : include 'include/body.php';
                } elseif ($q == 'yearly') {
                    ($obj->hasPermission($ty, 'yearly')) ? include 'view/statement/yearly.php' : include 'include/body.php';
                } elseif ($q == 'acc_statement') {
                    ($obj->hasPermission($ty, 'acc_statement')) ? include 'view/report/acc_statement.php' : include 'include/body.php';
                }elseif ($q == 'create_xlxs') {
                    ($obj->hasPermission($ty, 'create_xlxs')) ? include 'excel/create_xlxs.php' : include 'include/body.php';
                }
                elseif ($q == 'add_customize_sms') {
                    ($obj->hasPermission($ty, 'add_customize_sms')) ? include 'sms/add_customize_sms.php' : include 'include/body.php';
                } elseif ($q == 'due_sms') {
                    ($obj->hasPermission($ty, 'due_sms')) ? include 'sms/add_duesms.php' : include 'include/body.php';
                } elseif ($q == 'occation') {
                    ($obj->hasPermission($ty, 'occation')) ? include 'sms/add_occationalsms.php' : include 'include/body.php';
                }
                elseif ($q == 'view_unpaid_bill') {
                    ($obj->hasPermission($ty, 'view_unpaid_bill')) ? include 'view/view_unpaid_bill.php' : include 'include/body.php';
                }elseif ($q == 'print_view_unpaid_bill') {
                    ($obj->hasPermission($ty, 'print_view_unpaid_bill')) ? include 'view/print_view_unpaid_bill.php' : include 'include/body.php';
                }elseif ($q == 'view_bill_collection') {
                    ($obj->hasPermission($ty, 'view_bill_collection')) ? include 'view/view_bill_collection.php' : include 'include/body.php';
                }elseif ($q == 'bill_collection_company_wise') {
                    ($obj->hasPermission($ty, 'bill_collection_company_wise')) ? include 'view/bill_collection_company_wise.php' : include 'include/body.php';
                }
                elseif ($q == 'view_running_paid_customer') {
                    ($obj->hasPermission($ty, 'view_running_paid_customer')) ? include 'view/view_running_paid_customer.php' : include 'include/body.php';
                }
                
                
                // stock
                elseif ($q == 'link_wise_stock_out') {
                    ($obj->hasPermission($ty, 'all_stock_out')) ? include 'modules/stock/link_wise_stock_out.php': include 'include/body.php';
                }
                elseif ($q == 'view_all_items') {
                    ($obj->hasPermission($ty, 'view_all_items')) ? include 'modules/stock/item.php': include 'include/body.php';
                }
                elseif ($q == 'view_stock_category') {
                    ($obj->hasPermission($ty, 'view_stock_in')) ? include 'modules/stock/view_all_category.php': include 'include/body.php';
                }elseif ($q == 'view_stock_in') {
                    ($obj->hasPermission($ty, 'view_stock_in')) ? include 'modules/stock/view_stock_in.php': include 'include/body.php';
                }elseif ($q == 'all_stock_in') {
                    ($obj->hasPermission($ty, 'all_stock_in')) ? include 'modules/stock/all_stock_in.php':include 'include/body.php';
                }elseif ($q == 'all_stock_out') {
                    ($obj->hasPermission($ty, 'all_stock_out')) ? include 'modules/stock/all_stock_out.php':include 'include/body.php';
                }
                elseif ($q == 'view_all_warehouse') {
                    ($obj->hasPermission($ty, 'view_all_warehouse')) ? include 'modules/stock/view_all_warehouse.php': include 'include/body.php';
                }
                elseif ($q == 'edit_warehouse') {
                    ($obj->hasPermission($ty, 'edit_warehouse')) ? include 'modules/stock/edit_warehouse.php': include 'include/body.php';
                }
                
                elseif ($q == 'view_all_bills') {($obj->userWorkPermission($acc,'view')) ? include 'modules/marketing/view_all_bills.php': include 'include/body.php'; }
                
                elseif ($q == 'bandthwith_bill') {($obj->userWorkPermission($acc,'view')) ? include 'modules/marketing/bandthwith_bill.php': include 'include/body.php'; }
                //stock end

               // marketing client


                elseif ($q == 'add_client') {
                    ($obj->hasPermission($ty, 'add_client')) ? include 'modules/marketing/add_client.php': include 'include/body.php';}
                elseif ($q == 'view_all_client') {
                    ($obj->hasPermission($ty, 'view_all_client')) ? include 'modules/marketing/view_all_client.php': include 'include/body.php'; }
                elseif ($q == 'view_all_rectification_list') {
                    ($obj->hasPermission($ty, 'view_all_rectification_list')) ? include 'modules/marketing/view_all_rectification_list.php': include 'include/body.php'; }

                //end marketing
                // Insert Page Start
                 elseif ($q == 'inactive_customer_sms') {
                    ($obj->hasPermission($ty, 'inactive_customer_sms')) ? include 'sms/inactive_customer_sms.php': include 'include/body.php';}
                    elseif ($q == 'marketing_sms') {
                    ($obj->hasPermission($ty, 'marketing_sms')) ? include 'sms/marketing_sms.php': include 'include/body.php';}
                elseif ($q == 'software_setting') {
                    ($obj->hasPermission($ty, 'software_setting')) ? include 'edit/software_setting.php': include 'include/body.php';
                } elseif ($q == 'add_customer') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/add_customer.php': include 'include/body.php';
                } elseif ($q == 'add_account_head') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/add_account_head.php': include 'include/body.php';
                }elseif ($q == 'add_sub_account_head') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/add_sub_account_head.php': include 'include/body.php';
                }elseif ($q == 'add_sector') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/add_sector.php': include 'include/body.php';
                } elseif ($q == 'add_expense') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/add_expense.php': include 'include/body.php';
                } elseif ($q == 'add_income') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/add_income.php': include 'include/body.php';
                } elseif ($q == 't_customer') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/t_customer.php': include 'include/body.php';
                } elseif ($q == 't_agent') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/t_agent.php': include 'include/body.php';
                } elseif ($q == 'add_payment') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/add_payment.php': include 'include/body.php';
                }elseif ($q == 'add_bad_payment') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/add_bad_payment.php': include 'include/body.php';
                } // View Page Start
                elseif ($q == 'user_details') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'details/user_details.php': include 'include/body.php';
                }elseif ($q == 'employe_details') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/employe_details.php': include 'include/body.php';
                }
                elseif ($q == 'view_all_zone') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_all_zone.php': include 'include/body.php';
                }
                elseif ($q == 'view_all_sub_zone') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_all_sub_zone.php': include 'include/body.php';
                }elseif ($q == 'view_all_company') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_all_company.php': include 'include/body.php';
                }elseif ($q == 'view_customer') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_customer.php': include 'include/body.php';
                } elseif ($q == 'view_report_paganition') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_report_paganition.php': include 'include/body.php';
                } elseif ($q == 'view_sector') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_sector.php': include 'include/body.php';
                } elseif ($q == 'send_due_sms') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'sms/send_due_sms.php': include 'include/body.php';
                } elseif ($q == 'occationsend') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'sms/send_occationalsms.php': include 'include/body.php';
                } elseif ($q == 'view_customer_payment') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_customer_payment.php': include 'include/body.php';
                } elseif ($q == 'print_client_bill') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/bill_collection_print.php': include 'include/body.php';
                } elseif ($q == 'view_agent_payment') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_agent_payment.php': include 'include/body.php';
                } elseif ($q == 'view_other_income_details') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_other_income_details.php': include 'include/body.php';
                } 
                elseif ($q == 'view_company_details') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_company_details.php': include 'include/body.php';
                } 
                
                elseif ($q == 'view_expense_details') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_expense_details.php': include 'include/body.php';
                } elseif ($q == 'deleted_agent') {
                    ($obj->userWorkPermission($acc,'delete')) ? include 'view/deleted_agent.php': include 'include/body.php';
                } elseif ($q == 'daily_acc_statement') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/report/daily_acc_statement.php': include 'include/body.php';
                } elseif ($q == 'printclient') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/print_client_list.php': include 'include/body.php';
                } elseif ($q == 'printpaymenthistory') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/printpaymenthistory.php': include 'include/body.php';
                } elseif ($q == 'dailyReport') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/report/dailyReport.php': include 'include/body.php';
                } elseif ($q == 'daily_expense_report') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/report/daily_expense_report.php': include 'include/body.php';
                } elseif ($q == 'dueReport') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/report/dueReport.php': include 'include/body.php';
                }elseif ($q == 'monthly') {
                } elseif ($q == 'billing_statement') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/report/billing_statement.php': include 'include/body.php';
                } elseif ($q == 'monthly') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/statement/monthly.php': include 'include/body.php';
                } elseif ($q == 'decadely') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/statement/decadely.php': include 'include/body.php';
                } elseif ($q == 'view_customer_payment_individual') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_customer_payment_individual.php': include 'include/body.php';
                } elseif ($q == 'view_customer_details') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/view_customer_details.php': include 'include/body.php';
                }

                // Edit Page Start
                elseif ($q == 'user_edit') {
                    ($ty == 'SA') ? include 'edit/user_edit.php': include 'include/body.php';
                } elseif ($q == 'user_ch_pass') {
                    ($ty == 'SA' || $ty == 'EO') ? include 'edit/user_ch_pass.php': include 'include/body.php';
                } elseif ($q == 'edit_branch') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_branch.php': include 'include/body.php';
                } elseif ($q == 'edit_service') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_service.php': include 'include/body.php';
                } elseif ($q == 'edit_airlines') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_airlines.php': include 'include/body.php';
                } elseif ($q == 'edit_vendor') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_vendor.php': include 'include/body.php';
                } elseif ($q == 'edit_account_head') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_account_head.php': include 'include/body.php';
                }elseif ($q == 'edit_account_sub_head') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_account_sub_head.php': include 'include/body.php';
                }elseif ($q == 'edit_sector') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_sector.php': include 'include/body.php';
                } elseif ($q == 'edit_customer') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_customer.php': include 'include/body.php';
                } elseif ($q == 'edit_agent') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_agent.php': include 'include/body.php';
                }elseif ($q == 'agent_remarks') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'view/agent_remarks.php': include 'include/body.php';
                }elseif ($q == 'edit_remarks') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'view/edit_remarks.php': include 'include/body.php';
                }
                elseif ($q == 'edit_expense') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_expense.php': include 'include/body.php';
                } elseif ($q == 'edit_income') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_income.php': include 'include/body.php';
                } elseif ($q == 'edit_customer_payment') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_customer_payment.php': include 'include/body.php';
                } elseif ($q == 'edit_agent_payment') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_agent_payment.php': include 'include/body.php';
                } elseif ($q == 'edit_t_agent') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_t_agent.php': include 'include/body.php';
                } elseif ($q == 'edit_t_customer') {
                } elseif ($q == 'edit_zone') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_zone.php': include 'include/body.php';
                }elseif ($q == 'edit_sub_zone') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_sub_zone.php': include 'include/body.php';
                }elseif ($q == 'edit_company') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_company.php': include 'include/body.php';
                } elseif ($q == 'edit_t_customer') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_t_customer.php': include 'include/body.php';
                } // Edit Page End

                elseif ($q == 'mikrotik_configure') {
                    ($ty == 'SA') ? include 'mikrotik/mikrotik_configure.php': include 'include/body.php';
                } elseif ($q == 'mikrotik_edit') {include 'mikrotik/mikrotik_edit.php';}elseif ($q == 'mikrotik_conn') {
                    ($ty == 'SA') ? include 'mikrotik/mikrotik_conn.php': include 'include/body.php';
                }
                elseif ($q == 'mikrotik_all_dues') {
                    ($ty == 'SA') ? include 'mikrotik/mikrotik_all_dues.php': include 'include/body.php';
                }
                elseif ($q == 'disconnectByDue') {
                    ($ty == 'SA') ? include 'mikrotik/disconnectByDue.php': include 'include/body.php';
                }
                elseif ($q == 'mikrotik_all_secret') {
                    ($ty == 'SA') ? include 'mikrotik/mikrotik_all_secret.php': include 'include/body.php';
                }
                elseif ($q == 'mikrotik_secret_withoutbill') {
                    ($ty == 'SA') ? include 'mikrotik/mikrotik_secret_withoutbill.php': include 'include/body.php';
                }elseif ($q == 'mikrotik_list') {
                    ($ty == 'SA') ? include 'mikrotik/mikrotik_list.php': include 'include/body.php';
                } elseif ($q == 'check_bill_info') {
                    ($ty == 'SA') ? include 'mikrotik/check_bill_info.php': include 'include/body.php';
                }
                elseif ($q == 'sel_mik_for_disable') {
                    ($ty == 'SA') ? include 'mikrotik/sel_mik_for_disable.php': include 'include/body.php';
                }
                 elseif ($q == 'online_mikrotik_secret') {
                    ($ty == 'SA') ? include 'mikrotik/mikrotik_all_running_secret.php': include 'include/body.php';
                }
                  elseif ($q == 'core_mikrotik') {
                    ($ty == 'SA') ? include 'mikrotik/core_mikrotik.php': include 'include/body.php';
                }
                 elseif ($q == 'mikrotik_user_details') {
                    ($ty == 'SA') ? include 'mikrotik/mikrotik_user_details.php': include 'include/body.php';
                }
                elseif ($q == 'mikrotik_unmatching') {
                    ($ty == 'SA') ? include 'mikrotik/mikrotik_unmatching.php': include 'include/body.php';
                }
                elseif ($q == 'connection_charge') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'add/add_connection_charge.php': include 'include/body.php';
                }
                
                
                // stock
                elseif ($q == 'view_individual_item') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'modules/stock/view_individual_item.php': include 'include/body.php';
                }elseif ($q == 'stock_out') {
                    ($obj->userWorkPermission($acc,'delete')) ? include 'modules/stock/add_stock_out.php': include 'include/body.php';
                }elseif ($q == 'add_stock_item') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'modules/stock/add_stock_item.php': include 'include/body.php';
                } elseif ($q == 'edit_stock_category') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'modules/stock/edit_stock_category.php': include 'include/body.php';
                }elseif ($q == 'edit_stock_item') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'modules/stock/edit_stock_item.php': include 'include/body.php';
                }elseif ($q == 'edit_stock_out') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'modules/stock/edit_stock_out.php': include 'include/body.php';
                }elseif ($q == 'view_individual_cus') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'modules/stock/view_individual_customer.php': include 'include/body.php';
                }elseif ($q == 'edit_stock_in') {
                    ($obj->userWorkPermission($acc,'edit')) ? include 'modules/stock/edit_stock_in.php': include 'include/body.php';
                }elseif ($q == 'stock_return') {
                    ($obj->userWorkPermission($acc,'add')) ? include 'modules/stock/stock_return.php': include 'include/body.php';
                }
                elseif ($q == 'stock_by_unit') {
                    ($obj->userWorkPermission($acc,'view')) ? include 'modules/stock/stock_by_unit.php': include 'include/body.php';
                }
                //stock end
                //marketing
                // marketing client
                elseif ($q == 'edit_client') {($obj->userWorkPermission($acc,'edit')) ? include 'modules/marketing/edit_client.php': include 'include/body.php'; }
                //end marketing
                 // employee
                elseif ($q == 'add_employee') {($obj->userWorkPermission($acc,'add')) ? include 'modules/employee/add_employee.php': include 'include/body.php'; }
                elseif ($q == 'view_all_employee') {($obj->userWorkPermission($acc,'view')) ? include 'modules/employee/view_all_employee.php': include 'include/body.php'; }

                elseif ($q == 'employee_transaction_monthly') {($obj->userWorkPermission($acc,'edit')) ? include 'modules/employee/employee_transaction_monthly.php': include 'include/body.php'; }
                elseif ($q == 'view_single_employee_transaction') {($obj->userWorkPermission($acc,'edit')) ? include 'modules/employee/view_single_employee_transaction.php': include 'include/body.php'; }

                elseif ($q == 'add_employee_salary') {($obj->userWorkPermission($acc,'add')) ? include 'modules/employee/add_employee_salary.php': include 'include/body.php'; }
                elseif ($q == 'print_single_employee_transaction') {($obj->userWorkPermission($acc,'view')) ? include 'modules/employee/print_single_employee_transaction.php': include 'include/body.php';}
                elseif ($q == 'employee_transaction') {($obj->userWorkPermission($acc,'view')) ? include 'modules/employee/employee_transaction.php': include 'include/body.php'; }
                elseif ($q == 'view_single_employee_transaction') {($obj->userWorkPermission($acc,'view')) ? include 'modules/employee/view_single_employee_transaction.php': include 'include/body.php'; }
                elseif ($q == 'edit_employee') {($obj->userWorkPermission($acc,'edit')) ? include 'modules/employee/edit_employee.php': include 'include/body.php'; }
                elseif ($q == 'add_employee_attendance') {($obj->userWorkPermission($acc,'add')) ? include 'modules/employee/add_employee_attendance.php': include 'include/body.php'; }
                elseif ($q == 'add_employee_attendance_test') {($obj->userWorkPermission($acc,'add')) ? include 'modules/employee/add_employee_attendance_test.php': include 'include/body.php'; }
                elseif ($q == 'view_employee_attendance') {($obj->userWorkPermission($acc,'view')) ? include 'modules/employee/view_employee_attendance.php': include 'include/body.php'; }
                elseif ($q == 'attendance_report') {($obj->userWorkPermission($acc,'view')) ? include 'modules/employee/attendance_report.php': include 'include/body.php'; }
                elseif ($q == 'office_settings') {($obj->userWorkPermission($acc,'add')) ? include 'modules/employee/office_settings.php': include 'include/body.php'; }
                /*elseif ($q == 'add_client') {
                    ($obj->hasPermission($ty, 'add_client')) ? include 'modules/marketing/add_client.php': include 'include/body.php';}*/
                // complain
                elseif ($q == 'add_complain') {($obj->hasPermission($ty, 'add_complain')) ?include 'modules/complain/add_complain.php': include 'include/body.php';}
                elseif ($q == 'add_complain_imminent_user') {include 'modules/complain/add_complain_imminent_user.php';}
                elseif ($q == 'edit_complain') {($obj->userWorkPermission($acc,'edit')) ? include 'modules/complain/edit_complain.php': include 'include/body.php';}
                elseif ($q == 'edit_complain_imminent_user') {($obj->userWorkPermission($acc,'edit')) ? include 'modules/complain/edit_complain_imminent_user.php': include 'include/body.php';}
                elseif ($q == 'view_complains') {($obj->hasPermission($ty, 'view_complains')) ? include 'modules/complain/view_complains.php': include 'include/body.php';}
                elseif ($q == 'customer_complain_details') {include 'modules/complain/customer_complain_details.php';}
                elseif ($q == 'complain_template') {include 'modules/complain/complain_template.php';}
                elseif ($q == 'edit_complain_template') {include 'modules/complain/edit_complain_template.php';}
                elseif ($q=='view_all_package') {
                    include 'view/view_all_package.php';
                }
                elseif($q=='edit_package'){($obj->userWorkPermission($acc,'edit')) ? include 'edit/edit_package.php': include 'include/body.php';
                }
                 
                else {
                    include 'include/body.php';
                }
                ?>
            </div>
            <!-- ============== End Container ========================== -->
        </div>
        <div class="col-md-12" id="footer" class="box">
            <p class="f-right" style="font-size:11px;">&copy; <?php echo date('Y'); ?> <a href="#" target="_blank">ISP
                    Company Software.</a>, All Rights Reserved</p>
        </div>
    </div>
    <script>
        // $(document).ready(function () {
        //     $.post("view/ajax_action/ajax_data_return.php", function (data) {

        //         $('div#header_area span.due_bill_amount').html(data.duePayment.toLocaleString());

        //     }, "json");
        // });
    </script>
    </body>
    </html>
    <?php
} else {
    header("location: include/login.php");
}
?>