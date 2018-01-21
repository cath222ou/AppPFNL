/**

  author: Olivier Dupras-Tessier
  copyright: (c) 2015-2016 Olivier Dupras-Tessier
  contact: olivier.dupras.tessier@gmail.com
  last-modified: 2016-04-25@21:45:00 UTC/GMT -4 hours 

  This page support all objecs need to implement ol3: 
    - map;
    - basemap; 
    - overlay; 
    - feature; 
    - format; 
    - style; 
    - control; 

  - CONSTRUCTOR: 
    constructor is a JSON which contains every layer, feature and style function and params; 
    It's build to response as a class and method object. 
    It's assume a JSON object as a parameter entry. 
    For example, you can call constructor.tile( layer{} ), 
      which provide an ol.layer.Tile object ready to be use in a map. 
    It's use all over index.html

  - SETTESTSTYLE: 
    setTestStyle is a function which is call as a ol.feature.stylefunction() in a vector layer. 
    It's return a JSON object which is use to predefine style or define new style
    It's assume a series of STRING object in parameters entry. 
    All of this are suppose to define the properties of each editing vector layer in the map. 
    By default, setTestStyle define seven layer style. 
    By default, setTestStyle is use by constructor.vector
      It's requiered for every new editing vector layer.  

*/




 

/**
 * JSON element: ol3 component list { layer, source, style }
 * @key { STING } 
 */
var constructor = { 


  /**
   * OL3 Tile layer constructor
   * @param { json{ type: STRING, opacity: REAL, visible: BOOL, source: STRING, name: STRING, url: STRING, params: json{  } } }
   * @return ol.layer.Tile(  ); 
   */
  tile: function( layer ) { 
    var result = new ol.layer.Tile( { 
      name: layer.name, 
      source: constructor[ layer.source ]( layer ), 
      opacity: layer.opacity,  
      visible: layer.visible
     } ); 
    return result; 
  }, 


  /**
   * OL3 Vector layer constructor
   * @param { json{ type: STRING, opacity: REAL, visible: BOOL, source: STRING, name: STRING, url: STRING, params: json{  } } }
   * @return ol.layer.Vector(  ); 
   */
  vector: function( layer ) { 
    var result = new ol.layer.Vector( { 
      name: layer.title, 
      source: constructor[ layer.source ]( layer ), 
      opacity: layer.opacity,  
      visible: layer.visible
    } ); 

    if ( layer.source == "geojson" ) {

      if ( layer.url !== undefined && window.cordova === undefined ) {
        $.get( layer.url, function( data ) { 
          var features = new ol.format.GeoJSON().readFeatures( data, { dataProjection: layer.epsg, featureProjection: "EPSG:3857"} ); 
          if ( layer.title.indexOf( "Feuillets 1:" ) != -1 ) { 
            var style = setGridFeatureStyle;         
            for ( var i in features ) {
              features[i].setStyle( style ); 
            } 
          } 
          result.getSource().addFeatures( features );
        } );

      } else {
        var features = new ol.format.GeoJSON().readFeatures( layer.data, { dataProjection: layer.epsg, featureProjection: "EPSG:3857"} ); 
        for ( var i in features ) {
          features[i].setProperties( { projectcolor: layer.color, name: features[i].get("name").replaceAll( "\&#39;", "\'" ) } ); 
        }

        var styleFunction = function ( feature ) {
          if ( !feature.getProperties().projectcolor ) {
            var rgb = $( ".project-table-info td#pType span" ).css("background-color"); 
            var hex = rgb2hex( rgb ); 
            feature.setProperties( {"projectcolor": hex} ); 
          } else { 
            var hex = ( !feature.getProperties().featurecolor ) ? feature.getProperties().projectcolor : feature.getProperties().featurecolor; 
          } 
          //var hex = ( !feature.getProperties().featurecolor ) ? feature.getProperties().projectcolor : feature.getProperties().featurecolor;
          var rgba = hex2rbga( hex, 0.2 ); 
          var yiq = getContrastYIQ( hex, "rgba" ); 
          var label = ( !feature.get( "name" ) ) ? " " : feature.get( "name" ); 
          var styles = setTestStyle( rgba, hex, yiq, label ); 
          return styles[ feature.getProperties().status ]; 
        }

        result.setStyle( styleFunction ); 
        result.getSource().addFeatures( features );
      }

    }

    return result; 
  }, 


  /**
   * OL3 TileWMS source constructor
   * @param { json{ type: STRING, opacity: REAL, visible: BOOL, source: STRING, name: STRING, url: STRING, params: json{ LAYERS: STRING, FORMAT: STRING, TILED: BOOL, SERVER: STRING } } }
   * @return ol.source.TileWMS(  ); 
   */
  wms: function( layer ) { 
    var result = new ol.source.TileWMS( { 
      url: layer.url, 
      params: layer.params
    } );
    return result; 
  },


  tilewms: function( layer ) {
    var result = new ol.source.TileWMS( {
      url: layer.url, 
      serverType: layer.serverType, 
      params: layer.params, 
      tileGrid: layer.tileGrid
    } ); 

    return result; 
  }, 


  /**
   * OL3 MapQuest source constructor
   * @param { json{ type: STRING, opacity: REAL, visible: BOOL, source: STRING, name: STRING, layer: STRING } }
   * @return ol.source.MapQuest(  ); 
   */
  mapQuest: function( layer ) { 
    var result = new ol.source.MapQuest( { layer: layer.layer } )
    return result; 
  }, 


  /**
   * OL3 OSM source constructor
   * @param { json{ type: STRING, opacity: REAL, visible: BOOL, source: STRING, name: STRING, layer: STRING } }
   * @return ol.source.OSM(  ); 
   */
  osm: function( layer ) { 
    var result = new ol.source.OSM(  ); 
    return result; 
  }, 


  /**
   * OL3 vector source constructor
   * @param { json{ type: STRING, opacity: REAL, visible: BOOL, source: STRING, name: STRING, url: STRING, params: json{ LAYERS: STRING, FORMAT: STRING } } }
   * @return ol.source.Vector(  ); 
   */
  geojson: function( layer ) { 
    var result = new ol.source.Vector( { 
      format: new ol.format.GeoJSON(  ), 
      name: layer.title
    } );  
    return result; 
  }, 


  /**
   * OL3 style constructor
   * @param { json{ image:json{ type: STRING, stroke: STRING, fill: STRING }, stroke: STRING, fill: STRING, text: STRING } }
   * @return ol.style.Style(  ); 
   */
  style: function( style ) { 
    var results = new ol.style.Style( { 
      fill: ( style.fill !== undefined ) ? constructor[ 'fill' ]( style.fill ) : undefined, 
      stroke: ( style.stroke !== undefined ) ? constructor[ 'stroke' ]( style.stroke ) : undefined, 
      image: ( style.image !== undefined ) ? constructor[ style.image.image ]( style.image ) : undefined, 
      text: ( style.text !== undefined ) ? constructor[ 'label' ]( style.text ) : undefined, 
      zIndex: ( style.zIndex !== undefined ) ? style.zIndex : undefined
     } ); 
    return results 
  }, 


  icon: function( icon ) {
    var results = new ol.style.Icon(({
      anchor: ( icon.anchor === undefined ) ? [ 0.5, 0.5 ] : icon.anchor, 
      anchorXUnits: 'fraction',
      anchorYUnits: 'pixels',
      opacity: ( icon.opacity === undefined ) ? 0.75 : icon.opacity,
      src: ( [ document.URL.substring( 0, document.URL.lastIndexOf("/") ), "dist/img", icon.data ] ).join( "/" )
    })); 

    return results; 
  }, 


  /**
   * OL3 text constructor
   * @param { text: STRING, stroke: STRING, fill: STRING } }
   * @return ol.style.Text(  ); 
   */
  label: function( label ) { 
    var results = new ol.style.Text( { 
      text: label.text,
      baseline: ( label.baseline === undefined ) ? "bottom" : label.baseline, 
      offsetY: ( label.offsetY === undefined ) ? -15 : label.offsetY, 
      fill: constructor[ 'fill' ]( label.fill ), 
      stroke: constructor[ 'stroke' ]( label.stroke )
    } );

    return results;     
  }, 


  /**
   * OL3 image constructor
   * @param { json{ stroke: STRING, fill: STRING } }
   * @return ol.style.Circle(  ); 
   */
  circle: function( circle ) { 
    var results = new ol.style.Circle( { 
      radius: ( circle.radius === undefined ) ? 7 : circle.radius, 
      fill: constructor[ 'fill' ]( circle.fill ), 
      stroke: constructor[ 'stroke' ]( circle.stroke )
    } ); 
    return results; 
  }, 


  /**
   * OL3 image constructor
   * @param { STRING }
   * @return ol.style.Stroke(  ); 
   */
  stroke: function( stroke ) { 
    var results = new ol.style.Stroke( { 
      color: stroke.color,
      width: ( stroke.width === undefined ) ? 2 : stroke.width, 
      lineDash: ( stroke.lineDash === undefined ) ? undefined : stroke.lineDash 
     } ); 
    return results; 
  }, 


  /**
   * OL3 image constructor
   * @param { STRING }
   * @return ol.style.Fill(  ); 
   */
  fill: function( fill ) { 
    var results = new ol.style.Fill( { 
      color: fill.color
     } ); 
    return results; 
  }
}; 


/** 
 * LAYERS
 */
var vector={}, 
    layers=[], 
    basemap=[ /* basemap doit demeurer unique dans l'attente d'une solution à "basemap & overlay" d'ol3 */
      { type: 'tile', opacity:1, visible:true, source:'osm', name:'osm', layer: 'osm' }, 
    ], 
    overlays=[ 
      { type:'tile', opacity:0.7, visible:false, source:'wms', name:'nhn:hydrography', url:'http://ows.geobase.ca/wms/geobase_fr', params:{ LAYERS: 'nhn:hydrography',FORMAT: 'image/png' } }, 
      { type:'tile', opacity:0.7, visible:false, source:'wms', name:'nrn:roadnetwork', url:'http://ows.geobase.ca/wms/geobase_fr', params:{ LAYERS: 'nrn:roadnetwork',FORMAT: 'image/png' } }, 
      { type:'tile', opacity:1, visible:false, source:'tilewms', serverType:'mapserver', name:'aqrp:che_for', url: url.file( url.cgi(), "aqrplus.map&" ), params:{ LAYERS:'che_for_01', FORMAT:'image/png', VERSION:'1.0.0', TILED:true }, tileGrid: tileGrid } 
    ]; 

// OSM 
for ( i in basemap ) { 
  var type=basemap[ i ].type; 
  layers.push( constructor[ type ]( basemap[ i ] ) ); 
}

// Overlays
for ( i in overlays ) { 
  var type=overlays[ i ].type; 
  layers.push( constructor[ type ]( overlays[ i ] ) ); 
}


// Extent of the map in units of the projection (these match our base map)
var extent = [ -3276800, -3276800, 3276800, 3276800 ];

// Fixed resolutions to display the map at (pixels per ground unit (meters when
// the projection is British National Grid))
var resolutions = [ 1600, 800, 400, 200, 100, 50, 25, 10, 5, 2.5, 1, 0.5, 0.25, 0.125, 0.0625 ];

// Define a TileGrid to ensure that WMS requests are made for
// tiles at the correct resolutions and tile boundaries
var tileGrid = new ol.tilegrid.TileGrid( {
    origin: extent.slice( 0, 2 ),
    resolutions: resolutions
} ); 


function test_map() {
  map.getLayers().forEach( function( layer ) {
    var tmp = 0; 
  } ); 
} 

/*
 * MAP
 */
var map = new ol.Map( { 
  target: 'map', 
  controls: ol.control.defaults({
    attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
      collapsible: false
    })
  }).extend([
    new ol.control.ScaleLine()
  ]),
  layers: layers,
  view: new ol.View( { 
    center: ol.proj.transform( [ -72.5, 48.75 ], 'EPSG:4326', 'EPSG:3857' ), 
    zoom: 9
  } )
} ); 
if ( window.cordova === undefined ) {
  var featureOverlay = new ol.FeatureOverlay( { map:map } ),  
      highlight; 
}


function setTestStyle( rgba, hex, yiq, text ) {
  var results = {}; 
  var style = [{ 
    name: "defaut", style: [
      { fill: { color: rgba }, stroke: { color: "#ffffff", width: 4 }, image: { image:'circle', fill: { color: hex }, stroke: { color: "#ffffff", width: 1 } }, text: { text: text, stroke: { color: yiq["stroke"] }, fill: { color: yiq["fill"] } }, zIndex: 1000 }, 
      { stroke: { color: hex, width: 2 }, zIndex: 1000 }
    ] 
  },{  
    name: "achat", style: [ 
      { image: { image:"icon", anchor: [ 0.5, 16 ], data: "lokas_software/shopping_cart.png" }, text: { text: "Poste d'achat", stroke: { color: "#000000" }, fill: { color: "#ffffff" }, offsetY: -16 }, "zIndex": 1000 } 
    ] 
  },{ 
    name: "collecte", style: [ 
      { image: { image:"circle", fill: { color: "#336699" }, stroke: { color: "#339966" }, radius: 10 }, text: { text: "Point de vente", stroke: { color: "#000000" }, fill: { color: "#ffffff" } }, "zIndex": 1000 } 
    ] 
  },{ 
    name: "chemin", style: [
      { stroke: { color: "#000000", width: 5 }, text: { text: "Chemin d'accès", stroke: { color: "#000000" }, fill: { color: "#ffffff" } }, zIndex: 1000 }, 
      { stroke: { color: "#ffffff", width: 2, lineDash: [ 6, 6, 2, 4 ] }, zIndex: 1000 } 
    ]
  },{ 
    name: "validation", style: [
      { image: { image:"circle", fill: { color: "#ff5959" }, stroke: { color: "#ff2929" }, radius: 10 }, text: { text: "Validation", stroke: { color: "#000000" }, fill: { color: "#ffffff" } }, "zIndex": 1000 } 
    ]
  },{ 
    name: "site", style: [
      { fill: { color: "rgba( 35, 100, 56, 0.2 )" }, stroke: { color: "#236438", width: 4 }, text: { text: "Site d'exploitation", stroke: { color: "#000000" }, fill: { color: "#ffffff" } }, "zIndex": 1000 }, 
      { stroke: { color: "#7ECC97", width: 2}, zIndex: 1000 } 
    ] 
  },{ 
    name: "position", style: [
      { image: { image:"icon", anchor: [ 0.5, 16 ], data: "google_material_design_icons/my_location.png" }, text: { text: "Position actuelle", stroke: { color: "#000000" }, fill: { color: "#ffffff" }, offsetY: -16 }, "zIndex": 1000 } 
    ]
  },{ 
    name: "suivi", style: [
      { stroke: { color: "#000000", width: 5 }, text: { text: "Suivi", stroke: { color: "#000000" }, fill: { color: "#ffffff" } }, zIndex: 1000 }, 
      { stroke: { color: "#ff0000", width: 2, lineDash: [ 6, 6, 2, 4 ] }, zIndex: 1000 } 
    ]
  }]; 

  for ( var i in style ) {
    results[ style[i].name ] = []; 
    for ( var j in style[i].style ) {
      results[ style[i].name ].push( constructor[ "style" ]( style[i].style[j] ) ); 
    }
  }

  return results; 
} /* setTestStyle() */






