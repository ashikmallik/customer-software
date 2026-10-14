<?php
session_start();
require('../../services/Model.php');
$obj = new Model();

$userId = $_SESSION['userid'] ?? null;
$ty     = $_SESSION['usertype'] ?? null;

$where = " WHERE deleted_at IS NULL ";


if ($ty === 'EO' && $userId) {
    $where .= " AND vw_agent.billing_person_id = '$userId'";
}

if (!empty($_POST['ag_status_filter'])) {
    $statusMap = [
        'active'      => 1,
        'inactive'    => 0,
        'free'        => 2,
        'discontinue' => 3
    ];

    $filter = strtolower(trim($_POST['ag_status_filter']));
    if (array_key_exists($filter, $statusMap)) {
        $where .= " AND vw_agent.ag_status = '" . (int)$statusMap[$filter] . "'";
    }
}

if (!empty($_POST['zone'])) {
    $where .= " AND zone = " . (int)$_POST['zone'];
}
if (!empty($_POST['sub_zone'])) {
    $where .= " AND sub_zone = " . (int)$_POST['sub_zone'];
}
if (!empty($_POST['billing_person'])) $where .= " AND billing_person_id = " . (int)$_POST['billing_person'];
if (!empty($_POST['package']))        $where .= " AND mb = '" . addslashes($_POST['package']) . "'";
if (isset($_POST['status']) && $_POST['status'] !== '') $where .= " AND ag_status = " . (int)$_POST['status'];
if (!empty($_POST['date_from']))      $where .= " AND connection_date >= '" . addslashes($_POST['date_from']) . "'";
if (!empty($_POST['date_to']))        $where .= " AND connection_date <= '" . addslashes($_POST['date_to']) . "'";

if (!empty($_POST['search'])) {
    $search = addslashes($_POST['search']);
    $where .= " AND (
        ag_name LIKE '%$search%' OR
        cus_id LIKE '%$search%' OR
        ag_office_address LIKE '%$search%' OR
        ip LIKE '%$search%' OR
        ag_mobile_no LIKE '%$search%'
    )";
}

$per_page = $_POST['per_page'] ?? 20;

$countSql    = "SELECT COUNT(*) as total FROM vw_agent $where";
$countResult = $obj->rawSql($countSql);
$total_records = (int)($countResult[0]['total'] ?? 0);

if ($per_page == 'all') {
    $limit = $total_records;
} else {
    $limit = (int)$per_page;
    if ($limit <= 0) $limit = 10;
}


if ($limit > 100) $limit = 100;

$page  = isset($_POST['pg']) ? (int)$_POST['pg'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;
$total_pages = ($limit > 0) ? (int)ceil($total_records / $limit) : 1;


$sql = "
    SELECT vw_agent.*, tbl_zone.zone_name, _createuser.FullName
    FROM vw_agent
    LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id
    LEFT JOIN _createuser ON vw_agent.billing_person_id = _createuser.UserId
    $where
    ORDER BY ag_id DESC
    LIMIT $limit OFFSET $offset
";
$allData = $obj->rawSql($sql);


$uniqueMids = [];
foreach ($allData as $row) {
    $mid = (int)($row['mikrotik_id'] ?? 0);
    if ($mid > 0) $uniqueMids[$mid] = true;
}

// mid => onlineSet[name]=true
$onlineSetByMid = [];
$mkDead = [];

foreach (array_keys($uniqueMids) as $mid) {
    $onlineSetByMid[$mid] = [];

    try {
        $actives = $obj->pppeoActiveSecretList($mid);

        if (!$actives || !is_array($actives)) {
            // router down / no active
            continue;
        }

        foreach ($actives as $a) {
            $name = trim((string)($a['name'] ?? ''));
            if ($name !== '') $onlineSetByMid[$mid][$name] = true;
        }

    } catch (Throwable $e) {
        $mkDead[$mid] = true;
        $onlineSetByMid[$mid] = [];
    }
}

foreach ($allData as $i => $customer) {

    $mid = (int)($customer['mikrotik_id'] ?? 0);
    $key = trim((string)($customer['ip'] ?? '')); 

    // default offline
    $statusText = "<span title='Offline' style='height:12px;width:12px;background:red;border-radius:50%;display:inline-block;'></span>";

    if ($mid > 0 && $key !== '' && empty($mkDead[$mid])) {
        if (isset($onlineSetByMid[$mid][$key])) {
            $statusText = "<span title='Online' style='height:12px;width:12px;background:green;border-radius:50%;display:inline-block;'></span>";
        }
    }

    echo '<tr>';
    echo '<td>'.($offset + $i + 1).'</td>';
    echo '<td>'.$statusText.'</td>';

    echo '<td>';
    if ($obj->userWorkPermission('delete')){
        echo '<a href="?page=cus_info&delete-token='.$customer['ag_id'].'" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-danger">Delete</a> ';
    }
    if ($obj->userWorkPermission('edit')){
        echo '<a href="?page=customer_edit&token='.$customer['ag_id'].'" class="btn btn-sm btn-primary">Edit</a> ';
    }
    echo '</td>';

    echo '<td><a href="?page=customer_ledger&token='.$customer['ag_id'].'" class="btn btn-outline-success waves-effect waves-light btn-sm">'.htmlspecialchars($customer['cus_id']).'</a></td>';
    echo '<td>'.htmlspecialchars($customer['ip']).'</td>';
    echo '<td>'.htmlspecialchars($customer['ag_name']).'</td>';
    echo '<td>'.htmlspecialchars($customer['ag_office_address']).'</td>';
    echo '<td>'.htmlspecialchars($customer['ag_mobile_no']).'</td>';
    echo '<td>'.htmlspecialchars($customer['mb']).'</td>';
    echo '<td>'.number_format((float)$customer['taka'],2).'</td>';
    echo '<td>'.(!empty($customer['connection_date']) ? date("d-M-Y", strtotime($customer['connection_date'])) : '').'</td>';
    echo '<td>'.htmlspecialchars($customer['zone_name']).'</td>';
    echo '<td>'.htmlspecialchars($customer['FullName']).'</td>';

    echo '<td>';
    if($customer['ag_status']==1) echo '<span class="badge bg-success">Active</span>';
    elseif($customer['ag_status']==0) echo '<span class="badge bg-danger">Inactive</span>';
    elseif($customer['ag_status']==2) echo '<span class="badge bg-info">Free</span>';
    elseif($customer['ag_status']==3) echo '<span class="badge bg-warning">Discontinue</span>';
    echo '</td>';

    echo '</tr>';
}


$showing_from = ($total_records > 0) ? ($offset + 1) : 0;
$showing_to   = ($offset + count($allData));

echo '<tr><td colspan="13">';
echo '<div class="justify-content-between align-items-center">';

echo '<div class="text-muted">';
echo "Showing {$showing_from} to {$showing_to} of {$total_records} entries";
echo '</div>';

echo '<nav><ul class="pagination mb-0" style="margin-top: 8px;">';

if ($page > 1) echo '<li class="page-item"><a class="page-link" href="#" onclick="loadData('.($page-1).')">Previous</a></li>';
else echo '<li class="page-item disabled"><span class="page-link">Previous</span></li>';

echo '<li class="page-item '.(($page==1)?'active':'').'"><a class="page-link" href="#" onclick="loadData(1)">1</a></li>';

$adjacents = 2;
if ($page > ($adjacents+2)) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';

$start = max(2, $page - $adjacents);
$end   = min($total_pages - 1, $page + $adjacents);
for ($p = $start; $p <= $end; $p++) {
    echo '<li class="page-item '.(($p==$page)?'active':'').'"><a class="page-link" href="#" onclick="loadData('.$p.')">'.$p.'</a></li>';
}

if ($page < ($total_pages - ($adjacents+1))) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';

if ($total_pages > 1) {
    echo '<li class="page-item '.(($page==$total_pages)?'active':'').'"><a class="page-link" href="#" onclick="loadData('.$total_pages.')">'.$total_pages.'</a></li>';
}

if ($page < $total_pages) echo '<li class="page-item"><a class="page-link" href="#" onclick="loadData('.($page+1).')">Next</a></li>';
else echo '<li class="page-item disabled"><span class="page-link">Next</span></li>';

echo '</ul></nav>';
echo '</div>';
echo '</td></tr>';
?>
