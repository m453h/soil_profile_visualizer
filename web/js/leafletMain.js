let map, mapPerspective, mapDataType;
const defaultSoilDataFields = ['soil_type', 'main_type', 'map_color', 'level'];

// control that shows state info on hover
let info = L.control();
let markers = undefined;
let isFirstLoad = true;
let currentLatLng = undefined;

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
            center:  L.latLng(-6.132, 35.092),
            zoom: 6
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

        var positron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}.png', {
            attribution: "cartodbAttribution"
        }).addTo(map);

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
    // console.log(latitude+','+longitude);
    $.ajax(Routing.generate('api_reverse_geocode'), {
        data: {
            latitude: latitude,
            longitude: longitude
        },
        success: function (data) {
            // console.log(data);
            e.layer.properties.region = data.region;
            e.layer.properties.district = data.district;
            e.layer.properties.ward = data.ward;
            e.layer.properties.soil_type = data.soil_type;
            e.layer.properties.main_type = data.main_type;

            //info.update(e.target.feature.properties);
            var html = updateInformation(e.layer.properties);
            $('.info.leaflet-control').html(html);
        },
        error: function () {
            alert('Failed');
        }
    });
}

function sketchMapData(data, fields, layerLevel) {

    //Create GeoJSON container object
    let geoJSON = {
        'type': 'FeatureCollection',
        'features': []
    };

    //Split data into features
    let dataArray = data.split(", ;");
    dataArray.pop();

    //build GeoJSON features
    dataArray.forEach(function (data) {
        //Split the data up into individual attribute values and the geometry
        data = data.split(", ");
        let feature = {
            'type': 'Feature',
            'properties': {}, //properties object container
            'geometry': JSON.parse(data[fields.length]) //parse geometry
        };

        for (let i = 0; i < fields.length; i++) {
            feature.properties[fields[i]] = data[i];
        }

        geoJSON.features.push(feature);

    });

    let vectorGrid = L.vectorGrid.slicer(geoJSON, {
        rendererFactory: L.canvas.tile,
        interactive: true,
        vectorTileLayerStyles: {
            'sliced': function (properties, zoom) {
                // console.log(properties);
                let color = properties.map_color;

                if (properties.map_color === "")
                {
                    color = '#FFFFFF';
                }
                return {
                    fillColor: color,
                    fillOpacity: 0.8,
                    stroke: true,
                    fill: true,
                    color: 'black',
                    weight: 2
                }
            }
        }

    });

    vectorGrid.on("mouseover", function (e) {
        // console.log("mouseover");
    });

    vectorGrid.on("click", function (e) {
        addMarker(e);
        //console.log(e);
        fetchTextData(e,'reverse_geocode',e.latlng.lat, e.latlng.lng);
    });

    vectorGrid.on("load", function (e) {

    });


    vectorGrid.addTo(map);

    $('#map-loader').hide();
}
function addMarker(e){
    if(markers!==undefined)
    {
        markers.remove();
        currentLatLng = undefined;
    }

    // Add marker to map at click location; add popup window
    markers =  L.marker(e.latlng)
        .bindPopup('<strong>Science Hall</strong><br>Where the GISC was born.')
        .addTo(map)
        .openPopup();

    currentLatLng = e.latlng;
}

function addMapInformationLayer(layer, properties) {
    for (let property in properties) {
        let html = '';
        let fillColor;
        const featureName = '';
        //console.log(properties);

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
}

function updateInformation(props)
{
    let html = '<h2 class="styled-header">Soil Type</h2>';

    if (typeof props === 'undefined')
    {
        html += '<p>Click on the map area, to view more details</p>';
    }
    else
    {
        let soilType = props.soil_type;
        let mainType = props.main_type;
        let region = props.region;
        let district = props.district;
        let ward = props.ward;

        html = '<h2 class="styled-header">Soil Type</h2>';

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