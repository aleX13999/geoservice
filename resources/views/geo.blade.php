<!-- resources/views/geo/map.blade.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>GeoTracks Viewer</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .controls { background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.1); display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        #map { height: 75vh; margin-top: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.15); }
        button { background: #2563eb; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
    </style>
</head>
<body>
<div class="container">
    <div class="controls">
        <select id="device_id">
            @foreach(\App\Models\GeoDevice::all() as $dev)
                <option value="{{ $dev->id }}">{{ $dev->name ?: "Устройство #{$dev->id}" }}</option>
            @endforeach
        </select>
        <input type="date" id="date" value="">
        <button onclick="loadSegments()">Найти трек</button>
        <span id="status" style="margin-left:auto; color:#666; font-size:0.9em;"></span>
    </div>
    <div id="map"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([53.35, 83.75], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let currentLayers = [];

    async function loadSegments() {
        const status = document.getElementById('status');
        status.textContent = 'Загрузка...';

        currentLayers.forEach(l => map.removeLayer(l));
        currentLayers = [];

        const device = document.getElementById('device_id').value;
        const date = document.getElementById('date').value;

        try {
            const res = await fetch(`/api/segments?device_id=${device}&date=${date}`);
            const data = await res.json();
            const allPoints = [];

            data.segments.forEach(seg => {
                const line = L.polyline(seg.points, {
                    color: seg.color,
                    weight: 4,
                    lineJoin: 'round'
                }).addTo(map);
                line.bindTooltip(`Зона #${seg.zone_id || 'Вне зоны'}`, { permanent: false });
                currentLayers.push(line);
                allPoints.push(...seg.points);
            });

            if (allPoints.length > 0) {
                map.fitBounds(allPoints, { padding: [50, 50], maxZoom: 16 });
                status.textContent = `Загружено ${data.segments.length} сегментов.`;
            } else {
                status.textContent = 'Треков не найдено';
            }
        } catch (e) {
            status.textContent = 'Ошибка загрузки';
            console.error(e);
        }
    }

    document.addEventListener('DOMContentLoaded', loadSegments);
</script>
</body>
</html>
