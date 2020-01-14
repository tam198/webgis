var format = "image/png";
var map;
var container = document.getElementById("popup");
var content = document.getElementById("popup-content");
var closer = document.getElementById("popup-closer");
var minX = 102.144996643066;
var minY = 8.56333160400391;
var maxX = 109.469436645508;
var maxY = 23.3927326202393;
var cenX = (minX + maxX) / 2;
var cenY = (minY + maxY) / 2;
var mapLat = cenY;
var mapLng = cenX;
var mapDefaultZoom = 5;
var layerKhu_bao_ton;
var layerTPtinh;
var river;
var thuydien;

function addlay(id) {
  vectorLayer = new ol.layer.Vector({});
  if (id == "khubaoton") {
    if (document.getElementById("khubaoton").checked) {
      map.addLayer(layerKhu_bao_ton);
      map.addLayer(vectorLayer);
    } else {
      map.removeLayer(layerKhu_bao_ton);
      map.removeLayer(vectorLayer);
    }
  } else if (id == "thuydien") {
    console.log(layerTPtinh);
    if (document.getElementById("thuydien").checked) {
      map.addLayer(thuydien);
      map.addLayer(vectorLayer);
    } else {
      map.removeLayer(thuydien);
      map.removeLayer(vectorLayer);
    }
  } else if (id == "songngoi") {
    if (document.getElementById("songngoi").checked) {
      map.addLayer(river);
      map.addLayer(vectorLayer);
    } else {
      map.removeLayer(river);
      map.removeLayer(vectorLayer);
    }
  }
}
function initialize_map() {
  //*
  layerBG = new ol.layer.Tile({
    source: new ol.source.OSM({})
  });
  //*/
  layerTPtinh = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      ratio: 1,
      url: "http://localhost:8081/geoserver/WebgisProject/wms?",
      params: {
        FORMAT: format,
        VERSION: "1.1.1",
        STYLES: "",
        LAYERS: "vnm_adm1"
      }
    })
  });
  layerKhu_bao_ton = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      ratio: 1,
      url: "http://localhost:8081/geoserver/WebgisProject/wms?",
      params: {
        FORMAT: format,
        VERSION: "1.1.1",
        STYLES: "green",
        LAYERS: "khu_bao_ton"
      }
    })
  });

  river = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      ratio: 1,
      url: "http://localhost:8081/geoserver/WebgisProject/wms?",
      params: {
        FORMAT: format,
        VERSION: "1.1.1",
        STYLES: "",
        LAYERS: "river"
      }
    })
  });

  thuydien = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      ratio: 1,
      url: "http://localhost:8081/geoserver/WebgisProject/wms?",
      params: {
        FORMAT: format,
        VERSION: "1.1.1",
        STYLES: "",
        LAYERS: "hydropower_dams"
      }
    })
  });

  var viewMap = new ol.View({
    center: ol.proj.fromLonLat([mapLng, mapLat]),
    zoom: mapDefaultZoom
    //projection: projection
  });


  var overlay = new ol.Overlay({
    element: container,
    autoPan: true,
    autoPanAnimation: {
        duration: 250
    }
    });
    closer.onclick = function() {
        overlay.setPosition(undefined);
        closer.blur();
        return false;
    };



  map = new ol.Map({
    target: "map",
    layers: [layerBG, layerTPtinh],
    overlays: [overlay],
    view: viewMap
  });
  //map.getView().fit(bounds, map.getSize());

  //highlight
  var styles = {
    MultiPolygon: new ol.style.Style({
      fill: new ol.style.Fill({
        color: "orange"
      }),
      stroke: new ol.style.Stroke({
        color: "yellow",
        width: 2
      })
    }),
    Point: new ol.style.Style({
      stroke: new ol.style.Stroke({
        color: "yellow",
        width: 2
      })
    })
  };
  var styleFunction = function(feature) {
    return styles[feature.getGeometry().getType()];
  };
  var vectorLayer = new ol.layer.Vector({
    //source: vectorSource,
    style: styleFunction
  });
  map.addLayer(vectorLayer);




  function createJsonObj(result) {
    var geojsonObject =
      "{" +
      '"type": "FeatureCollection",' +
      '"crs": {' +
      '"type": "name",' +
      '"properties": {' +
      '"name": "EPSG:4326"' +
      "}" +
      "}," +
      '"features": [{' +
      '"type": "Feature",' +
      '"geometry": ' +
      result +
      "}]" +
      "}";
    return geojsonObject;
  }
  function drawGeoJsonObj(paObjJson) {
    var vectorSource = new ol.source.Vector({
      features: new ol.format.GeoJSON().readFeatures(paObjJson, {
        dataProjection: "EPSG:4326",
        featureProjection: "EPSG:3857"
      })
    });
    var vectorLayer = new ol.layer.Vector({
      source: vectorSource
    });
    map.addLayer(vectorLayer);
  }
  function highLightGeoJsonObj(paObjJson) {
    var vectorSource = new ol.source.Vector({
      features: new ol.format.GeoJSON().readFeatures(paObjJson, {
        dataProjection: "EPSG:4326",
        featureProjection: "EPSG:3857"
      })
    });
    vectorLayer.setSource(vectorSource);
    /*
            var vectorLayer = new ol.layer.Vector({
                source: vectorSource
            });
            map.addLayer(vectorLayer);
            */
  }
  function highLightObj(result) {
    //alert("result: " + result);
    var strObjJson = createJsonObj(result);
    //alert(strObjJson);
    var objJson = JSON.parse(strObjJson);
    //alert(JSON.stringify(objJson));
    //drawGeoJsonObj(objJson);
    highLightGeoJsonObj(objJson);
  }
  map.on("singleclick", function(evt) {
    //alert("coordinate: " + evt.coordinate);
    //var myPoint = 'POINT(12,5)';
    var lonlat = ol.proj.transform(evt.coordinate, "EPSG:3857", "EPSG:4326");
    var lon = lonlat[0];
    var lat = lonlat[1];
    var myPoint = "POINT(" + lon + " " + lat + ")";
    //alert("myPoint: " + myPoint);
    //*
    $.ajax({
      type: "POST",
      url: "CMR_pgsqlAPI.php",
      //dataType: 'json',
      data: { functionname: "getGeoCMRToAjax", paPoint: myPoint },
      success: function(result, status, erro) {
        highLightObj(result);
      },
      error: function(req, status, error) {
        alert(req + " " + status + " " + error);
      }
    });
    //*/
  });

  //ham lay thong tin
  function displayObjInfo(result, coordinate) {
    //alert("result: " + result);
    //alert("coordinate des: " + coordinate);
    $("#popup-content").html(result);
  }
  map.on("singleclick", function(evt) {
    //alert("coordinate org: " + evt.coordinate);
    //var myPoint = 'POINT(12,5)';
    var lonlat = ol.proj.transform(evt.coordinate, "EPSG:3857", "EPSG:4326");
    var lon = lonlat[0];
    var lat = lonlat[1];
    var myPoint = "POINT(" + lon + " " + lat + ")";
    //alert("myPoint: " + myPoint);
    //*
    $.ajax({
      type: "POST",
      url: "CMR_pgsqlAPI.php",
      //dataType: 'json',
      //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
      data: { functionname: "getInfoCMRToAjax", paPoint: myPoint },
      success: function(result, status, erro) {
        displayObjInfo(result, evt.coordinate);
        overlay.setPosition(evt.coordinate);
      },
      error: function(req, status, error) {
        alert(req + " " + status + " " + error);
      }
    });
  });
}
