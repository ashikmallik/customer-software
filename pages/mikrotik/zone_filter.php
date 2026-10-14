<?php
// Zone / Sub Zone filter columns for the Mikrotik secret pages.
// Include inside a .row; the page listens for the "mkZoneFilterChange" event
// and sends mkZoneFilterParams() with its ajax request.
$mkFilterZones = $obj->rawSql('SELECT zone_id, zone_name FROM tbl_zone WHERE level = 1 ORDER BY zone_name') ?: [];
$mkFilterSubZones = $obj->rawSql('SELECT zone_id, zone_name, parent_id FROM tbl_zone WHERE level = 2 ORDER BY zone_name') ?: [];
?>
<div class="col-sm-3">
    <label for="zone-filter" class="form-label">Zone</label>
    <div class="position-relative">
        <select id="zone-filter" class="form-control">
            <option value="">All Zones</option>
            <?php foreach ($mkFilterZones as $zone): ?>
                <option value="<?= $zone['zone_id'] ?>"><?= htmlspecialchars($zone['zone_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<div class="col-sm-3">
    <label for="sub-zone-filter" class="form-label">Sub Zone</label>
    <div class="position-relative">
        <select id="sub-zone-filter" class="form-control">
            <option value="">All Sub Zones</option>
            <?php foreach ($mkFilterSubZones as $subZone): ?>
                <option value="<?= $subZone['zone_id'] ?>" data-zone="<?= $subZone['parent_id'] ?>"><?= htmlspecialchars($subZone['zone_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<?php $obj->start_script(); ?>
<script>
    function mkZoneFilterParams() {
        return {
            zone: $('#zone-filter').val(),
            sub_zone: $('#sub-zone-filter').val()
        };
    }

    // Zone change: show only that zone's sub zones
    $('#zone-filter').on('change', function() {
        const zoneId = $(this).val();
        $('#sub-zone-filter').val('');
        $('#sub-zone-filter option[data-zone]').each(function() {
            $(this).toggle(!zoneId || String($(this).data('zone')) === zoneId);
        });
        $(document).trigger('mkZoneFilterChange');
    });

    $('#sub-zone-filter').on('change', function() {
        $(document).trigger('mkZoneFilterChange');
    });
</script>
<?php $obj->end_script(); ?>
