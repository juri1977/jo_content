$(document).ready(function(){
    init();
});

init = function() {

var container = document.getElementById('popup');
var content = document.getElementById('popup-content');
var closer = document.getElementById('popup-closer')

var json = './fileadmin/test.json';

var overlay = new ol.Overlay({
  element: container,
  autoPan: true,
  autoPanAnimation: {
    duration: 250
  }
});

var geojsonLayer = new ol.layer.Vector({
    source: new ol.source.Vector({
        url: json,
        format: new ol.format.GeoJSON()
    })
});

var source = new ol.source.Vector({
    url: json,
    format: new ol.format.GeoJSON()
});

var clusterSource = new ol.source.Cluster({
  distance: 50,
  source: source
});

var styleCache = {};

var clusters = new ol.layer.Vector({
  source: clusterSource,

  style: function(feature, resolution) {
  var size = feature.get('features').length;
  var style = styleCache[size];
  if (size > 1) {
    var color = 'orange';
  }
  else{
    var color = 'blue';
  }
    if (!style) {
      style = [new ol.style.Style({
        image: new ol.style.Circle({
          radius: 10,
          stroke: new ol.style.Stroke({
            color: '#fff'
          }),
          fill: new ol.style.Fill({
            color: color
          })
        }),
        text: new ol.style.Text({
          text: size.toString(),
          fill: new ol.style.Fill({
            color: '#fff'
          })
        })
      })];
      styleCache[size] = style;
    }
    return style;
  }
});

/**
 * Add a click handler to hide the popup.
 * @return {boolean} Don't follow the href.
 */
closer.onclick = function() {
  var view = map.getView();
          view.animate({
            zoom: 5
  });
  overlay.setPosition(undefined);
  closer.blur();
  return false;
};

var map = new ol.Map({
        target: 'mapdiv',
        layers: [
          new ol.layer.Tile({
            source: new ol.source.OSM()
          }),clusters   
         ],
         overlays: [overlay],       
          view: new ol.View({
          center: ol.proj.fromLonLat([11.100,50.500]),
          zoom: 5 
        })
      });

    map.on('singleclick', function(evt) {

      var feature = map.forEachFeatureAtPixel(evt.pixel,
      function(feature, layer) {
        return feature;
      });

      if (feature) { 
        cfeatures = feature.get('features');
        if (cfeatures.length == 1) {
          var geometry = feature.getGeometry();
          var coord = geometry.getCoordinates();
          
          var content_data = '<h3>' + cfeatures["0"].values_.name + '</h3>';

          content.innerHTML = content_data;
          overlay.setPosition(coord);

          var view = map.getView();
          view.animate({
            center: feature.getGeometry().getCoordinates(),
            zoom: 12
          }); 
        }
        else{
          var view = map.getView();
          view.animate({
            center: feature.getGeometry().getCoordinates(),
            zoom: map.getView().getZoom() + 1
          });
        }
      }
});
}
