<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
        <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
        <style>
            /*
            .map, .righ-panel {
                height: 500px;
                width: 80%;
                float: left;
            }
            */
            .map, .righ-panel {
                height: 98vh;
                width: 80vw;
                float: left;
            }
            .map {
                border: 1px solid #000;
            }
            .map, .righ-panel {
                height: 98vh;
                width: 80vw;
                float: left;
            }
            .map {
                border: 1px solid #000;
            }

            /* lam popup */
            .ol-popup {
                position: absolute;
                background-color: white;
                -webkit-filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
                filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
                padding: 15px;
                border-radius: 10px;
                border: 1px solid #cccccc;
                bottom: 12px;
                left: -50px;
                min-width: 280px;
            }
            .ol-popup:after, .ol-popup:before {
                top: 100%;
                border: solid transparent;
                content: " ";
                height: 0;
                width: 0;
                position: absolute;
                pointer-events: none;
            }
            .ol-popup:after {
                border-top-color: white;
                border-width: 10px;
                left: 48px;
                margin-left: -10px;
            }
            .ol-popup:before {
                border-top-color: #cccccc;
                border-width: 11px;
                left: 48px;
                margin-left: -11px;
            }
            .ol-popup-closer {
                text-decoration: none;
                position: absolute;
                top: 2px;
                right: 8px;
            }
            .ol-popup-closer:after {
                content: "✖";
            }
        </style>
    </head>
    <body onload="initialize_map()">
        <table>
            <tr>
                <td>
                    <div id="map" class="map"></div>
                    <!--<div id="map" style="width: 80vw; height: 100vh;"></div>-->
                </td>
                <td>
                    <div id="info"></div>
                    <div id="popup" class="ol-popup">
                        <a href="#" id="popup-closer" class="ol-popup-closer"></a>
                        <div id="popup-content"></div>
                    </div>
                </td>
                <td>
                    <input type='checkbox' id="thuydien" onclick="addlay('thuydien')"> Thủy điện<br>
                    <input type='checkbox' id="khubaoton" onclick="addlay('khubaoton')" > Khu bảo tồn<br>
                    <input type='checkbox' id="songngoi"  onclick="addlay('songngoi')"> Sông ngòi<br>

                </td>
            </tr>
        </table>
        <?php include 'CMR_pgsqlAPI.php' ?>
        <script src="func.js" type="text/javascript">
        </script>
    </body>
</html>