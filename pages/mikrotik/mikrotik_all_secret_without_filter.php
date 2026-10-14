<?php
$allmikrotik = $obj->getAllData('mikrotik_user');
// Coming from Mikrotik List: preselect that Mikrotik and load only its data
$selectedMikrotikId = intval($_GET['mikrotik_id'] ?? 0); ?>
<div class="col-xl-12">
    <div class="card h-100 p-0">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="text-lg fw-semibold mb-0">Mikrotik Connection</h6>
            <div class="d-flex align-items-center gap-2">
                <select id="mikrotik-filter" class="form-control form-select" style="min-width: 200px;">
                    <option value="">All Mikrotik</option>
                    <?php foreach ($allmikrotik as $mikrotiks): ?>
                        <option value="<?php echo $mikrotiks['id']; ?>" <?php echo ($mikrotiks['id'] == $selectedMikrotikId) ? 'selected' : ''; ?>><?php echo htmlspecialchars($mikrotiks['mik_ip']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-primary" id="refresh-btn"><i class="ri-refresh-line"></i> Refresh</button>
            </div>
        </div>
        <div class="card-body p-24">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <?php foreach ($allmikrotik as $mikrotiks): ?>
                    <span id="m<?php echo $mikrotiks['id']; ?>"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4 mt-1 mb-24">
    <?php
    $summaryCards = [
        ['key' => '',            'title' => 'Total User',          'color' => 'bg-primary-600', 'icon' => 'mdi:account-group'],
        ['key' => 'online',      'title' => 'Online',              'color' => 'bg-success-main', 'icon' => 'mdi:access-point-network'],
        ['key' => 'offline',     'title' => 'Offline',             'color' => 'bg-danger-main',  'icon' => 'mdi:access-point-network-off'],
        ['key' => 'enable',      'title' => 'Enable',              'color' => 'bg-info-main',    'icon' => 'mdi:account-check'],
        ['key' => 'disable',     'title' => 'Disable',             'color' => 'bg-warning-main', 'icon' => 'mdi:account-cancel'],
        ['key' => 'enable_offline', 'title' => 'Enable but Offline', 'color' => 'bg-secondary',  'icon' => 'mdi:account-alert'],
    ];
    foreach ($summaryCards as $card): ?>
        <div class="col-xxl-2 col-md-4 col-sm-6">
            <div class="card summary-card px-16 py-12 shadow-none radius-8 border h-100" data-filter="<?php echo $card['key']; ?>" style="cursor: pointer;">
                <div class="card-body p-0 d-flex align-items-center justify-content-between">
                    <span class="w-40-px h-40-px <?php echo $card['color']; ?> flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="<?php echo $card['icon']; ?>" class="text-white text-2xl"></iconify-icon>
                    </span>
                    <div class="text-end">
                        <h6 class="fw-semibold my-1" id="count-<?php echo $card['key'] ?: 'total'; ?>">0</h6>
                        <p class="text-sm text-primary-light fw-medium mb-0"><?php echo $card['title']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">Mikrotik All Users <span class="text-sm text-secondary-light" id="filter-label"></span></h5>
    </div>
    <div class="card-body">
        <table class="table bordered-table mb-0" id="usertable" style="width:100%">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Mikrotik</th>
                    <th>Username</th>
                    <th>Profile</th>
                    <th>Service</th>
                    <th>Online</th>
                    <th>Status</th>
                    <th>IP Address</th>
                    <th>Uptime</th>
                    <th>MAC</th>
                    <th>Last Log out</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<style>
    .summary-card.active-filter {
        border: 2px solid var(--primary-600, #487fff) !important;
    }
</style>

<?php $obj->start_script(); ?>
<script>
    const mikrotikList = <?php echo json_encode(array_map(function ($m) {
                                return ['id' => $m['id'], 'ip' => $m['mik_ip']];
                            }, $allmikrotik)); ?>;

    // users per mikrotik id
    let usersByMikrotik = {};
    let statusFilter = '';
    let userTable = null;

    const filterLabels = {
        '': '',
        'online': '(Online)',
        'offline': '(Offline)',
        'enable': '(Enable)',
        'disable': '(Disable)',
        'enable_offline': '(Enable but Offline)'
    };

    function esc(str) {
        return $('<div>').text(str ?? '').html();
    }

    function matchStatus(user, filter) {
        switch (filter) {
            case 'online':
                return user.online;
            case 'offline':
                return !user.online;
            case 'enable':
                return !user.disabled;
            case 'disable':
                return user.disabled;
            case 'enable_offline':
                return !user.disabled && !user.online;
            default:
                return true;
        }
    }

    function selectedUsers() {
        const mkId = $('#mikrotik-filter').val();
        if (mkId) {
            return usersByMikrotik[mkId] || [];
        }
        return Object.values(usersByMikrotik).flat();
    }

    function renderSummary() {
        const users = selectedUsers();
        const counts = {
            total: users.length,
            online: 0,
            offline: 0,
            enable: 0,
            disable: 0,
            enable_offline: 0
        };
        users.forEach(function(u) {
            u.online ? counts.online++ : counts.offline++;
            u.disabled ? counts.disable++ : counts.enable++;
            if (!u.disabled && !u.online) counts.enable_offline++;
        });
        Object.keys(counts).forEach(function(key) {
            $('#count-' + key).text(counts[key].toLocaleString());
        });

        $('.summary-card').removeClass('active-filter');
        $('.summary-card[data-filter="' + statusFilter + '"]').addClass('active-filter');
        $('#filter-label').text(filterLabels[statusFilter]);

        const rows = users.filter(function(u) {
            return matchStatus(u, statusFilter);
        }).map(function(u, i) {
            return [
                i + 1,
                esc(u.mk_ip),
                esc(u.name),
                esc(u.profile),
                esc(u.service),
                u.online ? '<span class="badge bg-success">Online</span>' : '<span class="badge bg-danger">Offline</span>',
                u.disabled ? '<span class="badge bg-warning">Disable</span>' : '<span class="badge bg-info">Enable</span>',
                esc(u.address),
                esc(u.uptime),
                esc(u.caller_id),
                esc(u.last_logged_out),
                esc(u.comment)
            ];
        });

        userTable.clear().rows.add(rows).draw();
    }

    // Mikrotik ids with a request in flight
    const loadingMikrotik = {};

    function loadMikrotik(mk) {
        loadingMikrotik[mk.id] = true;
        $('#m' + mk.id).html(`<button class="btn btn-primary" type="button" disabled><span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ${esc(mk.ip)} Loading...</button>`);

        $.ajax({
            type: "GET",
            url: "./pages/mikrotik/mikrotik_all_secret_without_filter_ajax.php",
            data: {
                'mkid': mk.id
            },
            dataType: "JSON",
            success: function(response) {
                if (response.connection) {
                    usersByMikrotik[mk.id] = response.users.map(function(u) {
                        u.mk_ip = mk.ip;
                        return u;
                    });
                    $('#m' + mk.id).html(`<button class="btn btn-success" type="button" disabled>${esc(mk.ip)} (${response.users.length} users)</button>`);
                } else {
                    delete usersByMikrotik[mk.id];
                    $('#m' + mk.id).html(`<button class="btn btn-danger" type="button" disabled>${esc(mk.ip)} ${esc(response.message)}</button>`);
                }
                renderSummary();
            },
            error: function() {
                delete usersByMikrotik[mk.id];
                $('#m' + mk.id).html(`<button class="btn btn-danger" type="button" disabled>${esc(mk.ip)} Failed to load</button>`);
                renderSummary();
            },
            complete: function() {
                delete loadingMikrotik[mk.id];
            }
        });
    }

    // Mikrotik(s) in the current dropdown selection: one, or all for "All Mikrotik"
    function selectedMikrotikList() {
        const mkId = $('#mikrotik-filter').val();
        return mkId ? mikrotikList.filter(mk => String(mk.id) === mkId) : mikrotikList;
    }

    // Load the selected Mikrotik(s) that are not loaded yet
    function loadSelected() {
        selectedMikrotikList().forEach(function(mk) {
            if (!usersByMikrotik[mk.id] && !loadingMikrotik[mk.id]) {
                loadMikrotik(mk);
            }
        });
        renderSummary();
    }

    // Refresh: reload only the selected Mikrotik(s)
    function loadAll() {
        selectedMikrotikList().forEach(function(mk) {
            delete usersByMikrotik[mk.id];
            if (!loadingMikrotik[mk.id]) {
                loadMikrotik(mk);
            }
        });
        renderSummary();
    }

    $(document).ready(function() {
        userTable = $('#usertable').DataTable({
            pageLength: 100,
            lengthMenu: [
                [10, 25, 100, 500, -1],
                [10, 25, 100, 500, 'All']
            ],
            deferRender: true,
            responsive: true,
            dom: 'Blfrtip',
            buttons: [{
                extend: 'excelHtml5',
                title: 'Mikrotik All Users'
            }]
        });

        $('.summary-card').on('click', function() {
            statusFilter = $(this).data('filter') || '';
            renderSummary();
        });

        $('#mikrotik-filter').on('change', loadSelected);
        $('#refresh-btn').on('click', loadAll);

        loadSelected();
    });
</script>
<?php $obj->end_script(); ?>