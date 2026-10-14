<?php
session_start();

require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

session_write_close();

// Zone / sub-zone filter sent by the secret pages (0 = all)
$zoneFilter = intval($_GET['zone'] ?? 0);
$subZoneFilter = intval($_GET['sub_zone'] ?? 0);

function agentInZoneFilter($agent)
{
    global $zoneFilter, $subZoneFilter;
    if ($zoneFilter && intval($agent['zone'] ?? 0) !== $zoneFilter) return false;
    if ($subZoneFilter && intval($agent['sub_zone'] ?? 0) !== $subZoneFilter) return false;
    return true;
}

// Check if 'mkid' is provided in the GET request
$secretName = $_GET['name'] ?? '';
$enableDisableState = $_GET['state'] ?? '';
$selectedMkTik = intval($_GET['selectedMktik'] ?? 0);
$mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = ' . $selectedMkTik);


$mikrotikId = $mikrotikLoginData['id'] ?? null;
if ($enableDisableState == 'Enable') {

    $obj->disableSingleSecret($mikrotikId, $secretName);
    echo '0 sadi' . $selectedMkTik;
        // Activity log start
    $mk = $obj->getSingleData('mikrotik_user', ['where' => [['id', '=', $mikrotikId]]]);
    
    $obj->createActivityLog($_SESSION['userid'], '3', '2', 'mikrotik_user', $mikrotikId, null, null, 'Disabled secret ' . $secretName .' IP:'. $mk["mik_ip"] , null, null, $mk["mik_ip"], false, null, $mikrotikId);
    // Activity log start

} else if ($enableDisableState == 'Disable') {
    
    $obj->enableSingleSecret($mikrotikId, $secretName);
    echo '1';
     // Activity log start
    $mk = $obj->getSingleData('mikrotik_user', ['where' => [['id', '=', $mikrotikId]]]);
    
    $obj->createActivityLog($_SESSION['userid'], '3', '2', 'mikrotik_user', $mikrotikId, null, null, 'Enabled secret ' . $secretName .' IP:'. $mk["mik_ip"] , null, null, $mk["mik_ip"], false, null, $mikrotikId);
     // Activity log End
}

//disconnect by due user menual start -- ashik
if(isset($_POST['action']) && isset($_POST['mikrotikId'])){
                $disconnectCount = 0;
                $agWhereCon = '';
                $mikId = intval($_POST['mikrotikId']);
                $zone = $_POST['zoneId'];
                $action = $_POST['action'];
                
                if (isset($_POST['dis_from']) && isset($_POST['dis_to']) && !empty($_POST['dis_from']) && !empty($_POST['dis_to'])) {
                    $dateFrom = date('d', strtotime($_POST['dis_from']));
                    $dateTo = date('d', strtotime($_POST['dis_to']));
                    // $agWhereCon .= " AND ((vw_agent.mikrotik_disconnect >= $dateTo) AND (vw_agent.mikrotik_disconnect <= $dateFrom))";
                    $agWhereCon .= " AND vw_agent.mikrotik_disconnect BETWEEN $dateFrom AND $dateTo";
                }
                if ($_POST['zoneId'] > 0 && !empty($_POST['zoneId'])) {
                    $agWhereCon = "AND vw_agent.zone = $zone";
                }
                $singleMikrotikAgent = $obj->rawSql("SELECT 
                                                    vw_agent.*,
                                                        DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.bill_date, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS bill_date,
                                                        DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.mikrotik_disconnect, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS mikrotik_disconnect,
                                                        customer_billing.dueadvance,
                                                        tbl_zone.zone_name,
                                                        _createuser.FullName 
                                                    FROM vw_agent 
                                                    left join customer_billing on customer_billing.agid = vw_agent.ag_id 
                                                    left join _createuser ON vw_agent.billing_person_id = _createuser.UserId 
                                                    left join tbl_zone on tbl_zone.zone_id = vw_agent.zone 
                                                    WHERE vw_agent.deleted_at is NULL
                                                        AND vw_agent.mikrotik_id =$mikId
                                                        AND customer_billing.dueadvance > 0 
                                                        AND vw_agent.ag_status = 1
                                                        AND vw_agent.bill_status = '0'
                                                        $agWhereCon
                                                        ");
                        $result = false;
                
                        foreach ($singleMikrotikAgent as $agent) {
                            
                            if($action == 'disconnect'){
                                    $result = $obj->disableSingleSecret($mikId, $agent['ip']);
                                }
                                if($action == 'reconnect'){
                                    $result = $obj->enableSingleSecret($mikId, $agent['ip']);
                                }
                            $disconnectCount++;
                        }       
                        
                    
                
                // elseif($action == 'predisconnect'){
                //     $result = $obj->preDisconnectUsers($mikId);
                // }
            
                if($result){
                    echo json_encode(['success'=>true],$disconnectCount);
                } else {
                    echo json_encode(['success'=>false, 'message'=>'Action failed']);
                }
                exit;
            }
//disconnect by due user menual end -- ashik


if (isset($_GET['mkid'])) {
    $mikrotik_id = $_GET['mkid'];
    $mikrotikConnection =  $obj->checkConnection($mikrotik_id);
    if ($mikrotikConnection) {
        $response = [
            'status' => 'Connected',
            'connection' => true,
        ];
    } else {
        $updateResult = $obj->updateData('mikrotik_user', ['status' => 0], ['id' => $mikrotik_id]);
        $response = [
            'status' => 'Failed to connect',
            'connection' => false,
        ];
    }
} elseif (isset($_GET['mkidsecretall'])) {

    $mikrotikConnection =  $obj->viewAllPppSecret($_GET['mkidsecretall']);
    if ($mikrotikConnection) {
        $singleMikrotikAgent = $obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $_GET['mkidsecretall']]]);
        $agentList = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                // Secrets of customers outside the zone filter are skipped below
                if (!agentInZoneFilter($agent)) {
                    continue;
                }
                $agentList[$agent['ip']] = [
                    'ip' => $agent['ip'],
                    'id' => $agent['ag_id'],
                    'cus_id' => $agent['cus_id']
                    ];
            }
        }

        $totalEnableSecret = 0;
        $totalDisableSecret = 0;

        $table = '';
        $sl = 1;
        foreach ($mikrotikConnection as $secret) {
            if (@$agentList[$secret['name']]['ip'] != $secret['name']) {
                continue;
            }

            if ($secret['disabled'] == 'true') {
                $totalDisableSecret++;
            } elseif ($secret['disabled'] == 'false') {
                $totalEnableSecret++;
            }


            if (isset($_GET['secretStatus']) && $_GET['secretStatus'] == '0' && $secret['disabled'] == 'true') {
                continue;
            } elseif (isset($_GET['secretStatus']) && $_GET['secretStatus'] == '1' && $secret['disabled'] == 'false') {
                continue;
            }

            $lastLogout = (isset($secret['last-logged-out']) && ($secret['last-logged-out'] != 'jan/01/1970 00:00:00')) ? ucfirst($secret['last-logged-out']) : '';
            
             $ledger = '<a class="btn btn-info waves-effect waves-light btn-sm" 
                        href="?page=customer_ledger&token=' . $agentList[$secret['name']]['id'] . '">
                        ' . $agentList[$secret['name']]['cus_id'] . '
                      </a>';
                      
            
        
                $statusbtn = ($secret['disabled'] == 'false') ? '<button id="" data-status="1" data-name="' . $secret['name'] . '" class="secretCangeStatus btn btn-xs btn-success">Enable</button>' : '<button id=""  data-status="0" data-name="' . $secret['name'] . '" class="secretCangeStatus btn btn-xs btn-danger">Disable</button>';
           

                // Comment: short text in the table, full text in a modal via "See more"
                $comment = trim($secret['comment'] ?? '');
                $commentLimit = 30;
                if (mb_strlen($comment) > $commentLimit) {
                    $commentCell = htmlspecialchars(mb_substr($comment, 0, $commentLimit)) . '... '
                        . '<a href="javascript:void(0)" class="secret-comment-more text-primary"'
                        . ' data-name="' . htmlspecialchars($secret['name']) . '"'
                        . ' data-comment="' . htmlspecialchars($comment) . '">See more</a>';
                } else {
                    $commentCell = htmlspecialchars($comment);
                }
          
                $deletebtn = '<form method="post" onsubmit="return confirm(&quot;Are you sure you want to delete?&quot;)">
                    <input type="hidden" name="delete_id" value="' . $secret["name"] . '">
                    <button type="submit" name="delete_static_user" class="btn btn-sm btn-danger">Delete</button>
                </form>';
            


            $table .= '<tr>
            <td>' . $sl++ . '</td>
            <td>' . $ledger . '</td>
            <td>' . $secret['name'] . '</td>
            <td>' . $secret['password'] . '</td>
            <td>' . $secret['profile'] . '</td>
            <td>' . $lastLogout . '</td>
            <td>' . $commentCell . '</td>';
            
            if ($obj->userWorkPermission('edit')) {
                $table .= '<td>' . $statusbtn . '</td>';
            }
            
            if ($obj->userWorkPermission('delete')) {
                $table .= '<td>' . $deletebtn . '</td>';
            }
            
            $table .= '</tr>';

        }

        $response = [
            'status' =>  $table,
            'connection' => true,
            'totalEnableSecret' => $totalEnableSecret,
            'totalDisableSecret' => $totalDisableSecret,
            'selectedMkTik' => $selectedMkTik
        ];
    } else {
        $response = [
            'status' =>  'No data Found',
            'connection' => false,
        ];
    }
} 
elseif (isset($_GET['mkidduedue'])) {
    
    $agWhereCon = '';
    $iddd = $_GET['mkidduedue'];
    $zone = $_GET['zone'];
    $mikrotikConnection =  $obj->viewAllPppSecret($_GET['mkidduedue']);
    
    if (isset($_GET['dis_from']) && isset($_GET['dis_to']) && !empty($_GET['dis_from']) && !empty($_GET['dis_to'])) {
        $dateFrom = date('d', strtotime($_GET['dis_from']));
        $dateTo = date('d', strtotime($_GET['dis_to']));
        // $agWhereCon .= " AND ((vw_agent.mikrotik_disconnect >= $dateTo) AND (vw_agent.mikrotik_disconnect <= $dateFrom))";
        $agWhereCon .= " AND vw_agent.mikrotik_disconnect BETWEEN $dateFrom AND $dateTo";
    }
    if ($_GET['zone'] > 0 && !empty($_GET['zone'])) {
        $agWhereCon = "AND vw_agent.zone = $zone";
    }
    
    if ($mikrotikConnection) {
        // $singleMikrotikAgent = $obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $_GET['mkidsecretall']]]);
       $singleMikrotikAgent = $obj->rawSql(
                                            "SELECT 
                                                vw_agent.*,
                                                DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.bill_date, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS bill_date,
                                                DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.mikrotik_disconnect, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS mikrotik_disconnect,
                                                customer_billing.dueadvance
                                            FROM vw_agent 
                                            left join customer_billing on customer_billing.agid = vw_agent.ag_id
                                            WHERE vw_agent.deleted_at is NULL
                                                AND vw_agent.mikrotik_id =$iddd
                                                AND customer_billing.dueadvance > 0 
                                                AND vw_agent.ag_status = 1
                                                AND vw_agent.bill_status = '0'
                                                $agWhereCon
                                                ");
        
        
        $agentList = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                $agentList[$agent['ip']] = $agent['ip'];
            }
        }
        
        $totalEnableSecret = 0;
        $totalDisableSecret = 0;

        $table = '';
        $sl = 1;
        foreach ($mikrotikConnection as $secret) {
            if (@$agentList[$secret['name']] != $secret['name']) {
                continue;
            }

            if ($secret['disabled'] == 'true') {
                $totalDisableSecret++;
            } elseif ($secret['disabled'] == 'false') {
                $totalEnableSecret++;
            }


            if (isset($_GET['secretStatus']) && $_GET['secretStatus'] == '0' && $secret['disabled'] == 'true') {
                continue;
            } elseif (isset($_GET['secretStatus']) && $_GET['secretStatus'] == '1' && $secret['disabled'] == 'false') {
                continue;
            }

            $lastLogout = (isset($secret['last-logged-out']) && ($secret['last-logged-out'] != 'jan/01/1970 00:00:00')) ? ucfirst($secret['last-logged-out']) : '';
            $statusbtn = ($secret['disabled'] == 'false') ? '<button id="" data-status="1" data-name="' . $secret['name'] . '" class="secretCangeStatus btn btn-xs btn-success">Enable</button>' : '<button id=""  data-status="0" data-name="' . $secret['name'] . '" class="secretCangeStatus btn btn-xs btn-danger">Disable</button>';
       
            $table .= '
            <tr>
            <td> ' . $sl++ . '</td>
            <td> ' . $secret['name'] . '</td>
            <td> ' . $secret['profile'] . '</td>
        
            <td> ' . $lastLogout . '</td>
            <td> ' . $statusbtn . '</td>
            
            </tr>';
        }

        $response = [
            'status' =>  $table,
            'connection' => true,
            'totalEnableSecret' => $totalEnableSecret,
            'totalDisableSecret' => $totalDisableSecret,
            'selectedMkTik' => $selectedMkTik
        ];
    } else {
        $response = [
            'status' =>  'No data Found',
            'connection' => false,
        ];
    }
}
elseif (isset($_GET['mikrotikStatikUser'])) {
    $totalInactive = 0;
    $totalActive = 0;

    $mikrotikConnection = null;
    $mk = $obj->getSingleData('mikrotik_user', ['where' => [['id', '=', $_GET['mikrotikStatikUser']], ['status', '=', '1']]]);

    if ($mk) {
        $statikMikrotik = new Mikrotik($mk["mik_ip"], $mk["mik_port"], $mk["mik_username"], $mk["mik_password"]);

        if (!empty($statikMikrotik) && $statikMikrotik->connected) {
            // Simple Queue list fetch
            $mikrotikConnection = $statikMikrotik->comm("/queue/simple/print");
        }
    }


    if ($mikrotikConnection) {
        $singleMikrotikAgent = $obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $_GET['mikrotikStatikUser']]]);
        $agentList = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                if (!agentInZoneFilter($agent)) {
                    continue;
                }
                $agentList[$agent['ip']] = [
                    "ip" => $agent['ip'],
                    "name" => $agent['ag_name'],
                    "agent_id" => $agent['ag_id'],
                ];
            }
        }

        $table = '';
        $sl = 1;
        foreach ($mikrotikConnection as $secret) {
            if (!empty($secret["target"])) {
                if (@$agentList[explode("/", $secret['target'])[0]]["ip"] != explode("/", $secret['target'])[0]) {
                    continue;
                }

                if ($secret["disabled"] == "true") {
                    $totalInactive++;
                } else {
                    $totalActive++;
                }

                $lastLogout = (isset($secret['last-logged-out']) && ($secret['last-logged-out'] != 'jan/01/1970 00:00:00')) ? ucfirst($secret['last-logged-out']) : '';
                $statusbtn = ($secret['disabled'] == 'false') ?
                    '<button data-status="1" data-name="' . $secret['name'] . '" class="btn btn-xs btn-success changeStatus">Enable</button>' :
                    '<button  data-status="0" data-name="' . $secret['name'] . '" class="btn btn-xs btn-danger changeStatus">Disable</button>';

                $table .= '
                <tr>
                    <td>' . $sl++ . '</td>
                    <td>
                        <a class="btn btn-primary waves-effect waves-light btn-sm" href="?page=customer_ledger&token=' . $agentList[explode("/", $secret['target'])[0]]["agent_id"] . '">
                            ' . $agentList[explode("/", $secret['target'])[0]]["name"] . '
                        </a>
                    </td>
                    <td>' . $secret['target'] . '</td>
                    <td>' . $secret['name'] . '</td>
                    <td>' . $secret['rate'] . '</td>
                    <td>' . number_format((array_sum(explode("/", $secret["bytes"])) / 1024 / 1024 / 1024), 2) . " GB" . '</td>
                    <td>' . $statusbtn . '</td>
                    <td>
                        <form method="post" onsubmit="return confirm(&quot;Are you sure you want to delete?&quot;)">
                            <input type="hidden" name="delete_id" value="' . $secret[".id"] . '">
                            <button type="submit" name="delete_static_user" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>

                </tr>';
            }
        }

        $response = [
            'status' =>  $table,
            'connection' => true,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
        ];
    } else {
        $response = [
            'status' =>  'No data Found',
            'connection' => false,
        ];
    }
} elseif (isset($_GET['mkidsecretonline'])) {

function formatBytes($bytes)
{
    $bytes = (float) $bytes;
    if ($bytes <= 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = min((int) floor(log($bytes, 1024)), count($units) - 1);
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}
    $mkid = (int)$_GET['mkidsecretonline'];

    $mikrotikConnection   = $obj->pppeoActiveSecretList($mkid);
    $allRunnigSecretData  = $obj->interfaceStatus($mkid);
    $table = '';

    if ($mikrotikConnection) {

        // interface list theke PPPoE usage map
        $usageMap = [];
        if (!empty($allRunnigSecretData)) {
            foreach ($allRunnigSecretData as $iface) {
                $ifName = $iface['name'] ?? '';
                // sudhu <pppoe-xxx> interface
                if (strpos($ifName, '<pppoe-') === 0) {
                    $user = substr($ifName, 7, -1); // "<pppoe-" ar ">" bad
                    $usageMap[$user] = [
                        'upload'   => $iface['rx-byte'] ?? 0,
                        'download' => $iface['tx-byte'] ?? 0,
                    ];
                }
            }
        }

        $singleMikrotikAgent = $obj->getAllData('tbl_agent', [
            'where' => ['mikrotik_id', '=', $mkid]
        ]);

        $agentMap = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                if (!empty($agent['ip']) && agentInZoneFilter($agent)) {
                    $agentMap[$agent['ip']] = $agent['ag_id'];
                }
            }
        }

        $sl = 1;
        $totalOnline = 0;
        $totalUpload = 0;
        $totalDownload = 0;
        foreach ($mikrotikConnection as $secret) {

            $name = $secret['name'] ?? '';
            if ($name === '' || !isset($agentMap[$name])) {
                continue;
            }

            $ag_id  = $agentMap[$name];
            $status = (($secret['radius'] ?? '') == 'false') ? 'Running' : 'Stop';

            $upload   = isset($usageMap[$name]) ? formatBytes($usageMap[$name]['upload'])   : '-';
            $download = isset($usageMap[$name]) ? formatBytes($usageMap[$name]['download']) : '-';

            $totalOnline++;
            $totalUpload   += (float) ($usageMap[$name]['upload'] ?? 0);
            $totalDownload += (float) ($usageMap[$name]['download'] ?? 0);

            $viewbtn = "<a href='?page=customer_online_info&token=" . (int)$ag_id . "' class='btn btn-success'>view</a>";

            $table .= '
            <tr>
                <td>' . $sl++ . '</td>
                <td>' . htmlspecialchars($name) . '</td>
                <td>' . htmlspecialchars($secret['address'] ?? '') . '</td>
                <td>' . htmlspecialchars($secret['uptime'] ?? '') . '</td>
                <td>' . $upload . '</td>
                <td>' . $download . '</td>
                <td>' . $status . '</td>
                <td>' . $viewbtn . '</td>
            </tr>';
        }

        $response = [
            'status' => $table,
            'connection' => true,
            'totalOnline' => $totalOnline,
            'totalCustomers' => count($agentMap),
            'totalUpload' => formatBytes($totalUpload),
            'totalDownload' => formatBytes($totalDownload),
        ];

    } else {
        $response = ['status' => 'No data Found', 'connection' => false];
    }
}
elseif (isset($_GET['mkidsecretofline'])) {
    
    
    // $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');


    $notification = "";
    //taking month and years
    $day = date('M-Y');
    $singleMikrotikAgent = $obj->view_all('tbl_agent');
    $agentList = [];
     if(!empty($singleMikrotikAgent)){
         //all agent data set to array
         foreach($singleMikrotikAgent as $agent){
             if (!agentInZoneFilter($agent)) {
                 continue;
             }
             $agentList[$agent['ip']] = $agent['ip'];
         }
     }
    $allSecretData = $obj->viewAllPppSecret($_GET['mkidsecretofline']) ?: [];
    $allRunnigSecretData = $obj->pppeoActiveSecretList($_GET['mkidsecretofline']);
    $agentListsecret = [];
     if(!empty($allRunnigSecretData)){
         foreach($allRunnigSecretData as $agentsecon){
            $agentsecon['name'] = trim(str_replace('<pppoe-','',$agentsecon['name']),'>');
            $agentListsecret[$agentsecon['name']] = $agentsecon['name'];
         }
     }

        $table = '';
        $sl = 1;
        // Info card counts (customers of this Mikrotik inside the zone filter)
        $totalCustomers = 0;
        $totalOnline = 0;
        $totalOffline = 0;
        $totalDisabled = 0;
        foreach ($allSecretData as $secretData) {
                    if(@$agentList[$secretData['name']] != $secretData['name']){
                        continue;
                    }
                    $totalCustomers++;

                    if(@$secretData['disabled'] == 'true'){
                        $totalDisabled++;
                        continue;
                    }
                    if(@$agentListsecret[$secretData['name']] == $secretData['name']){
                        $totalOnline++;
                        continue;
                    }
                    $totalOffline++;
            $status =  (@$secretData['disabled'] == 'false') ? 'offline' : 'Disabled';
            $table .= '
            <tr>
            <td> ' . $sl++ . '</td>
            <td> ' . $secretData['name'] . '</td>
            <td> ' . $secretData['password'] . '</td>
            <td> ' . $secretData['profile'] . '</td>
            <td> ' . $secretData['service'] . '</td>
            <td>' . $status . ' </td>
            <td> ' . ucfirst($secretData['last-logged-out']) . '</td>

            </tr>';
        }

        $response = [
            'status' =>  $table,
            'connection' => true,
            'totalCustomers' => $totalCustomers,
            'totalOnline' => $totalOnline,
            'totalOffline' => $totalOffline,
            'totalDisabled' => $totalDisabled,
        ];
    
}
elseif (isset($_GET['mkidsecretdisable'])) {

    $notification = "";
    $day = date('M-Y');

    // Get all agents
    $singleMikrotikAgent = $obj->view_all('tbl_agent');
    $agentList = [];

    if (!empty($singleMikrotikAgent)) {
        foreach ($singleMikrotikAgent as $agent) {
            $agentList[$agent['ip']] = $agent['ip'];
        }
    }

    $mkid = $_GET['mkidsecretdisable'];

    $allSecretData = $obj->viewAllPppSecret($mkid);

    $allRunnigSecretData = $obj->interfaceStatus($mkid);
    $agentListsecret = [];

    if (!empty($allRunnigSecretData)) {
        foreach ($allRunnigSecretData as $agentsecon) {
            $name = trim(str_replace('<pppoe-', '', $agentsecon['name']), '>');
            $agentListsecret[$name] = $name;
        }
    }

    $table = '';
    $sl = 1;

    foreach ($allSecretData as $secretData) {

        $name = $secretData['name'] ?? '';

        // Agent check
        if (!isset($agentList[$name])) {
            continue;
        }

        // ✅ ONLY disabled users
        if (!isset($secretData['disabled']) || $secretData['disabled'] != 'true') {
            continue;
        }

        $status = 'Disabled';

        $table .= '
        <tr>
            <td>' . $sl++ . '</td>
            <td>' . $name . '</td>
            <td>' . ($secretData['password'] ?? '') . '</td>
            <td>' . ($secretData['profile'] ?? '') . '</td>
            <td>' . ($secretData['service'] ?? '') . '</td>
            <td>' . $status . '</td>
            <td>' . ucfirst($secretData['last-logged-out'] ?? '') . '</td>
        </tr>';
    }

    $response = [
        'status' => $table,
        'connection' => true,
    ];
}
elseif (isset($_GET['mkidsecretunmatching'])) {

    $mikrotikConnection =  $obj->viewAllPppSecret($_GET['mkidsecretunmatching']);
    if ($mikrotikConnection) {
        $singleMikrotikAgent = $obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $_GET['mkidsecretunmatching']]]);
        $agentList = [];
        $unmatchingAgentList = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                $agentIP = $agent['ip'] ?? ''; // Avoid undefined index issue
                $agentList[$agentIP] = $agentIP;
            }
        }
        if (!empty($mikrotikConnection)) {
            foreach ($mikrotikConnection as $secret) {
                $unmatchingAgentList[$secret['name']] = $secret['name'];
            }
        }


        $table = '';
        $sl = 1;
        $unmatchingTable = '';
        if (!empty($singleMikrotikAgent)) {
            $i = 1;
            foreach ($singleMikrotikAgent as $agent) {
                if (@$unmatchingAgentList[$agent['ip']] == $agent['ip']) {
                    continue;
                }

                if (@$agent['deleted_at'] == NULL) {
                    $status2 =  (@$agent['ag_status'] == 1) ? 'Active' : 'Inactive';
                    $delete = '<button data-id="' . $agent['ag_id'] . '" class="btn btn-xs btn-danger secretDelete"><iconify-icon icon="mdi:delete" class="text-xl"></iconify-icon></button>';
                    $unmatchingTable .= '
                <tr>
                    <td>' . $i . '</td>
                    <td>' . $agent['ag_name'] . '</td>
                    <td>' . $agent['ip'] . '</td>
                    <td>' . $agent['queue_password'] . '</td>
                    <td>' . $agent['mb'] . '</td>
                    <td>' . $status2 . '</td>
                    <td>' . $delete . '</td>
                </tr>';
                }

                $i++;
            }
        }

        foreach ($mikrotikConnection as $secret) {
            if (@$agentList[$secret['name']] == $secret['name']) {
                continue;
            }

            $status =  (@$secret['ag_status'] == 'false') ? 'Active' : 'Inactive';
            $lastLogout = (isset($secret['last-logged-out']) && ($secret['last-logged-out'] != 'jan/01/1970 00:00:00')) ? ucfirst($secret['last-logged-out']) : '';
            $status = ($secret['disabled'] == 'false') ? 'Enable' : 'Disable';
            $actionbtn = '<button id="secretCangeStatus"  data-status="' . $secret['disabled'] . '"   data-profile="' . $secret['profile'] . '"  data-password="' . $secret['password'] . '" data-name="' . $secret['name'] . '" data-mkid="' . $_GET['mkidsecretunmatching'] . '" class="btn btn-xs btn-primary secretAddSoft"><iconify-icon icon="mdi:plus" class="text-xl"></iconify-icon></button>';
            $table .= '
            <tr>
            <td> ' . $sl++ . '</td>
            <td> ' . $secret['name'] . '</td>
            <td> ' . $secret['password'] . '</td>
            <td> ' . $secret['profile'] . '</td>
            <td> ' . $secret['service'] . '</td>
            <td> ' . $lastLogout . ' </td>
            <td> ' . $status . ' </td>
            <td> ' . $actionbtn . ' </td>
            </tr>';
        }

        $response = [
            'status' =>  $table,
            'unmatching' =>  $unmatchingTable,
            'connection' => true,
        ];
    } else {
        $response = [
            'status' =>  'No data Found',
            'connection' => false,
        ];
    }
}
elseif (isset($_GET['mikrotikStatikUnmatchingUser'])) {

    $totalInactive = 0;
    $totalActive = 0;
    $table = '';
    $sl = 1;

    $mk = $obj->getSingleData('mikrotik_user', [
        'where' => [
            ['id', '=', $_GET['mikrotikStatikUnmatchingUser']],
            ['status', '=', '1']
        ]
    ]);

    if ($mk) {

        $statikMikrotik = new Mikrotik(
            $mk["mik_ip"],
            $mk["mik_port"],
            $mk["mik_username"],
            $mk["mik_password"]
        );

        if ($statikMikrotik && $statikMikrotik->connected) {

            $mikrotikConnection = $statikMikrotik->comm("/queue/simple/print");

            if ($mikrotikConnection) {

                $agents = $obj->getAllData('tbl_agent', [
                    'where' => ['mikrotik_id', '=', $_GET['mikrotikStatikUnmatchingUser']]
                ]);

                $agentList = [];
                foreach ($agents as $agent) {
                    $agentList[$agent['ip']] = true;
                }

                foreach ($mikrotikConnection as $secret) {

                    if (empty($secret['target'])) continue;

                    $targetIp = explode("/", $secret['target'])[0];

                    // ðŸ”´ matching à¦¥à¦¾à¦•à¦²à§‡ skip
                    if (isset($agentList[$targetIp])) continue;

                    // count
                    ($secret['disabled'] == 'true') ? $totalInactive++ : $totalActive++;

                    $statusbtn = ($secret['disabled'] == 'false')
                        ? '<button data-status="1" data-name="'.$secret['name'].'" class="btn btn-xs btn-success changeStatus">Enable</button>'
                        : '<button data-status="0" data-name="'.$secret['name'].'" class="btn btn-xs btn-danger changeStatus">Disable</button>';

                    $table .= '
                    <tr class="table-danger">
                        <td>'.$sl++.'</td>
                        <td><span class="badge badge-danger">Unmatched</span></td>
                        <td>'.$secret['target'].'</td>
                        <td>'.$secret['name'].'</td>
                        <td>'.$secret['rate'].'</td>
                        <td>'.number_format(array_sum(explode("/", $secret['bytes'])) / 1024 / 1024 / 1024, 2).' GB</td>
                        <td>'.$statusbtn.'</td>
                    </tr>';
                }

                $response = [
                    'status' => $table ?: '<tr><td colspan="7" class="text-center">No Unmatched Static User</td></tr>',
                    'connection' => true,
                    'totalInactive' => $totalInactive,
                    'totalActive' => $totalActive,
                ];
            }
        }
    }
}

else if ($_POST['mkid']) {
    $mikrotik_id = $_POST['mkid'];
    $updateResult = $obj->updateData('mikrotik_user', ['status' => 1], ['id' => $mikrotik_id]);
    $response = [
        'status' => 'Data updated successfully!',
        'success' => true,
    ];
} else {
    $response = [
        'status' => 'Invalid ID provided',
        'connection' => false,
    ];
}
echo json_encode($response);
