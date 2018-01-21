//////Définition des couches///////////
	
	
	//////////// Basemap /////////////////
	// var ImageryBasemap = new ol.layer.Tile({source: new ol.source.XYZ({	url: 'http://api.tiles.mapbox.com/v4/alexandrecaron.llljl7kh/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYWxleGFuZHJlY2Fyb24iLCJhIjoiTlR2V0J5OCJ9.ynXXZ7pn3GV3Zk9hczwtTg'})	});
	var ImageryMapbox = new ol.layer.Tile({source: new ol.source.XYZ({maxZoom:17,	url: 'http://api.tiles.mapbox.com/v4/alexandrecaron.llljl7kh/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYWxleGFuZHJlY2Fyb24iLCJhIjoiTlR2V0J5OCJ9.ynXXZ7pn3GV3Zk9hczwtTg', }), name: 'Imagerie Mapbox'});
			ImageryMapbox.setVisible(false);
			
	var EsriTopo = new ol.layer.Tile({source: new ol.source.XYZ({maxZoom:18, attributions: [new ol.Attribution({html: 'Tiles &copy; <a href="http://services.arcgisonline.com/ArcGIS/' + 'rest/services/World_Topo_Map/MapServer">ArcGIS</a>'})], url: 'http://server.arcgisonline.com/ArcGIS/rest/services/' +'World_Topo_Map/MapServer/tile/{z}/{y}/{x}', }), name: 'Topographie ESRI'});
			EsriTopo.setVisible(false);
		
	var EsriImagery = new ol.layer.Tile({source: new ol.source.XYZ({maxZoom:17,	attributions: [new ol.Attribution({html: 'Tiles &copy; <a href="http://services.arcgisonline.com/ArcGIS/' + 'rest/services/World_Imagery/MapServer">ArcGIS</a>'})],	url: 'http://server.arcgisonline.com/ArcGIS/rest/services/' +'World_Imagery/MapServer/tile/{z}/{y}/{x}', }), name: 'Imagerie ESRI'});
			EsriImagery.setVisible(false);

//OSM	
    var OSM = new ol.layer.Tile({
        source : new ol.source.OSM()
    }
    );
    OSM.setVisible(true);

    var Groupe_OpenData = new ol.layer.Group({layers: [OSM], name: 'CouchefondOpen'});

    
	
	var Groupe_Basemap = new ol.layer.Group({layers: [ImageryMapbox, EsriTopo, EsriImagery], name: 'Couchefond'});
	
	
	//////////// PFNL /////////////////

   
	var iqh1_20 = new ol.layer.Vector({
      source: new ol.source.GeoJSON({
                url: "data/iqh/iqh3/grille20/iqh3.geojson",
                projection: 'EPSG:3857'
                }),
      title:"IQH Grille 20",
      minResolution: 100,
      maxResolution: 1000,
      style: setGridFeatureStyle
    });
    iqh1_20.setVisible(true);

    var iqh1_50 = new ol.layer.Vector({
      source: new ol.source.GeoJSON({
                url: "data/iqh/iqh3/grille50/iqh3.geojson",
                projection: 'EPSG:3857'
                }),
      title:"IQH Grille 50",
      minResolution: 1000,
      maxResolution: 2000,
      style: setGridFeatureStyle
    });
    iqh1_50.setVisible(true);

    var iqh1_250 = new ol.layer.Vector({
      source: new ol.source.GeoJSON({
                url: "data/iqh/iqh3/grille250/iqh3.geojson",
                projection: 'EPSG:3857'
                }),
      title:"IQH Grille 250",
      minResolution: 2000,
      style: setGridFeatureStyle
    });
    iqh1_250.setVisible(true);

    var iqh1_WMS = new ol.layer.Tile({source: new ol.source.TileWMS({
                url: 'http://igeomedia.com/cgi-bin/mapserv?map=/home/odupras/public_html/data/iqh.map&',
                params: {LAYERS: 'iqh3', VERSION: '1.1.1'}
            }),
        maxResolution: 200,
        });
    iqh1_WMS.setVisible(true);
		
	var Groupe_IQH1 = new ol.layer.Group({layers: [iqh1_20,iqh1_50,iqh1_250,iqh1_WMS], name: 'iqh1'});
	Groupe_IQH1.setVisible(false);

	//////////// iqh2 /////////////////

	var iqh2_20 = new ol.layer.Vector({
      source: new ol.source.GeoJSON({
                url: "data/iqh/iqh8/grille20/iqh8.geojson",
                projection: 'EPSG:3857'
                }),
      title:"IQH Grille 20",
      minResolution: 100,
      maxResolution: 1000,
      style: setGridFeatureStyle
    });
    iqh2_20.setVisible(true);

    var iqh2_50 = new ol.layer.Vector({
      source: new ol.source.GeoJSON({
                url: "data/iqh/iqh8/grille50/iqh8.geojson",
                projection: 'EPSG:3857'
                }),
      title:"IQH Grille 50",
      minResolution: 1000,
      maxResolution: 2000,
      style: setGridFeatureStyle
    });
    iqh2_50.setVisible(true);

    var iqh2_250 = new ol.layer.Vector({
      source: new ol.source.GeoJSON({
                url: "data/iqh/iqh8/grille250/iqh8.geojson",
                projection: 'EPSG:3857'
                }),
      title:"IQH Grille 250",
      minResolution: 2000,
      style: setGridFeatureStyle
    });
    iqh2_250.setVisible(true);

    var iqh2_WMS = new ol.layer.Tile({source: new ol.source.TileWMS({
                url: 'http://igeomedia.com/cgi-bin/mapserv?map=/home/odupras/public_html/data/iqh.map&',
                params: {LAYERS: 'iqh8', VERSION: '1.1.1'}
            }),
        maxResolution: 200,
        });
    iqh2_WMS.setVisible(true);
		
	var Groupe_IQH2 = new ol.layer.Group({layers: [iqh2_20,iqh2_50,iqh2_250,iqh2_WMS], name: 'iqh2'});
	Groupe_IQH2.setVisible(false);
	
    ////////// Météo ////////////////
    var precipitation_6h = new ol.layer.Tile({name: 'Precipitation 6h', source: new ol.source.TileWMS({url: 'http://geo.weather.gc.ca/geomet/',params: {'LAYERS': 'RDPA.6P_PR','TILED': true}, })});
		precipitation_6h.setVisible(false);

    var radar_rain = new ol.layer.Tile({name: 'Radar/rain', source: new ol.source.TileWMS({url: 'http://geo.weather.gc.ca/geomet/',params: {'LAYERS': 'RADAR_RRAI','TILED': true}, })});
		radar_rain.setVisible(false);

    var temperature = new ol.layer.Tile({name: 'Temperature', source: new ol.source.TileWMS({url: 'http://geo.weather.gc.ca/geomet/',params: {'LAYERS': 'HRDPS.CONTINENTAL_TT','TILED': true}, })});
		temperature.setVisible(false);
        temperature.setOpacity(0.7);

    var Groupe_Meteo = new ol.layer.Group({layers:[precipitation_6h,radar_rain,temperature], name: 'Couche meteo'});
    Groupe_Meteo.setVisible(false);
    
    ////////// Topo ////////////////
	var hydro = new ol.layer.Tile({source: new ol.source.TileWMS({
                url: 'http://ows.geobase.ca/wms/geobase_fr',
                params: {LAYERS: 'nhn:hydrography', VERSION: '1.1.1'}
            })
        });
    hydro.setVisible(false);

    var route = new ol.layer.Tile({source: new ol.source.TileWMS({
                url: 'http://ows.geobase.ca/wms/geobase_fr',
                params: {LAYERS: 'nrn:roadnetwork', VERSION: '1.1.1'}
            })
        });
    route.setVisible(false);

    var chemin = new ol.layer.Tile({source: new ol.source.TileWMS({
                url: 'http://igeomedia.com/cgi-bin/mapserv?map=/home/odupras/public_html/data/aqrplus.map&',
                params: {LAYERS: 'che_for_01', VERSION: '1.1.1'}
            })
        });
    chemin.setVisible(false);
	
    var Groupe_Topo = new ol.layer.Group({layers:[hydro,route,chemin], name: 'Couche topo'});
    Groupe_Topo.setVisible(false);

    
	var view = new ol.View({
		  center: ol.proj.transform([lonCenter, latCenter], 'EPSG:4326', 'EPSG:3857'),
          projection: ol.proj.get('EPSG:3857'),
		  zoom: zoomCenter
		});
	
//////Définition de la carte////////////////
	var map = new ol.Map({
		target: 'map',
		renderer: 'canvas',
		layers: [Groupe_OpenData,Groupe_Basemap, Groupe_IQH1, Groupe_IQH2,Groupe_Meteo, Groupe_Topo],
		view: view,
		controls: ol.control.defaults().extend([
			new ol.control.FullScreen(),
			new app.RotateNorthControl(),
			new ol.control.ScaleLine(),
			// new ol.control.ZoomSlider(),
			// mousePositionControl,
		]),
		interactions: ol.interaction.defaults().extend([
			new ol.interaction.DragRotateAndZoom()
		  ]),
		});
		

function setGridFeatureStyle(feature,resolution) {
	var style; 
	var production = feature.get('Production'); 
    
	if ( production == 0 || production <= 20 ) { 
		style = new ol.style.Style({ fill: new ol.style.Fill({ color: 'rgba( 254, 229, 217, 0.5 )' }), stroke: new ol.style.Stroke({ color: 'rgba( 0, 0, 0, 0.1 )' })}); 
	} else if ( production > 20 || production <= 40 ) {
		style = new ol.style.Style({ fill: new ol.style.Fill({ color: 'rgba( 252, 174, 145, 0.5 )' }), stroke: new ol.style.Stroke({ color: 'rgba( 0, 0, 0, 0.1 )' })}); 
	} else if ( production > 40 || production <= 60 ) {
		style = new ol.style.Style({ fill: new ol.style.Fill({ color: 'rgba( 251, 106, 74, 0.5 )' }), stroke: new ol.style.Stroke({ color: 'rgba( 0, 0, 0, 0.1 )' })}); 
	} else if ( production > 60 || production <= 80 ) {
		style = new ol.style.Style({ fill: new ol.style.Fill({ color: 'rgba( 222, 45, 38, 0.5 )' }), stroke: new ol.style.Stroke({ color: 'rgba( 0, 0, 0, 0.1 )' })}); 
	} else if ( production > 80 || production <= 100 ) {
		style = new ol.style.Style({ fill: new ol.style.Fill({ color: 'rgba( 165, 15, 21, 0.5 )' }), stroke: new ol.style.Stroke({ color: 'rgba( 0, 0, 0, 0.1 )' })}); 
	}

	return [style]; 
} /** setGridFeatureStyle() */
	
