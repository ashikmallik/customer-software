<?php
// NOTE: Ei file ta 'forward.php' - ata index.php?page=forward theke include hoy.
// $obj (apnar main class-er object) upore index.php te already create kora ache,
// tai ekhane abar require/new korar dorkar nei.
// Jodi standalone test korte chan, tahole niche 2 line-er comment tule den:

// require_once 'your_class_file.php';
// $obj = new YourClassName();

$message = '';
$results = [];
$mikrotikIdForList = $_GET['mikrotik_id'] ?? ($_POST['mikrotik_id'] ?? '');
$existingRules = [];
$editRule = null; // jei rule ta ekhon edit kora hocche, tar data rakhbe

// dropdown-e dekhanor jonno shob active mikrotik-er list
$mikrotikList = $obj->getMikrotikList();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? 'add';

    // ---------------- ADD notun port forward ----------------
    if ($action === 'add') {
        $mikrotikId   = $_POST['mikrotik_id'];
        $portsString  = $_POST['ports'];          // example: 161,22,443,80
        $toAddress    = $_POST['to_address'];     // internal IP, e.g. 192.168.88.10
        $protocolType = $_POST['protocol'];       // TCP / UDP / TCP+UDP
        $comment      = $_POST['comment'];

        // asol call - eita real verify kore return kore (success/failed)
        $results = $obj->createMultiplePortForward($mikrotikId, $portsString, $toAddress, $protocolType, $comment);

        $successCount = count(array_filter($results, fn($r) => $r['status'] === 'success'));
        $message = "$successCount ta rule verify kore forward hoyeche (MikroTik theke real check kore)";
    }

    // ---------------- UPDATE existing rule ----------------
    if ($action === 'update') {
        $mikrotikId = $_POST['mikrotik_id'];
        $ruleId     = $_POST['rule_id'];
        $dstPort    = $_POST['dst_port'];
        $toAddress  = $_POST['to_address'];
        $toPort     = $_POST['to_port'] ?: $dstPort;
        $protocol   = $_POST['protocol'];
        $comment    = $_POST['comment'];

        $res = $obj->updatePortForward($mikrotikId, $ruleId, $dstPort, $toAddress, $toPort, $protocol, $comment);
        $message = $res['ok'] ? "Rule ta successfully update hoyeche" : "Update failed";
    }

    // ---------------- DELETE existing rule ----------------
    if ($action === 'delete') {
        $mikrotikId = $_POST['mikrotik_id'];
        $ruleId     = $_POST['rule_id'];

        $ok = $obj->deletePortForward($mikrotikId, $ruleId);
        $message = $ok ? "Rule ta successfully delete hoyeche" : "Delete failed - rule ekhono ache";
    }

    // ---------------- ENABLE / DISABLE toggle ----------------
    if ($action === 'toggle') {
        $mikrotikId = $_POST['mikrotik_id'];
        $ruleId     = $_POST['rule_id'];
        $disable    = $_POST['disable'] === '1';

        $obj->togglePortForward($mikrotikId, $ruleId, $disable);
        $message = $disable ? "Rule disable kora hoyeche" : "Rule enable kora hoyeche";
    }
}

// edit korte chaile (?edit_id=... URL e), sei rule-er current data anbo
if (!empty($_GET['edit_id']) && $mikrotikIdForList) {
    $editRule = $obj->getPortForwardById($mikrotikIdForList, $_GET['edit_id']);
}

// page load hole ba submit korar por, existing NAT list o dekhano
if ($mikrotikIdForList) {
    $existingRules = $obj->getPortForwardList($mikrotikIdForList);
}
?>

<style>
    :root {
        --primary: #4f6df5;
        --primary-dark: #3b54d6;
        --danger: #e5484d;
        --success: #16a34a;
        --success-bg: #dcfce7;
        --danger-bg: #fee2e2;
        --warn-bg: #fef3c7;
        --warn-border: #f5cb5c;
        --bg: #f2f4f9;
        --card: #ffffff;
        --border: #e4e7ee;
        --text: #1f2430;
        --muted: #6b7280;
        --radius: 14px;
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', system-ui, -apple-system, Arial, sans-serif;
        background: var(--bg);
        margin: 0;
        padding: 24px 16px 60px;
        color: var(--text);
    }

    .page-wrap {
        max-width: 900px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 4px;
    }

    .page-header h1 {
        font-size: 22px;
        margin: 0 0 4px;
        background: linear-gradient(135deg, var(--primary), #8b5cf6);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        font-weight: 800;
    }

    .page-header p {
        margin: 0;
        color: var(--muted);
        font-size: 13.5px;
    }

    .card {
        background: var(--card);
        padding: 24px 26px;
        border-radius: var(--radius);
        box-shadow: 0 1px 3px rgba(20,20,43,.06), 0 8px 24px rgba(20,20,43,.05);
        border: 1px solid var(--border);
    }

    .card h2 {
        margin: 0 0 18px;
        font-size: 17px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text);
    }

    .card h2::before {
        content: '';
        width: 6px;
        height: 18px;
        background: var(--primary);
        border-radius: 3px;
        display: inline-block;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 16px;
    }

    .form-grid .full { grid-column: 1 / -1; }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        font-size: 13px;
        color: #374151;
    }

    input[type=text], select {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid var(--border);
        border-radius: 9px;
        font-size: 14px;
        background: #fafbfe;
        transition: border-color .15s, box-shadow .15s;
        color: var(--text);
    }

    input[type=text]:focus, select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79,109,245,.15);
        background: #fff;
    }

    .field { display: flex; flex-direction: column; }

    button, .btn {
        cursor: pointer;
        font-size: 14px;
        border: none;
        border-radius: 9px;
        font-weight: 600;
        transition: transform .06s ease, opacity .15s ease, background .15s ease;
    }

    button:active, .btn:active { transform: scale(0.97); }

    .btn-primary {
        margin-top: 20px;
        padding: 11px 24px;
        background: linear-gradient(135deg, var(--primary), #6b7ff5);
        color: #fff;
        width: 100%;
        font-size: 15px;
        box-shadow: 0 4px 14px rgba(79,109,245,.35);
    }
    .btn-primary:hover { opacity: .92; }

    @media (min-width: 480px) {
        .btn-primary { width: auto; }
    }

    .msg {
        margin-top: 18px;
        padding: 12px 16px;
        background: var(--success-bg);
        border: 1px solid #a7e6bd;
        border-radius: 10px;
        color: #166534;
        font-size: 13.5px;
        font-weight: 500;
    }

    /* ---- table ---- */
    .table-scroll {
        overflow-x: auto;
        margin-top: 18px;
        border-radius: 10px;
        border: 1px solid var(--border);
    }

    table { width: 100%; border-collapse: collapse; min-width: 640px; }
    th, td { padding: 10px 12px; text-align: left; font-size: 13px; }
    thead th {
        background: #f6f7fb;
        color: #4b5165;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: .03em;
        border-bottom: 1px solid var(--border);
    }
    tbody tr { border-bottom: 1px solid var(--border); }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #fafbff; }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .badge.success { background: var(--success-bg); color: #166534; }
    .badge.failed  { background: var(--danger-bg); color: #991b1b; }

    .actions { display: flex; gap: 6px; flex-wrap: nowrap; align-items: center; }

    .action-btn {
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 7px;
        border: 1px solid var(--border);
        background: #fff;
        color: #374151;
    }
    .action-btn:hover { background: #f3f4f6; }
    .action-btn.edit { color: var(--primary); border-color: #c7d2fe; }
    .action-btn.edit:hover { background: #eef1ff; }
    .action-btn.delete { color: var(--danger); border-color: #fecaca; }
    .action-btn.delete:hover { background: var(--danger-bg); }
    .action-btn.toggle { color: #b45309; border-color: #fde68a; }
    .action-btn.toggle:hover { background: #fffbeb; }

    /* ---- edit box ---- */
    .edit-box {
        background: var(--warn-bg);
        border: 1px solid var(--warn-border);
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 18px;
    }
    .edit-box h3 {
        margin: 0 0 14px;
        font-size: 15px;
        color: #92400e;
    }
    .edit-box .cancel-link {
        display: inline-block;
        margin-top: 20px;
        margin-left: 10px;
        color: var(--muted);
        font-size: 13px;
        text-decoration: none;
    }
    .edit-box .cancel-link:hover { text-decoration: underline; }

    .empty-state {
        color: var(--muted);
        font-size: 13.5px;
        padding: 20px 4px;
        text-align: center;
    }

    /* ---- results table right after add ---- */
    .results-table th, .results-table td { font-size: 12.5px; }

    /* ---- responsive stack for small screens ---- */
    @media (max-width: 640px) {
        body { padding: 16px 10px 40px; }
        .card { padding: 18px 16px; border-radius: 12px; }
        .form-grid { grid-template-columns: 1fr; gap: 12px; }
        .page-header h1 { font-size: 19px; }
    }
</style>
</head>
<body>
<div class="page-wrap">

    <div class="page-header">
        <h1>🌐 Mikrotik Port Forward Manager</h1>
        <p>NAT rule add, verify, edit ar delete — shob ekhane theke</p>
    </div>

    <!-- ================= ADD NEW PORT FORWARD ================= -->
    <div class="card">
        <h2>Notun Port Forward Add Korun</h2>
        <form method="POST" action="?page=forward">
            <input type="hidden" name="action" value="add">
            <div class="form-grid">
                <div class="field full">
                    <label>Mikrotik Select Korun</label>
                    <select name="mikrotik_id" required>
                        <option value="">-- Mikrotik select korun --</option>
                        <?php foreach ($mikrotikList as $mk): ?>
                            <option value="<?= htmlspecialchars($mk['id']) ?>" <?= ($mk['id'] == $mikrotikIdForList) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mk['mik_name'] ?? ('Router #' . $mk['id'])) ?> (<?= htmlspecialchars($mk['mik_ip']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field full">
                    <label>Ports (comma diye alada korun)</label>
                    <input type="text" name="ports" required placeholder="e.g. 161,22,443,80" value="161,22,443,80">
                </div>

                <div class="field">
                    <label>To Address (Internal/LAN IP)</label>
                    <input type="text" name="to_address" required placeholder="e.g. 192.168.88.10">
                </div>

                <div class="field">
                    <label>Protocol</label>
                    <select name="protocol">
                        <option value="TCP">TCP</option>
                        <option value="UDP">UDP</option>
                        <option value="TCP+UDP" selected>TCP+UDP (dutoi)</option>
                    </select>
                </div>

                <div class="field full">
                    <label>Comment (optional)</label>
                    <input type="text" name="comment" placeholder="e.g. Office Server PF">
                </div>
            </div>

            <button type="submit" class="btn-primary">➕ Add Port Forward</button>
        </form>

        <?php if ($message): ?>
            <div class="msg">✅ <?= htmlspecialchars($message) ?></div>
            <?php if (!empty($results)): ?>
            <div class="table-scroll">
                <table class="results-table">
                    <thead>
                        <tr><th>Port</th><th>Protocol</th><th>Status</th><th>Rule ID</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['port']) ?></td>
                                <td><?= strtoupper(htmlspecialchars($r['protocol'])) ?></td>
                                <td>
                                    <span class="badge <?= $r['status']==='success' ? 'success' : 'failed' ?>">
                                        <?= $r['status']==='success' ? '✅ Forward hoise' : '❌ Hoyni' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($r['rule_id'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- ================= EXISTING NAT RULE LIST ================= -->
    <div class="card">
        <h2>Current Port Forward List</h2>

        <form method="GET" action="">
            <input type="hidden" name="page" value="forward">
            <div class="field">
                <label>Mikrotik Select Kore Search Korun</label>
                <select name="mikrotik_id" onchange="this.form.submit()">
                    <option value="">-- Mikrotik select korun --</option>
                    <?php foreach ($mikrotikList as $mk): ?>
                        <option value="<?= htmlspecialchars($mk['id']) ?>" <?= ($mk['id'] == $mikrotikIdForList) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mk['mik_name'] ?? ('Router #' . $mk['id'])) ?> (<?= htmlspecialchars($mk['mik_ip']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <noscript><button type="submit" class="btn-primary">List Dekhao</button></noscript>
        </form>

        <?php if ($mikrotikIdForList): ?>
            <?php if (empty($existingRules)): ?>
                <p class="empty-state">😶 Kono rule paoya jayni, ba class connect kora hoyni.</p>
            <?php else: ?>

                <?php if ($editRule): ?>
                    <div class="edit-box">
                        <h3>✏️ Rule Edit Korun (ID: <?= htmlspecialchars($editRule['.id']) ?>)</h3>
                        <form method="POST" action="?page=forward&mikrotik_id=<?= urlencode($mikrotikIdForList) ?>">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="mikrotik_id" value="<?= htmlspecialchars($mikrotikIdForList) ?>">
                            <input type="hidden" name="rule_id" value="<?= htmlspecialchars($editRule['.id']) ?>">

                            <div class="form-grid">
                                <div class="field">
                                    <label>Dst Port</label>
                                    <input type="text" name="dst_port" required value="<?= htmlspecialchars($editRule['dst-port'] ?? '') ?>">
                                </div>
                                <div class="field">
                                    <label>To Port</label>
                                    <input type="text" name="to_port" value="<?= htmlspecialchars($editRule['to-ports'] ?? '') ?>">
                                </div>
                                <div class="field">
                                    <label>To Address</label>
                                    <input type="text" name="to_address" required value="<?= htmlspecialchars($editRule['to-addresses'] ?? '') ?>">
                                </div>
                                <div class="field">
                                    <label>Protocol</label>
                                    <select name="protocol">
                                        <option value="tcp" <?= (($editRule['protocol'] ?? '')==='tcp') ? 'selected':'' ?>>TCP</option>
                                        <option value="udp" <?= (($editRule['protocol'] ?? '')==='udp') ? 'selected':'' ?>>UDP</option>
                                    </select>
                                </div>
                                <div class="field full">
                                    <label>Comment</label>
                                    <input type="text" name="comment" value="<?= htmlspecialchars($editRule['comment'] ?? '') ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn-primary" style="display:inline-block; width:auto;">💾 Update Korun</button>
                            <a href="?page=forward&mikrotik_id=<?= urlencode($mikrotikIdForList) ?>" class="cancel-link">Cancel</a>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Dst Port</th>
                                <th>Protocol</th>
                                <th>To Address</th>
                                <th>To Port</th>
                                <th>Comment</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($existingRules as $rule): ?>
                                <?php $isDisabled = (($rule['disabled'] ?? '') === 'true'); ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($rule['dst-port'] ?? '-') ?></strong></td>
                                    <td><?= strtoupper(htmlspecialchars($rule['protocol'] ?? '-')) ?></td>
                                    <td><?= htmlspecialchars($rule['to-addresses'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($rule['to-ports'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($rule['comment'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge <?= $isDisabled ? 'failed' : 'success' ?>">
                                            <?= $isDisabled ? 'Disabled' : 'Active' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a class="action-btn edit" href="?page=forward&mikrotik_id=<?= urlencode($mikrotikIdForList) ?>&edit_id=<?= urlencode($rule['.id']) ?>">Edit</a>

                                            <form method="POST" action="?page=forward&mikrotik_id=<?= urlencode($mikrotikIdForList) ?>" style="display:inline;">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="mikrotik_id" value="<?= htmlspecialchars($mikrotikIdForList) ?>">
                                                <input type="hidden" name="rule_id" value="<?= htmlspecialchars($rule['.id']) ?>">
                                                <input type="hidden" name="disable" value="<?= $isDisabled ? '0' : '1' ?>">
                                                <button type="submit" class="action-btn toggle"><?= $isDisabled ? 'Enable' : 'Disable' ?></button>
                                            </form>

                                            <form method="POST" action="?page=forward&mikrotik_id=<?= urlencode($mikrotikIdForList) ?>" style="display:inline;" onsubmit="return confirm('Ei rule ta delete korte chan?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="mikrotik_id" value="<?= htmlspecialchars($mikrotikIdForList) ?>">
                                                <input type="hidden" name="rule_id" value="<?= htmlspecialchars($rule['.id']) ?>">
                                                <button type="submit" class="action-btn delete">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>
