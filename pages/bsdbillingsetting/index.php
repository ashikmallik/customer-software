<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$ip_add   = $_SERVER['REMOTE_ADDR'];
$userid   = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;

$customers  = $obj->view_all_by_cond("tbl_agent", "ag_status=1");
$templates  = $obj->view_all("tbl_complain_templates");
$apiKey     = $obj->getSettingValue('sms', 'pass');
$smsPass    = $obj->getSettingValue('sms', 'password');
$sender     = $obj->getSettingValue('sms', 'sender');
$smsUser    = $obj->getSettingValue('sms', 'user');
$billGenerateInfo = $obj->getSettingValue('billGenerate', 'billGenerate');

$logo        = $obj->getSettingValue('logo', 'logo');
$companyName = $obj->getSettingValue('excel', 'name');
$address     = $obj->getSettingValue('address', 'address');
$mobile      = $obj->getSettingValue('mobile', 'mobile');
$email       = $obj->getSettingValue('email', 'email');
$employees   = $obj->view_all_by_cond('tbl_employee', 'employee_status=1');
$supportLink = $obj->getSettingValue('supportLink ', 'supportLink');

$countDownInfo = $obj->rawSqlSingle("SELECT * FROM countdown_notice WHERE id = 1");

// ---------------------
// PROCEDURE EXECUTION
// ---------------------
$alert = "";

if (isset($_POST['execute_procedure'])) {

    try {
        $sql = "CALL insert_customer_billing_from_agent()";
        $run = $obj->rawSqlSingle($sql);
        $_SESSION['swal_alert'] = [
            'icon'  => 'success',
            'title' => 'Success',
            'text'  => 'Procedure executed successfully!'
        ];
        // header("Location: /?page=bsdbillingsetting");


    } catch (Exception $e) {
        $_SESSION['swal_alert'] = [
            'icon'  => 'error',
            'title' => 'Error',
            'text'  => $e->getMessage()
        ];
        // header("Location: /?page=bsdbillingsetting");

    }
}
?>



<!--update countdown-->
<?php
    if (isset($_POST['update_countdown'])) {
        try {
            $customerLimit = intval($_POST['customerLimit']);
            $billAmount    = floatval($_POST['monthly_bill']);
            // bill_use decides the first notice month:
            //   running_month_use - this month's bill is paid now -> next bill = +1 month (Sep -> Oct)
            //   next_month_use    - payment is kept as advance    -> next bill = +2 months (Sep -> Nov)
            $billUse     = ($_POST['bill_use'] ?? '') === 'next_month_use' ? 'next_month_use' : 'running_month_use';
            $monthsAhead = $billUse === 'next_month_use' ? 2 : 1;
            $nextBill    = new DateTime('first day of this month');
            $nextBill->modify("+$monthsAhead month");
            $billMonth   = (int) $nextBill->format('n');
            $billYear    = (int) $nextBill->format('Y');
            // Mirror the master: running month is paid (0); next-month payment is an advance (negative).
            $dueAdvance  = $billUse === 'next_month_use' ? -$billAmount : 0;
           $obj->rawSqlSingle("UPDATE countdown_notice
                           SET is_erp_and_billing_updated = 1, cus_limit = '$customerLimit', monthly_bill = $billAmount, dueadvance = $dueAdvance,
                               bill_month = $billMonth, bill_year = $billYear
                           WHERE id = 1");

            $_SESSION['swal_alert'] = [
                'icon'  => 'success',
                'title' => 'Success',
                'text'  => 'Countdown notice updated successfully!'
            ];

        } catch (Exception $e) {
            $_SESSION['swal_alert'] = [
                'icon'  => 'error',
                'title' => 'Error',
                'text'  => 'Failed to update countdown: ' . $e->getMessage()
            ];
        }
    }
?>


<div class="row">

    <?= $alert; ?>

    <!-- ==============================
         STORED PROCEDURE EXECUTE BOX
    ================================= -->
    <div class="col-md-4 mt-5">

        <div class="p-3 mb-3" style="background:#f8f9fa; border-left:4px solid #28a745; border-radius:8px;">
            <h6 class="text-success mb-2 text-center" style="font-size:14px;">
                Execute Customer Billing Procedure
            </h6>
        </div>

        <div class="card border-success shadow-sm" style="border-width:2px;">
            <div class="card-body text-center py-4">

                <h6 class="text-success mb-3" style="font-size:15px;">
                    ⚙️ Run Billing Generate Procedure
                </h6>

                <form method="post">
                    <button type="button" id="executeBtn"
                                class="btn btn-success btn-sm px-4">
                            <i class="fa fa-play"></i> Execute
                        </button>
                </form>

            </div>
        </div>
    </div>



        <!-- ==============================
         Create Customer in Billing and ERP Software
        ================================= -->
        <?php if(isset($countDownInfo["is_erp_and_billing_updated"]) && $countDownInfo["is_erp_and_billing_updated"] == 0){ ?>
            <div class="col-md-4 mt-5">

                <div class="p-3 mb-3" style="background:#f8f9fa; border-left:4px solid #28a745; border-radius:8px;">
                    <h6 class="text-info mb-2 text-center" style="font-size:14px;">
                        Create Customer in Billing and ERP Software
                    </h6>
                </div>

                <div class="card border-success shadow-sm" style="border-width:2px;">
                    <div class="card-body text-center py-4">

                        <h6 class="text-success mb-3" style="font-size:15px;">
                            Create Customer in Billing and ERP Software
                        </h6>

                        <form method="post">
                            <button type="button" id="create_user"
                                    class="btn btn-success btn-sm px-4">
                                <i class="fa fa-play"></i> Execute
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        <?php } ?>

    <!-- Your other boxes remain exactly same -->
</div>

<?php $obj->start_script(); ?>


<script src="assets/js/pages/form-advanced.init.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->



<script>
document.getElementById('executeBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you really want to execute the stored procedure?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Execute!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Ask for text confirmation
            Swal.fire({
                title: 'Type "CONFIRMED" to proceed',
                input: 'text',
                inputPlaceholder: 'Type CONFIRMED here...',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (value !== 'CONFIRMED') {
                        return 'You must type "CONFIRMED" to proceed!';
                    }
                }
            }).then((confirmation) => {
                if (confirmation.isConfirmed) {
                    // Create and submit the form
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.style.display = 'none';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'execute_procedure';
                    input.value = '1';
                    form.appendChild(input);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });
});


// new one start
window.addEventListener('load', async () => {
    try {
        const response = await fetch('https://billing.bsdisp.com/api.php?get_users=1', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Failed to load users');
        }

        billingUsers = await response.json();

        const response2 = await fetch('https://billing.bsdisp.com/api.php?get_packages=1', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        if (!response2.ok) {
            throw new Error('Failed to load users');
        }

        billPackages = await response2.json();

        // Optional: যদি চাও তাহলে একটা normal select-এ populate করতে পারো (debug-এর জন্য)
        // const select = document.getElementById('billing-users-select');
        // select.innerHTML = '<option value="">-- Select --</option>';
        // billingUsers.forEach(user => {
        //     const option = document.createElement('option');
        //     option.value = user.UserId;
        //     option.textContent = user.FullName;
        //     select.appendChild(option);
        // });

    } catch (error) {
        billingUsers = []; // fallback
        billPackages = []; // fallback
    }
});
document.getElementById('create_user').addEventListener('click', function() {
    Swal.fire({
        title: 'Confirmation Required',
        html: `

           <div style="text-align: left; margin-bottom: 20px;">
                <label for="select-billing-person" style="font-weight: bold; display: block; margin-bottom: 8px;">Select Billing Person:</label>
                <select id="select-billing-person" class="swal2-select" style="width: 100%;">
                    <option value="">Select Billing Person</option>
                    ${billingUsers.map(user =>
                        `<option value="${user.UserId}">${user.FullName}</option>`
                    ).join('')}
                </select>
            </div>

            <div style="text-align: left; margin-bottom: 20px;">
                <label for="select-bill-package" style="font-weight: bold; display: block; margin-bottom: 8px;">Select Package:</label>
                <select id="select-bill-package" class="swal2-select" style="width: 100%;">
                    <option value="">Select Package</option>
                    ${billPackages.map(bill =>
                        `<option value="${bill.net_speed}">${bill.package_name}</option>`
                    ).join('')}
                </select>
            </div>

            <div style="text-align: left;">
                <label for="bill-amount" style="font-weight: bold; display: block; margin-bottom: 8px;">Bill Amount:</label>
                <input id="bill-amount" class="swal2-input" type="number" min="1" placeholder="Enter bill amount" autocomplete="off">
            </div>

            <div style="text-align: left;">
                <label for="swal-customer-limit" style="font-weight: bold; display: block; margin-bottom: 8px;">Customer Limit:</label>
                <input id="swal-customer-limit" class="swal2-input" type="number" min="1" placeholder="Enter customer limit (e.g. 50)" autocomplete="off">
            </div>

            <div style="text-align: left; margin-top: 12px;">
                <label for="bill-use" style="font-weight: bold; display: block; margin-bottom: 8px;">First payment covers:</label>
                <select id="bill-use" class="swal2-select" style="width: 100%;">
                    <option value="" selected>-- Select --</option>
                    <option value="running_month_use">Use from Running Month</option>
                    <option value="next_month_use">Use from Next Month</option>
                </select>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Execute Now',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const billingPerson = document.getElementById('select-billing-person').value;
            const billPakcageName = document.getElementById('select-bill-package').value;
            const billPackageAmount = document.getElementById('bill-amount').value;
            const customerLimit = document.getElementById('swal-customer-limit').value.trim();



            if (!billingPerson) {
                Swal.showValidationMessage('Please select a billing person');
                return false;
            }

            if (!billPakcageName) {
                Swal.showValidationMessage('Please select a Package');
                return false;
            }

            if (!billPackageAmount) {
                Swal.showValidationMessage('Please type bill amount');
                return false;
            }

            if (!customerLimit || isNaN(customerLimit) || Number(customerLimit) < 1) {
                Swal.showValidationMessage('Please enter a valid customer limit (minimum 1)');
                return false;
            }

            // সব ঠিক থাকলে return করো
            const billUse = document.getElementById('bill-use').value;
            if (billUse !== 'running_month_use' && billUse !== 'next_month_use') {
                Swal.showValidationMessage('Please select what the first payment covers (running month / next month)');
                return false;
            }

            return {
                billingPerson: billingPerson,
                customerLimit: Number(customerLimit),
                billAmount: billPackageAmount,
                billPackageName: billPakcageName,
                billUse: billUse
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { billingPerson, customerLimit, billPackageName, billAmount, billUse } = result.value;
            console.log('Billing Person:', billingPerson);
            console.log('Customer Limit:', customerLimit);

            // এখান থেকে তোমার আসল logic শুরু (loading + API calls + countdown update)
            Swal.fire({
                title: 'Executing...',
                text: 'Please wait while creating users in both systems.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // তোমার বাকি কোড (variables + fetch calls) এখানে চালাও
            const agName = <?= json_encode($companyName) ?>;
            const agMobile = <?= json_encode($mobile) ?>;
            const agEmail = <?= json_encode($email) ?>;
            const clientName = <?= json_encode($companyName) ?>;
            const clientEmail = <?= json_encode($email) ?>;
            const clientPhone = <?= json_encode($mobile) ?>;
            const bsderpLink = <?= json_encode($supportLink) ?>;
            const address = <?= json_encode($address) ?>;
            const clientPassword = "123456";
            const currentDomain = window.location.origin;
            const hostname = window.location.hostname;
            const parts = hostname.split('.');
            let subdomain = '';
            if (parts.length >= 3) {
                subdomain = parts[0];
            } else {
                subdomain = '';
            }

            // Show loading
            Swal.fire({
                title: 'Executing...',
                text: 'Please wait while creating users in both systems.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // First API: https://billing.bsdisp.com/api.php
            const firstApiData = {
                "CREATE_AGENT": 1,
                "ag_name": agName,
                "ag_mobile_no": agMobile,
                "ag_email": agEmail,
                "domain_link": currentDomain,
                "ip": subdomain,
                "ag_office_address": address,
                "entry_by": billingPerson,
                "running_customer": customerLimit,
                "taka": billAmount,
                "mb": billPackageName,
                "bill_use": billUse,
            };

            fetch('https://billing.bsdisp.com/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(firstApiData)
            })
            .then(response1 => {
                if (!response1.ok) {
                    throw new Error(`First API failed: ${response1.status}`);
                }
                return response1.json(); // or .text() if not JSON
            })
            .then(data1 => {
                console.log('First API success:', data1);

                // Second API: https://bsderp.test/api/create-client
                const secondApiData = {
                    "name": clientName,
                    "email": clientEmail,
                    "phone": clientPhone,
                    "password": clientPassword,
                    "website": currentDomain,
                    "address": address
                };

                return fetch(`${bsderpLink}/api/create-client`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(secondApiData)
                })
                .then(response2 => {
                    if (!response2.ok) {
                        throw new Error(`Second API failed: ${response2.status}`);
                    }
                    return response2.json(); // or .text() if not JSON
                })
                .then(data2 => {
                    console.log('Second API success:', data2);

                    // Both successful - show success
                    Swal.fire({
                        title: 'Success!',
                        text: 'Users created in both billing and ERP systems.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: `Failed to create users: ${error.message}`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });


            // UPDATE COUNTDOWN start
            const countdownId = 1;

            if (countdownId > 0) {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'update_countdown': '1',
                        'countdown_id': countdownId,
                        'customerLimit': customerLimit,
                        'monthly_bill': billAmount,
                        'bill_use': billUse
                    })
                })
                .then(response => response.text())
                .then(() => {
                    console.log('Countdown update requested');
                })
                .catch(err => {
                    console.error('Countdown update failed:', err);
                });
            }
            // UPDATE COUNTDOWN end
        }
    });
});
</script>


<?php if (isset($_SESSION['swal_alert'])): ?>
    <script>
        let alertData = <?= json_encode($_SESSION['swal_alert']) ?>;
        Swal.fire(alertData);
    </script>
    <?php
    unset($_SESSION['swal_alert']);
endif; ?>



<?php $obj->end_script(); ?>
