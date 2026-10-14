<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<?php 
$sql = "SELECT 
    ag_id,
    ag_name,
    ag_mobile_no,
    ag_office_address,
    sub_zone,
    latitude,
    logitude
FROM tbl_agent 
WHERE latitude IS NOT NULL 
AND logitude IS NOT NULL";

$customers = $obj->raw_sql($sql);

?>

<div id="map" style="height:600px;"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>


let customers = <?= json_encode($customers); ?>;

const map = L.map('map').setView([23.8103, 90.4125], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
}).addTo(map);

customers.forEach(c => {

    if (c.latitude && c.logitude) {

        L.marker([
            parseFloat(c.latitude),
            parseFloat(c.logitude)
        ])
        .addTo(map)
        .bindPopup(`
            <b>${c.ag_name}</b><br>
            Address: ${c.ag_office_address ?? ''}<br>
            Mobile: ${c.ag_mobile_no ?? ''}
        `);
    }
});
</script>