<?php
  // Initialize the session
  session_start();
   
  // Check if the user is logged in, if not then redirect him to login page
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
  {
      //header("location: login.php");
      //exit;
  }
?>

<!DOCTYPE html>

<html>
  <head>
    <meta charset="UTF-8" />
    <link href="css/index.css" rel="stylesheet" type="text/css" />
    <link rel = "icon" type = "image/icon" href = "images/Fatcow-Farm-Fresh-Table-add.ico" />
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">

    <title>ctTextbooks</title>
  </head>

  <body>
    <?php include_once 'includes/header.php'?>
    <?php require_once "includes/config.php"?>
    <div>
    <h2>Geocoding Service</h2>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3slsv5zhLYYrjxIWDkya6vwaFGWJVCqM&callback=initMap&libraries=&v=weekly"
      defer
    > </script>

    <script>
      function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 8,
          center: { lat: -34.397, lng: 150.644 },
        });
        const geocoder = new google.maps.Geocoder();
        geocodeAddress(geocoder, map);
        document.getElementById("submit").addEventListener("click", () => {
          geocodeAddress(geocoder, map);
        });
      }

      function geocodeAddress(geocoder, resultsMap) {
        //const address = document.getElementById("address").value;
        var url_string = window.location.href;
        var url = new URL(url_string);
        var college = url.searchParams.get("college");
        //document.getElementById("demo").innerHTML = college;
        const address = college;
        //const address = "9414 NORTH 25TH AVENUE,PHOENIX,AZ,85021";
        geocoder.geocode({ address: address }, (results, status) => {
          if (status === "OK") {
            resultsMap.setCenter(results[0].geometry.location);
            new google.maps.Marker({
              map: resultsMap,
              position: results[0].geometry.location,
            });
          } else {
            alert(
              "Geocode was not successful for the following reason: " + status
            );
          }
        });
      }

      function FindUniversity()
      {
        const map = new google.maps.Map(document.getElementById("map"), {
        });
        const geocoder = new google.maps.Geocoder();
      }
    </script>
    <div id="floating-panel">
      <input id="address" type="textbox" value="Sydney, NSW" />
      <input id="submit" type="button" value="Geocode" />
    </div>
    <div id="map"></div>
    <button type="button">Click Me!</button>

  </body>
  <footer></footer>
</html>
