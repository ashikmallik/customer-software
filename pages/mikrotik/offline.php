<?php
$allmikrotik = $obj->getAllData('mikrotik_user');
$middd = !empty($_GET['mikrotik_id']) ? $_GET['mikrotik_id'] : '';
?>

<div class="col-xl-12">
    <div class="card h-100 p-0">
        <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Mikrotik Connection</h6>
        </div>
        <div class="card-body p-24">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <?php foreach ($allmikrotik as $mikrotiks): ?>
                    <div class="col-md-2 mb-2">
                        <span id="m<?php echo $mikrotiks['id']; ?>">
                            <button class="btn btn-primary" type="button" disabled>
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Loading...
                            </button>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3 mb-24">
    <div class="card-body">
        <div class="row gy-3">
            <?php include __DIR__ . '/zone_filter.php'; ?>
        </div>
    </div>
</div>

<?php
$mkInfoCards = [
    ['infoOffline', 'Offline Users', 'mdi:access-point-network-off', 'bg-danger-main'],
    ['infoCustomers', 'Total Users', 'mdi:account-group', 'bg-primary-600'],
    ['infoOnline', 'Online Users', 'mdi:access-point-network', 'bg-success-main'],
    ['infoDisabled', 'Disabled Users', 'mdi:account-cancel', 'bg-warning-main'],
];
include __DIR__ . '/info_cards.php';
?>

<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">Offline Secret List</h5>
    </div>
    <div class="card-body">
        <table class="table bordered-table mb-0" id="secrettable">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Name</th>
                    <th>Password</th>
                    <th>Profile</th>
                    <th>Service</th>
                    <th>Disable Status</th>
                    <th>Last Logout</th>
                </tr>
            </thead>
            <tbody id="secret_body"></tbody>
        </table>
    </div>
</div>

<?php $obj->start_script(); ?>

<script>
    let sectetShow = true;
    let defaultMikrotikId = "<?php echo $middd; ?>";

    function mkConnectCheck(id, mkip) {
        $.ajax({
            type: "GET",
            url: "./pages/mikrotik/connect_ajax.php",
            data: { mkid: id },
            dataType: "JSON",
            success: function (response) {

                if (response.connection) {

                    let btnClass = (defaultMikrotikId == id) ? 'btn-warning' : 'btn-success';

                    $('#m' + id).html(`
                        <button class="btn ${btnClass}" onclick="mkAllSecret(${id})" type="button">
                            ${mkip} ${response.status}
                        </button>
                    `);

        
                    if (defaultMikrotikId && defaultMikrotikId == id) {
                        mkAllSecret(id);
                    }

                    
                    if (!defaultMikrotikId && sectetShow) {
                        mkAllSecret(id);
                        sectetShow = false;
                    }

                } else {
                    $('#m' + id).html(`
                        <button class="btn btn-danger" disabled type="button">
                            ${mkip} ${response.status}
                        </button>
                    `);
                }
            }
        });
    }

    let currentMikrotikId = null;

    function mkAllSecret(id) {
        currentMikrotikId = id;
        $.ajax({
            type: "GET",
            url: "./pages/mikrotik/connect_ajax.php",
            data: { mkidsecretofline: id, ...mkZoneFilterParams() },
            dataType: "JSON",
            success: function (response) {

                if ($.fn.DataTable.isDataTable('#secrettable')) {
                    $('#secrettable').DataTable().destroy();
                }

                $('#secret_body').html('');

                if (response.connection) {
                    $('#secret_body').html(response.status);
                    $('#infoOffline').text(response.totalOffline);
                    $('#infoCustomers').text(response.totalCustomers);
                    $('#infoOnline').text(response.totalOnline);
                    $('#infoDisabled').text(response.totalDisabled);
                }

                $('#secrettable').DataTable({
                    pageLength: 100,
                    lengthMenu: [[10, 25, 100, 500], [10, 25, 100, 500]],
                    responsive: true
                });
            }
        });
    }

    $(document).on('mkZoneFilterChange', function () {
        if (currentMikrotikId) mkAllSecret(currentMikrotikId);
    });
</script>

<?php foreach ($allmikrotik as $mikrotiks): ?>
    <script>
        mkConnectCheck(
            "<?php echo $mikrotiks['id']; ?>",
            "<?php echo $mikrotiks['mik_ip']; ?>"
        );
    </script>
<?php endforeach; ?>

<?php $obj->end_script(); ?>
