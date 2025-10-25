<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />


<!-- Maplibre GL -->
<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet" />

</head>
<body>
<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<!-- Maplibre GL Leaflet  -->
<script src="https://unpkg.com/@maplibre/maplibre-gl-leaflet/leaflet-maplibre-gl.js"></script>
<div id="map" style="width: 100%; height: 500px"></div>
<script>
  const map = L.map('map').setView([52.517, 13.388], 9.5)

  L.maplibreGL({
    style: 'https://tiles.openfreemap.org/styles/liberty',
  }).addTo(map)
</script>
</body>
</html>