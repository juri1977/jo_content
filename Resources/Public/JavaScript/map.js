$(document).ready(() => {
    init();
});

init = () => {
 	if (typeof clusters != 'undefined') {
		var container = document.getElementById('popup');
		var content = document.getElementById('popup-content');
		var closer = document.getElementById('popup-closer')

		var overlay = new ol.Overlay({
			element: container,
			autoPan: true,
			autoPanAnimation: {
				duration: 250
			}
		});

		if (pointcolor == '') {pointcolor = 'red';}
		if (clustercolor == '') {clustercolor = 'red';}
		if (linecolor == '') {linecolor = 'red';}
		if (linewidth == '') {linewidth = 1;}

        var linesource = new ol.source.Vector({
        	features: (new ol.format.GeoJSON()).readFeatures(lines, {featureProjection:"EPSG:3857"})
        });

        var linesLayer = new ol.layer.Vector({
        	source: linesource,
        	style: new ol.style.Style({
				stroke: new ol.style.Stroke({
					color: linecolor,
					width: linewidth
				}),
          	})
        });


        var pointsource = new ol.source.Vector({
            features: (new ol.format.GeoJSON()).readFeatures(clusters, {featureProjection:"EPSG:3857"})
        });

        var pointLayer = new ol.source.Cluster({
			distance: 20,
			source: pointsource,
			style: new ol.style.Style({
				stroke: new ol.style.Stroke({
				}),
			})
        });


        var styleCache = {};
		var clusterLayer = new ol.layer.Vector({
        	source: pointLayer,
			style: (feature, resolution) => {
				var size = feature.get('features').length;
				var style = styleCache[size];
				if (size > 1) {
					var color = clustercolor;
				} else {
					var color = pointcolor;
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

    	closer.onclick = () => {
        	var view = map.getView();
			view.animate({
				zoom: 9
			});
			overlay.setPosition(undefined);
			closer.blur();
			return false;
    	};

		var map = new ol.Map({
			target: 'mapdiv',
			layers: [new ol.layer.Tile({source: new ol.source.OSM()}), linesLayer, clusterLayer],
			overlays: [overlay],       
			view: new ol.View({
				center: ol.proj.fromLonLat([37.73,37.56]),
				zoom: 9 
			})
		});

		map.on('singleclick', function(evt) {
			var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
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
				} else {
					var view = map.getView();
					view.animate({
					center: feature.getGeometry().getCoordinates(),
					zoom: map.getView().getZoom() + 1
					});
				}
			}
		});

		var getInput = (layer, id) => {
			var input = document.createElement('input');
			input.type = 'checkbox';
			input.checked = true;
			input.id = id;
			input.onchange = (e) => {
				layer.setVisible(e.target.checked);
			};
			return input;
		};
		
		var joLayerControl = function(opt_options) {
			var options = opt_options || {};

			var button = document.createElement('button');
			button.className = 'joLayerButton';
			button.innerHTML = 'L';

			var element = document.createElement('div');
			element.className = 'joLayerContainer ol-unselectable ol-control';
			element.appendChild(button);

			var layerContent = document.createElement('div');
			layerContent.className = 'joLayerContent';
			element.appendChild(layerContent);

			button.onclick = function() {
				layerContent.classList.toggle('active');
			};

			var ul = document.createElement('ul');
			var tmplayers = map.getLayers().getArray();
			for (var i = 1; i < tmplayers.length; i++) {
				if (tmplayers[i].get('title') && tmplayers[i].get('title') == 'polygone') {
					continue;
				}
				var id = 'Layer ' + (i);
				var li = document.createElement('li');
				var input = getInput(tmplayers[i], id);
				var label = document.createElement('label');
				label.htmlFor = id;
				label.innerHTML = tmplayers[i].get('title') ? tmplayers[i].get('title') : id;
				li.appendChild(input);
				li.appendChild(label);
				ul.appendChild(li);
			}
			layerContent.appendChild(ul);
			ol.control.Control.call(this, {
				element: element,
				target: options.target
			});
		};
		ol.inherits(joLayerControl, ol.control.Control);
		map.addControl(new joLayerControl());
	}
}
