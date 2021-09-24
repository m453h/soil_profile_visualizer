var map, mapPerspective, mapDataType;
var defaultSoilDataFields = ['soil_type', 'main_type', 'map_color','level'];

// control that shows state info on hover
var info = L.control();
var markers = undefined;

$(document).ready(function () {
    mapPerspective = 'region-data';
    renderMap('map-render', 'soil_profile_spatial_statistics',defaultSoilDataFields, 'country');
});

function renderMap(mapContainerId, route, fields, layerLevel) {

    if ($('#' + mapContainerId).length > 0)
    {
        mapDataType = mapContainerId;

        $('#' + mapContainerId).height($(window).height());

        map = L.map(mapContainerId, {
            center: L.latLng(-6.132, 35.092),
            zoom: 7
        });

        info.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info');
            this.update();
            return this._div;
        };

        info.update = function (props) {

            this._div.innerHTML = updateInformation(props);
        };

        info.addTo(map);

        fetchMapData(route, null, fields, layerLevel);
    }
}


function fetchMapData(route, value, fields, layerLevel) {
    $('#map-loader').show();
    $.ajax(Routing.generate(route), {
        data: {
            value: value
        },
        success: function (data) {
            sketchMapData(data, fields, layerLevel);
        },
        error: function () {
            alert('Failed');
        }
    });
}

function fetchTextData(e,route, latitude, longitude) {
    //$('#map-loader').show();
    console.log(latitude+','+longitude);
    $.ajax(Routing.generate('api_reverse_geocode'), {
        data: {
            latitude: latitude,
            longitude: longitude
        },
        success: function (data) {
            console.log(data);
            e.target.feature.properties.region = data.region;
            e.target.feature.properties.district = data.district;
            e.target.feature.properties.ward = data.ward;

            info.update(e.target.feature.properties);
        },
        error: function () {
            alert('Failed');
        }
    });
}

function sketchMapData(data, fields, layerLevel) {

    if (layerLevel === 'country')
    {
        //Remove existing map layers
        map.eachLayer(function (layer) {
            //If not the tile layer
            if (typeof layer._url === 'undefined') {
                map.removeLayer(layer);
            }
        });

    }
    //Create GeoJSON container object
    var geoJSON = {
        'type': 'FeatureCollection',
        'features': []
    };

    //Split data into features
    var dataArray = data.split(", ;");
    dataArray.pop();

    //build GeoJSON features
    dataArray.forEach(function (data) {
        //Split the data up into individual attribute values and the geometry
        data = data.split(", ");
        // console.log(data);
        //feature object container

        var feature = {
            'type': 'Feature',
            'properties': {}, //properties object container
            'geometry': JSON.parse(data[fields.length]) //parse geometry
        };

        for (var i = 0; i < fields.length; i++) {
            feature.properties[fields[i]] = data[i];
        }

        geoJSON.features.push(feature);
    });

   /* var mapDataLayer = L.geoJson(geoJSON, {
        pointToLayer: function (feature, point) {
            var markerStyle = {
                fillColor: '#CC9900',
                color: '#FFF',
                fillOpacity: 0.5,
                opacity: 0.8,
                weight: 1,
                radius: 8
            };

            return L.circleMarker(point, markerStyle);
        },
        onEachFeature: function (feature, layer) {

            layer.on({
                mouseover: highlightFeature,
                mouseout: resetHighlight,
                click: zoomToFeature
            });

            if (mapDataType === 'map-render') {
                //console.log(feature.properties);
                addMapInformationLayer(layer, feature.properties);
            }

        }

    });*/
    console.log(geoJSON);
    var vectorGrid = L.vectorGrid.slicer( geoJSON, {
        rendererFactory: L.canvas.tile,
        interactive: true,
        vectorTileLayerStyles: {
            'sliced': function(properties, zoom) {
               // console.log(properties);
                return {
                    fillColor:properties.map_color,
                    //fillOpacity: 0.5,
                    fillOpacity: 1,
                    stroke: true,
                    fill: true,
                    color: 'black',
                    //opacity: 0.2,
                    weight: 2
                }
            }
        }

    });

    vectorGrid.on("mouseover", function (e) {
        console.log("mouseover");
    });

    vectorGrid.on("click", function (e) {
      //  zoomToFeature(e.layer);
        console.log(e);
        //var marker = L.marker(e.latlng).addTo(map);
        addMarker(e);
        fetchTextData(e,'reverse_geocode',e.latlng.lat, e.latlng.lng);
        //console.log(e.layer.properties);
         var html = updateInformation(e.layer.properties);
          $('.info.leaflet-control').html(html);
        // addMapInformationLayer(e.layer,e.layer.properties);
    });


    vectorGrid.addTo(map);








    /*
    if (layerLevel === 'country')
    {
        mapDataLayer.addTo(map);
        map.fitBounds(mapDataLayer.getBounds());
    }
    */
    $('#map-loader').hide();
}

function highlightFeature(e) {
    var layer = e.target;

    layer.setStyle({
        weight: 2,
        color: '#ffffff',
        dashArray: '',
        fillOpacity: 0.4
    });

    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
        layer.bringToFront();
    }
}

function resetHighlight(e) {

    var layer = e.target;

    layer.setStyle({
        weight: 1,
        color: '#FFFFFF',
        dashArray: '',
        fillOpacity: 1,
        opacity: 1
    });

    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
        //layer.bringToFront();
    }

    //info.update();
}

function zoomToFeature(e) {

    map.fitBounds(e.target.getBounds());
    console.log(e.latlng);
    var layerLevel = e.target.feature.properties.level;
    console.log(e.target.feature.properties);
    var value = null;
    var route = null;
    var fields = [];
    //console.log(e.latlng.lat+" ,"+e.latlng.lng);
    addMarker(e);
   fetchTextData(e,'reverse_geocode',e.latlng.lat, e.latlng.lng);

  //  info.update(e.target.feature.properties);
}

function addMarker(e){
    if(markers!=undefined)
    {
        markers.remove();

    }
    // Add marker to map at click location; add popup window
    markers =  L.marker(e.latlng)
        .bindPopup('<strong>Science Hall</strong><br>Where the GISC was born.')
        .addTo(map)
        .openPopup();




}

function addMapInformationLayer(layer, properties) {
    for (var property in properties) {
        var html = '';
        var fillColor;
        var featureName = '';
        console.log(properties);

        html += property + ': ' + properties[property] + '<br>';

        if (typeof properties.map_color == 'undefined')
        {
            fillColor = '#919191';
        }
        else
        {
            fillColor = properties.map_color;
        }

        layer.setStyle({
            fillColor: fillColor,
            color: '#ffffff',
            transparent: true,
            weight: 0.6,
            opacity: 1,
            fillOpacity: 1
        });

        layer.bindTooltip(featureName, {
            permanent: true,
            direction: 'auto',
            offset: [6, -6],
            zoomAnimation: true
        });

        layer.bindPopup(html);
    }

    function addInformationBox(map){

    }
}

function updateInformation(props)
{
    var html ='<h2 class="styled-header">Soil Profile</h2>';

    if (typeof props === 'undefined')
    {
        html += '<p>Click on the map area, to view more details</p>';
    }
    else
    {
        var soilType = props.soil_type;
        var mainType = props.main_type;
        var region = props.region;
        var district = props.district;
        var ward = props.ward;

        var html ='<h2 class="styled-header">Soil Profile</h2>';

        html += getDetailElement('Type Code',soilType,'odd')
            + getDetailElement('Main Type',mainType,'even')
            + getDetailElement('Region',region,'odd')
            + getDetailElement('District',district,'even')
            + getDetailElement('Ward',ward,'odd');

        html ='<table>'+html+'</table>';
    }


    return html;
}

function getDetailElement(label,text,htmlClass){
    return '<tr class="'+htmlClass+'"><td><span class="info-label">'+label+':</span>'+'<td>'+text+'</td></tr>';
}