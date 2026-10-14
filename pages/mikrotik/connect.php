<!--cards start -->
<?php
// One slot per Mikrotik; each slot shows a skeleton and is filled by its own request
$mkCardRows = $obj->getAllData('mikrotik_user') ?: [];
$mkCardCount = count($mkCardRows);
// 1 card -> full width, 2 -> half, 3 -> one third, 4+ -> 4 per row
$mkCardCol = $mkCardCount === 1 ? 'col-12' : ($mkCardCount === 2 ? 'col-lg-6' : ($mkCardCount === 3 ? 'col-xl-4 col-md-6' : 'col-xl-3 col-md-6'));
?>
<div class="row mt-4" id="mikrotikCards" data-single="<?= $mkCardCount === 1 ? '1' : '0' ?>">
    <?php foreach ($mkCardRows as $mkCardRow) { ?>
        <div class="<?= $mkCardCol ?> mb-4 mk-card-slot" data-id="<?= (int) $mkCardRow['id'] ?>">
            <div class="card mk-card h-100">
                <div class="card-body p-20 placeholder-glow">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-16">
                        <div class="d-flex align-items-center gap-12 flex-grow-1">
                            <span class="placeholder mk-card-icon"></span>
                            <div class="flex-grow-1">
                                <span class="placeholder col-6 d-block mb-2"></span>
                                <span class="placeholder col-3 d-block"></span>
                            </div>
                        </div>
                        <span class="placeholder mk-ring"></span>
                    </div>
                    <div class="mk-stats">
                        <span class="placeholder mk-stat" style="height: 56px;"></span>
                        <span class="placeholder mk-stat" style="height: 56px;"></span>
                        <span class="placeholder mk-stat" style="height: 56px;"></span>
                    </div>
                </div>
                <div class="card-footer mk-card-footer placeholder-glow">
                    <span class="placeholder col-3"></span>
                    <span class="placeholder col-2" style="height: 30px;"></span>
                </div>
            </div>
        </div>
    <?php } ?>
</div>


<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <!-- Form Wizard Start -->
            <div class="form-wizard">
                <fieldset class="wizard-fieldset show">
                    <h6 class="text-md text-neutral-500">Create New Mikrotik</h6>
                    <div class="row gy-3">
                        <div class="col-sm-3">
                            <label class="form-label">User Name*</label>
                            <div class="position-relative">
                                <input type="text" class="form-control wizard-required" name="username" id="username" placeholder="Enter Username" required>
                                <div class="wizard-form-error"></div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Password*</label>
                            <div class="position-relative">
                                <input type="password" class="form-control wizard-required" name="password" id="password" placeholder="Enter Password" required>
                                <div class="wizard-form-error"></div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">IP*</label>
                            <div class="position-relative">
                                <input type="text" class="form-control wizard-required" name="ip" id="ip" placeholder="Enter IP" required>
                                <div class="wizard-form-error"></div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Port*</label>
                            <div class="position-relative">
                                <select class="form-control wizard-required" name="port" id="port">
                                    <option value="">Select</option>
                                    <option value="8721">8721</option>
                                    <option value="8725">8725</option>
                                    <option value="8728">8728</option>
                                    <option value="8011">8011</option>
                                    <option value="9000">9000</option>
                                </select>
                                <div class="wizard-form-error"></div>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <label class="form-label"></label>
                            <button type="button" class="btn btn-success-600 radius-8 p-20 w-60-px h-50-px d-flex align-items-center justify-content-center gap-2" id="save-button">
                                <iconify-icon icon="mdi:plus" class="text-2xl"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </fieldset>
            </div>
            <!-- Form Wizard End -->
        </div>
    </div>
</div>


<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">Mikrotik List</h5>
    </div>
    <div class="card-body">
        <table
            class="table table-striped bordered-table mb-0"
            id="mikrotikTable"
            data-page-length="10">
            <thead>
                <tr>
                    <th scope="col">SL.No</th>
                    <th scope="col">User Name</th>
                    <th scope="col">Password</th>
                    <th scope="col">IP</th>
                    <th scope="col">Port</th>
                    <th scope="col">Status</th>
                    <th scope="col">Connection Status</th>
                    <th scope="col" style="white-space: normal; width: 130px;">Secrets<br><span class="text-xs fw-normal text-secondary-light">(matching and unmatching)</span></th>
                    <?php if ($obj->userWorkPermission('edit')) { ?>
                        <th scope="col">Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Edit Mikrotik Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label for="edit-username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit-username" name="mik_username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-password" class="form-label">Password</label>
                        <input type="text" class="form-control" id="edit-password" name="mik_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-ip" class="form-label">IP Address</label>
                        <input type="text" class="form-control" id="edit-ip" name="mik_ip" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-port" class="form-label">Port</label>
                        <input type="text" class="form-control" id="edit-port" name="mik_port" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success waves-effect waves-light" id="saveEditButton">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<style>
    .mk-details-tabs .nav-link {
        color: var(--success-600);
    }

    .mk-details-tabs .nav-link.active {
        background-color: var(--success-600);
        color: #fff;
    }

    /* Mikrotik summary card */
    .mk-card {
        border: 1px solid var(--border-color);
        border-top: 4px solid var(--success-600);
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(16, 24, 40, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .mk-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(16, 24, 40, 0.1);
    }

    .mk-card-icon {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--success-600);
        background: rgba(22, 163, 74, 0.12);
    }

    .mk-card-badge {
        flex-shrink: 0;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 20px;
        color: var(--success-600);
        background: rgba(22, 163, 74, 0.12);
    }

    .mk-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .mk-stat {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 8px;
        border-radius: 10px;
        text-decoration: none;
        transition: filter 0.2s ease;
    }

    .mk-stat:hover {
        filter: brightness(0.95);
    }

    .mk-stat-icon {
        font-size: 22px;
        flex-shrink: 0;
    }

    .mk-stat-value {
        display: block;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.15;
    }

    .mk-stat-label {
        display: block;
        font-size: 12px;
        font-weight: 500;
    }

    .mk-stat-online {
        color: var(--success-600);
        background: rgba(22, 163, 74, 0.1);
    }

    .mk-stat-offline {
        color: var(--warning-600);
        background: rgba(255, 159, 41, 0.12);
    }

    .mk-stat-disabled {
        color: var(--danger-600);
        background: rgba(220, 38, 38, 0.1);
    }

    .mk-ring {
        width: 72px;
        height: 72px;
        flex-shrink: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: conic-gradient(var(--success-600) calc(var(--percent) * 1%), rgba(22, 163, 74, 0.12) 0);
    }

    .mk-ring-inner {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--white);
    }

    .mk-ring-value {
        font-size: 16px;
        font-weight: 700;
        line-height: 1.1;
        color: var(--success-600);
    }

    .mk-ring-label {
        font-size: 11px;
        color: var(--text-secondary-light);
    }

    .mk-card-failed {
        border-top-color: var(--danger-600);
    }

    .mk-card-failed .mk-card-icon {
        color: var(--danger-600);
        background: rgba(220, 38, 38, 0.1);
    }

    .mk-failed {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 66px;
        padding: 12px 16px;
        border-radius: 10px;
        color: var(--danger-600);
        background: rgba(220, 38, 38, 0.08);
    }

    /* Single Mikrotik on a full-width row */
    .mk-card-single {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .mk-single-identity {
        flex: 1 1 240px;
        min-width: 0;
    }

    .mk-single-stats {
        flex: 2 1 360px;
        max-width: 640px;
    }

    .mk-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        background: transparent;
        border-top: 1px solid var(--border-color);
    }
</style>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="detailsModalLabel">Mikrotik Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success-600" id="detailsRefresh">Refresh</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {
        // Define the mkConnectCheck function within the ready function or globally if required
        function mkConnectCheck(id) {
            $.ajax({
                type: "GET",
                url: "./pages/mikrotik/connect_ajax.php",
                data: {
                    'mkid': id
                },
                dataType: "JSON",
                success: function(response) {
                    if (response.connection) {
                        $(`#m${id}`).html(`<button class="btn btn-success" type="button">${response.status}</button>`);
                    } else {
                        $(`#m${id}`).html(`<button class="btn btn-danger" type="button">${response.status}</button>`);
                    }
                },
                error: function() {
                    $(`#m${id}`).html(`<button class="btn btn-danger" type="button">Error</button>`);
                }
            });
        }

        // Initialize DataTable
        $('#mikrotikTable').DataTable({
            "ajax": "./pages/mikrotik/mikrotik.php",
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "paging": true, // Enable pagination
            "searching": true, // Enable searching
            "info": true,
            "ordering": true,

            "columns": [{
                    "data": "sl"
                },
                {
                    "data": "mik_username"
                },
                {
                    "data": "mik_password"
                },
                {
                    "data": "mik_ip"
                },
                {
                    "data": "mik_port"
                },
                {
                    "data": "status",
                    // "render": function(data, type, row) {
                    //     return data ? "Enabled" : "Disabled";
                    // }
                },
                {
                    "data": "id",
                    "render": function(data, type, row) {
                        return `<span id="m${data}">
                        <button class="btn btn-primary" type="button" disabled>
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...
                        </button>
                    </span>`;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `<a href="?page=mikrotik_all_secret_without_filter&mikrotik_id=${data}" class="btn btn-sm btn-success-600 radius-8 d-inline-flex align-items-center gap-1 text-nowrap">
                            <iconify-icon icon="mdi:account-key-outline"></iconify-icon> View Secrets
                        </a>`;
                    }
                },
                {
                    "data": null,
                    "orderable": false, // Disable ordering for this column
                    "render": function(data, type, row) {
                        const detailsBtn = `
                        <a href="javascript:void(0)" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center details-btn" data-id="${row.id}" title="Details">
                        <iconify-icon icon="mdi:eye-outline"></iconify-icon></a>`;
                        if (row.status === "Disabled") {
                            return detailsBtn + `
                        <a href="javascript:void(0)"class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center edit-btn" data-id="${row.id}">
                        <iconify-icon icon="lucide:edit"></iconify-icon></a>
                        <a href="javascript:void(0)"class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center restore-btn" data-id="${row.id}">
                         <iconify-icon icon="mdi:toggle-switch"></iconify-icon></a>
                         <a href="javascript:void(0)" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center delete-btn" data-id="${row.id}">
                            <iconify-icon icon="mdi:cancel"></iconify-icon>
                        </a>
                        `;
                        } else {
                            return detailsBtn + `
                        <a href="javascript:void(0)"class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center edit-btn" data-id="${row.id}">
                        <iconify-icon icon="lucide:edit"></iconify-icon></a>
                        <a href="javascript:void(0)"class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center delete-btn" data-id="${row.id}">
                        <iconify-icon icon="mdi:cancel"></iconify-icon></a>
                       
                        `;
                        }
                    }
                }
            ],
            "responsive": true,
            "paging": false,
            "searching": false,
            "ordering": false,
            "info": false
        });

        // Call the connection check function for each row after table draw
        $('#mikrotikTable').on('xhr.dt', function(e, settings, json) {
            if (json && json.data) {
                json.data.forEach(function(row) {
                    mkConnectCheck(row.id);
                });
            }
        });
        $('#save-button').click(function(e) {
            e.preventDefault();
            // Collect values from the input fields
            var username = $('#username').val().trim();
            var password = $('#password').val().trim();
            var ip = $('#ip').val().trim();
            var port = $('#port').val().trim();

            // Validation flags
            var isValid = true;


            if (!ip) {
                Swal.fire({
                    title: 'Validation Error',
                    text: 'IP Address is required.',
                    icon: 'error',
                    // toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1000
                });
                isValid = false;
            }
            if (!password) {
                Swal.fire({
                    title: 'Validation Error',
                    text: 'Password is required.',
                    icon: 'error',
                    // toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1000
                });
                isValid = false;
            }
            // Validate input fields
            if (!username) {
                Swal.fire({
                    title: 'Validation Error',
                    text: 'Username is required.',
                    icon: 'error',
                    // toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1000
                });
                isValid = false;
            }

            if (isValid) {
                var data = {
                    username: username,
                    password: password,
                    ip: ip,
                    port: port
                };

                // Send the data via AJAX to the server
                $.ajax({
                    url: './pages/mikrotik/connect_update_ajax.php', // Adjust to your server-side script URL
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success == false) {
                            Swal.fire(
                                'Error!',
                                response.status,
                                'error'
                            );
                            $('#username').val('');
                            $('#password').val('');
                            $('#ip').val('');
                            $('#port').val('');
                        } else if (response.success == true) {
                            Swal.fire(
                                'Success',
                                response.status,
                                'success'
                            );
                            $('#username').val('');
                            $('#password').val('');
                            $('#ip').val('');
                            $('#port').val('');

                            $('#mikrotikTable').DataTable().ajax.reload();
                            mkConnectCheck(id); // Call mkConnectCheck with the ID
                        } else {
                            Swal.fire(
                                'Error!',
                                'Something went wrong. Please try again later.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'Something went wrong. Please try again later.',
                            'error'
                        );
                    }
                });
            }
        });
        $(document).on('click', '.restore-btn', function() {
            var id = $(this).data('id'); // Get the id from data-id attribute

            $.ajax({
                type: "POST",
                url: "./pages/mikrotik/connect_ajax.php",
                data: {
                    'mkid': id
                },
                dataType: "JSON",
                success: function(response) {
                    Swal.fire(
                        'Updated!',
                        'Your Mikrotik has been updated.',
                        'success'
                    );
                    mkConnectCheck(id); // Call mkConnectCheck with the ID
                    $('#mikrotikTable').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'Something went wrong. Please try again later.',
                        'error'
                    );
                }
            });

        });
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id'); // Get the id from data-id attribute
            $.ajax({
                type: "POST",
                url: "./pages/mikrotik/connect_update_ajax.php",
                data: {
                    'mikrotik_id': id
                },
                dataType: "JSON",
                success: function(response) {
                    Swal.fire(
                        'Disconnected!',
                        'Your Mikrotik has been disconnected.',
                        'success'
                    );
                    mkConnectCheck(id); // Call mkConnectCheck with the ID
                    $('#mikrotikTable').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'Something went wrong. Please try again later.',
                        'error'
                    );
                }
            });

        });


        // Mikrotik details modal
        let detailsId = null;

        function esc(value) {
            return $('<div>').text(value ?? '').html();
        }

        function formatBytes(bytes) {
            bytes = Number(bytes) || 0;
            const units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
            let i = 0;
            while (bytes >= 1024 && i < units.length - 1) {
                bytes /= 1024;
                i++;
            }
            return bytes.toFixed(i ? 2 : 0) + ' ' + units[i];
        }

        function usageBar(label, percent, text) {
            const color = percent >= 85 ? 'bg-danger' : (percent >= 60 ? 'bg-warning' : 'bg-success');
            return `
                <div class="col-md-4">
                    <div class="border radius-8 p-16 h-100">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">${label}</span>
                            <span class="fw-semibold">${percent}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar ${color}" style="width: ${percent}%"></div>
                        </div>
                        <small class="text-muted">${text}</small>
                    </div>
                </div>`;
        }

        function statusBadge(disabled) {
            if (disabled === 'true') return '<span class="badge bg-danger">Disabled</span>';
            return '<span class="badge bg-success">Enabled</span>';
        }

        // columns: [[header, row => html], ...]
        function detailsTable(rows, columns) {
            if (!rows.length) return '<p class="text-muted mb-0">No data found.</p>';
            const head = columns.map(c => `<th>${c[0]}</th>`).join('');
            const body = rows.map(r => '<tr>' + columns.map(c => `<td>${c[1](r)}</td>`).join('') + '</tr>').join('');
            return `<div class="table-responsive"><table class="table table-sm table-striped bordered-table mb-0"><thead><tr>${head}</tr></thead><tbody>${body}</tbody></table></div>`;
        }

        function infoRow(label, value) {
            return `<tr><th class="text-muted fw-normal" style="width: 40%;">${label}</th><td class="fw-semibold">${esc(value) || '-'}</td></tr>`;
        }

        function renderDetails(d) {
            const s = d.system;
            const cpu = s.cpu_load;
            const ramUsed = s.total_memory - s.free_memory;
            const ramPercent = s.total_memory ? Math.round(ramUsed / s.total_memory * 100) : 0;
            const hddUsed = s.total_hdd - s.free_hdd;
            const hddPercent = s.total_hdd ? Math.round(hddUsed / s.total_hdd * 100) : 0;
            const apiService = d.api_service;
            const field = key => r => esc(r[key]) || '-';

            const health = d.health.map(h => `
                <div class="col-6 col-md-3">
                    <div class="border radius-8 p-12 text-center">
                        <small class="text-muted d-block">${esc(h.name)}</small>
                        <span class="fw-semibold">${esc(h.value)} ${esc(h.type)}</span>
                    </div>
                </div>`).join('');

            const tabs = [
                ['overview', 'Overview', `
                    <div class="row g-3 mb-3">
                        ${usageBar('CPU Load', cpu, `${esc(s.cpu_count)} core &middot; ${esc(s.cpu_frequency)} MHz &middot; ${esc(s.cpu)}`)}
                        ${usageBar('RAM', ramPercent, `${formatBytes(ramUsed)} used of ${formatBytes(s.total_memory)}`)}
                        ${usageBar('Storage', hddPercent, `${formatBytes(hddUsed)} used of ${formatBytes(s.total_hdd)}`)}
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6"><div class="border radius-8 p-12 text-center"><small class="text-muted d-block">PPP Active</small><span class="fw-semibold text-success">${d.counts.ppp_active}</span></div></div>
                        <div class="col-6"><div class="border radius-8 p-12 text-center"><small class="text-muted d-block">PPP Secret</small><span class="fw-semibold">${d.counts.ppp_secret}</span></div></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <table class="table table-sm mb-0">
                                ${infoRow('Identity', s.identity)}
                                ${infoRow('Board', s.board)}
                                ${infoRow('Model', s.model)}
                                ${infoRow('Serial Number', s.serial)}
                                ${infoRow('Architecture', s.architecture)}
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm mb-0">
                                ${infoRow('RouterOS Version', s.version)}
                                ${infoRow('Firmware', s.firmware + (s.upgrade_firmware && s.upgrade_firmware !== s.firmware ? ` (upgrade: ${s.upgrade_firmware})` : ''))}
                                ${infoRow('Uptime', s.uptime)}
                                ${infoRow('API Login', `${d.login.username} @ ${d.login.ip}:${d.login.port}`)}
                                ${infoRow('API Service Port', apiService ? apiService.port + (apiService.disabled === 'true' ? ' (disabled)' : '') : '')}
                            </table>
                        </div>
                    </div>
                    ${health ? `<h6 class="text-md mt-4 mb-2">Health</h6><div class="row g-2">${health}</div>` : ''}`],
                ['profiles', 'PPP Profiles', detailsTable(d.ppp_profiles, [
                    ['Name', field('name')],
                    ['Local Address', field('local-address')],
                    ['Remote Address', field('remote-address')],
                    ['Rate Limit', field('rate-limit')],
                    ['DNS', field('dns-server')],
                    ['Only One', field('only-one')],
                ])],
                ['packages', 'Packages', detailsTable(d.packages, [
                    ['Name', field('name')],
                    ['Version', field('version')],
                    ['Build Time', field('build-time')],
                    ['Status', r => statusBadge(r.disabled)],
                ])],
            ];

            const nav = tabs.map((t, i) => `
                <li class="nav-item" role="presentation">
                    <button class="nav-link ${i === 0 ? 'active' : ''}" data-bs-toggle="pill" data-bs-target="#mk-${t[0]}" type="button" role="tab">${t[1]}</button>
                </li>`).join('');
            const panes = tabs.map((t, i) => `<div class="tab-pane fade ${i === 0 ? 'show active' : ''}" id="mk-${t[0]}" role="tabpanel">${t[2]}</div>`).join('');

            $('#detailsModalLabel').text(`Mikrotik Details - ${s.identity || d.login.ip}`);
            $('#detailsBody').html(`<ul class="nav nav-pills mk-details-tabs mb-3 flex-wrap gap-2" role="tablist">${nav}</ul><div class="tab-content">${panes}</div>`);
        }

        function loadDetails(id) {
            $('#detailsBody').html(`
                <div class="text-center py-5">
                    <span class="spinner-border text-success-600" role="status" aria-hidden="true"></span>
                    <p class="mt-2 text-muted mb-0">Loading...</p>
                </div>`);
            $.ajax({
                type: 'GET',
                url: './pages/mikrotik/mikrotik_details_ajax.php',
                data: {
                    mkid: id
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.success) {
                        renderDetails(response);
                    } else {
                        $('#detailsBody').html(`<div class="alert alert-danger mb-0">${esc(response.status)}</div>`);
                    }
                },
                error: function(xhr) {
                    $('#detailsBody').html(`<div class="alert alert-danger mb-0">Request failed (HTTP ${xhr.status})</div>`);
                }
            });
        }

        $(document).on('click', '.details-btn', function() {
            detailsId = $(this).data('id');
            $('#detailsModalLabel').text('Mikrotik Details');
            $('#detailsModal').modal('show');
            loadDetails(detailsId);
        });

        $('#detailsRefresh').click(function() {
            if (detailsId) loadDetails(detailsId);
        });

        $('#mikrotikTable').on('click', '.edit-btn', function() {
            var id = $(this).data('id');

            // Fetch data using AJAX
            $.ajax({
                url: './pages/mikrotik/connect_update_ajax.php',
                type: 'GET',
                data: {
                    mkid: id
                },
                success: function(response) {


                    // Assuming the response is JSON with the data
                    $('#edit-id').val(response.id);
                    $('#edit-username').val(response.mik_username);
                    $('#edit-password').val(response.mik_password);
                    $('#edit-ip').val(response.mik_ip);
                    $('#edit-port').val(response.mik_port);

                    // Show the modal
                    $('#editModal').modal('show');
                },
                error: function(xhr, status, error) {
                    alert('Error fetching data: ' + error);
                }
            });
        });

        $('#saveEditButton').click(function() {
            var id = $('#edit-id').val(); // Get the ID from the hidden input (read-only)
            var username = $('#edit-username').val(); // Get the new username
            var password = $('#edit-password').val(); // Get the new password
            var ip = $('#edit-ip').val(); // Get the new IP
            var port = $('#edit-port').val(); // Get the new port


            $.ajax({
                url: './pages/mikrotik/connect_update_ajax.php',
                type: 'POST',
                data: {
                    mkid: id,
                    mik_username: username,
                    mik_password: password,
                    mik_ip: ip,
                    mik_port: port
                },
                success: function(response) {
                    //Check Status
                    if (response.success) {
                        Swal.fire(
                            'Updated!',
                            `${response.status}`,
                            'success'
                        );
                        $('#editModal').modal('hide');
                        $('#mikrotikTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire(
                            'ERROR!',
                            `${response.status}`,
                            'error'
                        );
                        $('#editModal').modal('hide');
                        $('#mikrotikTable').DataTable().ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        html: `<b>HTTP:</b> ${xhr.status}<br><b>Status:</b> ${status}`,
                    });
                }

            });
        });

    });
</script>


<script>
    // mirkotik offline online



    function mikrotikIdentity(mk) {
        return `
            <div class="d-flex align-items-center gap-12" style="min-width: 0;">
                <span class="mk-card-icon">
                    <iconify-icon icon="mdi:router-network"></iconify-icon>
                </span>
                <div style="min-width: 0;">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0 text-lg fw-bold text-truncate">${mk.ip}</h6>
                        <span class="mk-card-badge">MikroTik</span>
                    </div>
                    <span class="text-sm text-secondary-light d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="mdi:account-outline"></iconify-icon> ${mk.username}
                    </span>
                </div>
            </div>`;
    }

    function mikrotikStats(mk) {
        const stats = [
            ['online', 'Online', 'mdi:wifi', mk.online, `?page=mikrotik_online_secret&mikrotik_id=${mk.id}`],
            ['offline', 'Offline', 'mdi:wifi-off', mk.offline, `?page=mikrotik_offline&mikrotik_id=${mk.id}`],
            ['disabled', 'Disabled', 'mdi:account-cancel-outline', mk.disabled, `?page=mikrotik_all_secret&mikrotik_id=${mk.id}&status=1`],
        ];
        return `
            <div class="mk-stats">
                ${stats.map(s => `
                <a href="${s[4]}" class="mk-stat mk-stat-${s[0]}">
                    <iconify-icon icon="${s[2]}" class="mk-stat-icon"></iconify-icon>
                    <span>
                        <span class="mk-stat-value">${s[3]}</span>
                        <span class="mk-stat-label">${s[1]}</span>
                    </span>
                </a>`).join('')}
            </div>`;
    }

    function mikrotikRing(mk) {
        return `
            <div class="mk-ring" style="--percent: ${mk.percent};" title="${mk.online} / ${mk.total} users online">
                <div class="mk-ring-inner">
                    <span class="mk-ring-value">${mk.percent}%</span>
                    <span class="mk-ring-label">${mk.online}/${mk.total}</span>
                </div>
            </div>`;
    }

    function mikrotikFailed() {
        return `
            <div class="mk-failed">
                <iconify-icon icon="mdi:lan-disconnect" class="mk-stat-icon"></iconify-icon>
                <span>
                    <span class="d-block fw-semibold">Failed to connect</span>
                    <span class="d-block text-xs">Check IP, port, username and password</span>
                </span>
            </div>`;
    }

    // single = only one Mikrotik on a full-width row: identity, stats and ring side by side
    function mikrotikCard(mk, single) {
        const failed = !mk.connected;
        const stats = failed ? mikrotikFailed() : mikrotikStats(mk);
        const ring = failed ? '' : mikrotikRing(mk);

        const body = single ? `
            <div class="mk-card-single">
                <div class="mk-single-identity">${mikrotikIdentity(mk)}</div>
                <div class="mk-single-stats">${stats}</div>
                ${ring}
            </div>` : `
            <div class="d-flex align-items-center justify-content-between gap-3 mb-16">
                ${mikrotikIdentity(mk)}
                ${ring}
            </div>
            ${stats}`;

        const footerLeft = failed ? `
            <span class="text-sm text-secondary-light d-inline-flex align-items-center gap-1">
                <iconify-icon icon="mdi:refresh"></iconify-icon> Retrying...
            </span>` : `
            <a href="?page=mikrotik_online_secret&mikrotik_id=${mk.id}" class="text-sm fw-semibold text-success-600 d-inline-flex align-items-center gap-1 text-nowrap">
                Online List <iconify-icon icon="mdi:arrow-right"></iconify-icon>
            </a>`;

        return `
            <div class="card h-100 mk-card ${failed ? 'mk-card-failed' : ''}">
                <div class="card-body p-20">${body}</div>
                <div class="card-footer mk-card-footer">
                    ${footerLeft}
                    <a href="javascript:void(0)" class="btn btn-sm btn-success-600 radius-8 px-16 details-btn d-inline-flex align-items-center gap-1 text-nowrap" data-id="${mk.id}">
                        <iconify-icon icon="mdi:eye-outline"></iconify-icon> Details
                    </a>
                </div>
            </div>`;
    }

    // Each card loads and refreshes on its own, so a slow/unreachable
    // Mikrotik never holds back the others
    async function loadMikrotikCard(slot, single) {
        let nextIn = 10000;

        try {
            const res = await fetch(`./pages/mikrotik/mikrotik_status_ajax.php?mkid=${slot.dataset.id}`);
            const mk = await res.json();

            if (!mk) {
                slot.remove();
                return;
            }

            slot.innerHTML = mikrotikCard(mk, single);
            // Unreachable routers take ~15s per attempt, retry them less often
            if (!mk.connected) nextIn = 30000;
        } catch (err) {
            console.error(err);
        }

        setTimeout(() => loadMikrotikCard(slot, single), nextIn);
    }

    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById('mikrotikCards');
        const single = container.dataset.single === '1';
        container.querySelectorAll('.mk-card-slot').forEach(slot => loadMikrotikCard(slot, single));
    });
</script>

<?php $obj->end_script(); ?>