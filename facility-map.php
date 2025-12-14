<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Facility Map</title>
<link rel="stylesheet" href="assets/css/facility-map.css">
<style>
    img {
        width: 100%;
        max-width: 1000px;
    }
</style>
</head>

<body>

<a href="index.php" style="display: inline-block;">
    <img src="assets/images/Paw House Logo.png" alt="Logo" 
         style="width: 55px; height: 55px; cursor: pointer;">
</a>

<h1>📍 Facility Map</h1>

<div class="map-container">
<img src="assets/images/facility-map.png" usemap="#facilitymap" alt="Facility Map">

<map name="facilitymap">

    <area shape="rect" coords="197,48,378,236" href="assets/images/frontdesk.jpg" alt="Front Desk">
    <area shape="rect" coords="303,426,753,615" href="assets/images/dogadoptionarea.jpg" alt="Dog Adoption Area">
    <area shape="rect" coords="585,48,893,285" href="assets/images/isolationarea.jpg" alt="Isolation Area">
    <area shape="rect" coords="902,48,1347,401" href="assets/images/vetclinic.jpg" alt="Veterinary Clinic">
    <area shape="rect" coords="296,702,522,845" href="assets/images/feeding and supply storage.jpg" alt="Feeding & Supply Storage">
    <area shape="rect" coords="912,423,1101,683" href="assets/images/playarea.jpg" alt="Play Area">
    <area shape="rect" coords="554,734,794,942" href="assets/images/groomingroom.jpg" alt="Grooming Room">
    <area shape="rect" coords="1130,434,1334,681" href="assets/images/parkingarea.jpg" alt="Parking Area">

</map>
</div>

<script src="https://cdn.jsdelivr.net/npm/image-map-resizer@1.0.10/js/imageMapResizer.min.js"></script>
<script>
  imageMapResize();
</script>

</body>
</html>
