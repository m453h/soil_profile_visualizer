var map, districtLayer, wardLayer, mapPerspective, mapDataType;
var defaultDataFields = ["region_name", "region_code", "level"];
var defaultDistrictFields = ["region_name","district_name", "district_code", "level", "results", 'active_cases', 'recovered_cases', 'fatal_cases'];
var defaultWardFields = ["region_name","ward_name", "ward_code", "level", "results", 'active_cases', 'recovered_cases', 'fatal_cases'];

var defaultSoilDataFields = ['soil_type', 'main_type', 'map_color','level'];

// control that shows state info on hover
var info = L.control();



$(document).ready(function () {

    mapPerspective = 'region-data';
    $('#region-data').addClass('active');

    $('.map-menu-item').click(function () {

        mapPerspective = $(this).attr('id');

        $(this).addClass('active');

        if (mapPerspective === 'region-data')
        {
            $('#district-data').removeClass('active');
            $('#ward-data').removeClass('active');
        }
        else if (mapPerspective === 'district-data')
        {
            $('#region-data').removeClass('active');
            $('#ward-data').removeClass('active');
        }
        else if (mapPerspective === 'ward-data')
        {
            $('#district-data').removeClass('active');
            $('#region-data').removeClass('active');
        }

        //Clear the map and reload
        map.remove();
        renderMap('map-render', 'region_spatial_statistics',defaultDataFields, 'country');
    });

    $('.ward-key').hide();
    $('.region-key').hide();
    $('.constituency-key').hide();


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

            this._div.innerHTML = updateStatistics(props);
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

    var mapDataLayer = L.geoJson(geoJSON, {
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

    });

    if (layerLevel === 'country')
    {
        mapDataLayer.addTo(map);
        map.fitBounds(mapDataLayer.getBounds());
    }
    else if (layerLevel === 'region')
    {
        districtLayer = mapDataLayer;
        districtLayer.addTo(map);
    }
    else if (layerLevel === 'district')
    {
        wardLayer = mapDataLayer;
        wardLayer.addTo(map);
    }

    $('#map-loader').hide();
}

function highlightFeature(e) {
    var layer = e.target;

    layer.setStyle({
        weight: 2,
        color: '#ffffff',
        dashArray: ''
        //fillOpacity: 0.4
    });

    info.update(layer.feature.properties);

    //refreshChart(layer.feature.properties);
    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
        layer.bringToFront();
    }

    //info.update(layer.feature.properties);
}

function resetHighlight(e) {

    $('.ward-key').hide();
    $('.region-key').hide();
    $('.district-key').hide();

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

    var layerLevel = e.target.feature.properties.level;
    console.log(e.target.feature.properties);
    var value = null;
    var route = null;
    var fields = [];

    if (mapDataType === 'map-render')
    {
        if (layerLevel === 'region')
        {
            if(mapPerspective=='region-data')
            {
                value = e.target.feature.properties.region_code;
                route = 'soil_profile_spatial_statistics';
                fields = defaultRegionSoilDataFields;
            }
            else
            {
                value = e.target.feature.properties.region_code;
                route = 'district_spatial_statistics';
                fields = defaultDistrictFields;
            }
        }
        else if (layerLevel === 'district')
        {
            value = e.target.feature.properties.district_code;
            route = 'ward_spatial_statistics';
            fields = defaultWardFields;
        }
    }

    if (map.hasLayer(districtLayer) && layerLevel === 'region')
    {
        map.removeLayer(districtLayer);
    }

    if (map.hasLayer(wardLayer))
    {
        map.removeLayer(wardLayer);
    }

    fetchMapData(route, value, fields, layerLevel);
}

function addMapInformationLayer(layer, properties) {
    for (var property in properties) {
        var html = "";
        var fillColor;
        var featureName = "";
        var totalCount = properties.active_cases;
        var recoveredCasesCount = properties.recovered_cases;
        var fatalCasesCount = properties.fatal_cases;
        var activeCasesCount = totalCount-recoveredCasesCount-fatalCasesCount;
        console.log(properties);

        html += property + ": " + properties[property] + "<br>";


        if (typeof properties.map_color == 'undefined')
        {
            fillColor = '#919191';
        }
        else
        {
            fillColor = properties.map_color;
        }

        /*if (typeof properties.region_name !== 'undefined')
        {
        }
        else if (typeof properties.district_name !== 'undefined')
        {
            fillColor = ;
        }
        else if (typeof properties.ward_name !== 'undefined')
        {
            fillColor = "#e74c3c";
        }*/

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

    function getFillColour(totalCount,activeCasesCount,recoveredCasesCount,fatalCasesCount)
    {
        fillColor = "#777777";
        console.log(mapPerspective+'activeCasesCount:'+activeCasesCount+' recoveredCasesCount:'+recoveredCasesCount+' fatalCasesCount:'+fatalCasesCount);

        if(mapPerspective==='total' && activeCasesCount>0)
        {
            fillColor = "#e74c3c";
        }
        else if (mapPerspective==='active' && activeCasesCount>0)
        {
            fillColor = "#f39c12";
        }
        else if (mapPerspective==='recovered' && recoveredCasesCount>0)
        {
            fillColor = "#16a085";
        }
        else if (mapPerspective==='fatal' && fatalCasesCount>0)
        {
            fillColor = "#8e44ad";
        }


        return fillColor;
    }

    function addInformationBox(map){

    }
}

function updateStatistics(props)
{
    var html ='<h2 class="styled-header">Summary</h2>';

    if (typeof props === "undefined")
    {
        html += '<p>Hover over a region, to view statistics</p>';
    }
    else
    {
        var totalActiveCases = props.active_cases;
        var totalRecoveredCases = props.recovered_cases;
        var totalFatalCases = props.fatal_cases;
        var regionName = props.region_name;

        var html ='<h2 class="styled-header">Summary ('+regionName+')</h2>';

        html += getDetailElement('Active',totalActiveCases-totalFatalCases-totalRecoveredCases,'odd')
            + getDetailElement('Recovered',totalRecoveredCases,'even')
            + getDetailElement('Fatal',totalFatalCases,'odd')
            +getDetailElement('Total',totalActiveCases,'total');

        html ='<table>'+html+'</table>';
    }

    return html;
}

function getDetailElement(label,text,htmlClass){
    return '<tr class="'+htmlClass+'"><td><span class="info-label">'+label+':</span>'+'<td>'+text+'</td></tr>';
}