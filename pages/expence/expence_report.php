<?php
include('expence.php');

$headId = isset($_POST['head-filter']) ? $_POST['head-filter'] : '';
$dateform = isset($_POST['dateform']) ? $_POST['dateform'] : date('Y-m-01');
$dateto = isset($_POST['dateto']) ? $_POST['dateto'] : date('Y-m-d');

$headFilter = "";

if (!empty($headId)) {
    $headId = (int)$headId;
    $headFilter = " AND acc_head = '$headId' ";
}

$expenseDetails = $obj->rawSql("
    SELECT * FROM tbl_account 
    WHERE acc_type = '1'
    AND entry_date BETWEEN '$dateform' AND '$dateto'
    $headFilter
    ORDER BY entry_date DESC
");
?>

<div class="row mt-3">
    <div class="col-md-12">

        <form method="POST">
            <div class="card">
                <div class="card-body">

                    <!-- FILTER -->
                    <div class="row mb-3">

                        <div class="col-md-4">
                            <label>Head</label>
                            <select name="head-filter" id="head-filter" class="form-control">
                                <option value="">All Head</option>
                                <?php
                                $heads = $obj->getAllData("tbl_accounts_head", ['where' => ['level', '=', '1']]);
                                foreach ($heads as $head) {
                                ?>
                                    <option value="<?= $head['acc_id'] ?>"
                                        <?= ($headId == $head['acc_id']) ? 'selected' : '' ?>>
                                        <?= $head['acc_name'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>From</label>
                            <input type="date" name="dateform" class="form-control" value="<?= $dateform ?>">
                        </div>

                        <div class="col-md-3">
                            <label>To</label>
                            <input type="date" name="dateto" class="form-control" value="<?= $dateto ?>">
                        </div>

                        <div class="col-md-2 mt-4">
                            <button class="btn btn-secondary w-100" name="search">Search</button>
                        </div>

                    </div>

                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="expense-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Head</th>
                                    <th>Sub Head</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $i = 0;
                                $total = 0;

                                foreach ($expenseDetails as $row) {
                                    $i++;
                                    $total += (int)$row['acc_amount'];

                                    $headName = '';
                                    $subHeadName = '';

                                    if (!empty($row['acc_head'])) {
                                        $head = $obj->getSingleData("tbl_accounts_head", [
                                            'where' => ['acc_id', '=', $row['acc_head']]
                                        ]);
                                        $headName = $head['acc_name'] ?? '';
                                    }

                                    if (!empty($row['acc_sub_head'])) {
                                        $sub = $obj->getSingleData("tbl_accounts_head", [
                                            'where' => ['acc_id', '=', $row['acc_sub_head']]
                                        ]);
                                        $subHeadName = $sub['acc_name'] ?? '';
                                    }
                                ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= date('d-m-Y', strtotime($row['entry_date'])) ?></td>
                                        <td><?= $headName ?: 'N/A' ?></td>
                                        <td><?= $subHeadName ?: 'N/A' ?></td>
                                        <td><?= $row['acc_description'] ?? '' ?></td>
                                        <td><?= $row['acc_amount'] ?? 0 ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="5">Total</th>
                                    <th><?= $total ?> tk</th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>