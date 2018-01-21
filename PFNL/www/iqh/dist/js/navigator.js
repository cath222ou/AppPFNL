/**

  author: Olivier Dupras-Tessier
  copyright: (c) 2015-2016 Olivier Dupras-Tessier
  contact: olivier.dupras.tessier@gmail.com
  last-modified: 2016-04-25@21:45:00 UTC/GMT -4 hours 

	This page support every function needed to communicate with the phone: 
		- GPS; 
		- ACCELEROMETER. 

	- GETCURRENTPOSITION:
		Retrun a position object which contains accuracy and coordinates. 
		It's possible to implement different accuracy method directly in the core (success) of this function. 
		As in the urban environnement, the application assume accuracy less of 10m, depending of the medias supported
		If the mean accuray is highter than 10m, I strongly recommand to define a new method to overpass the wrong positionning
			It that case, use a similar function as starttracking() to get a position in a row and stop the positionning only when accuracy is under. 

	- STARTTRACKING: 
		It's callback function which return a continius flow of position while the WATCHID isn't clear. 
		The tracking stop only when $( ".btn-box-tool#position[name='LineString']" ).attr( "status" ) == "stop"
		In fact, this function build a linestring as long as the user don't ask to stop or pause it. 
		It about creating a tracking device to fallow the user motion by GPS. 
		Each position come with accuracy and coordinates. 
		It's possible to implement the same method to externate the position with a lack of positionning.  

*/



/**
 * Get the current position of your device
 * @params { --- }
 * @return { --- }
 */
function getCurrentPosition( source, fid, type ) {

	var options = {
		enableHighAccuracy: true,
		timeout: 5000,
		maximumAge: 0
	};


	/**
	 * Show current position
	 * @params { dict() }
	 * @return { --- }
	 */
	function success( position ) {
		var geojson = { "type": "Feature", "geometry": { "type": "Point", "coordinates": [ position.coords.longitude, position.coords.latitude ] }, "properties": { "status": "position", "name": "Position actuelle" } }
		var	feature = new ol.format.GeoJSON().readFeature( geojson, { dataProjection: "EPSG:4326", featureProjection: "EPSG:3857"} ); 
		
		feature.setId( fid+=1 ); 
		source.addFeature( feature ); 
		zoom2layer( feature, "feature" ); 
		$( ".stop-interaction" ).click(); 

	} /* success() */


	/** 
	 * Error message
	 * @params { STING }
	 * @return { --- }
	 */
	function error( err ) {
	  console.warn('ERROR(' + err.code + '): ' + err.message);
	} /* error() */


	//Geolocation.getCurrentPosition( successCallback, errorCallback )
	navigator.geolocation.getCurrentPosition( success, error, options ); 


} /* getCurrentPosition() */ 


/**
 * Watch position of your device on time delay
 * @params { --- }
 * @return { --- }
 */
function startTracking( source, fid ) {
	var results; 
	var tmp_coord = { latitude : 0, longitude: 0  }; 
	var geojson = { "type": "Feature", "geometry": { "type": "LineString", "coordinates": [] }, "properties": { "status": "suivi", "name": "Suivi des de&#769placements" } }
	var feature = new ol.format.GeoJSON().readFeature( geojson, { dataProjection: "EPSG:4326", featureProjection: "EPSG:3857"} ); 
	fid+=1; 
	feature.setId( fid ); 
	source.addFeature( feature ); 
	$( ".stop-interaction" ).click(); 


	/**
	 * Show current position
	 * @params { dict() }
	 * @return { --- }
	 */
	function success( position ) {
		// If status in on "pause", then do not add a new LineString's node 
		if ( $( ".btn-box-tool#position[name='LineString']" ).attr( "status" ) == "play" ) {
			// Add a new node to the tracking LineString
			if ( tmp_coord.latitude != position.coords.latitude && tmp_coord.longitude != position.coords.longitude ) { 
				var coordinates = ol.proj.transform( [ position.coords.longitude, position.coords.latitude ], 'EPSG:4326', 'EPSG:3857' ); 
				tmp_coord.latitude = position.coords.latitude; 
				tmp_coord.longitude = position.coords.longitude; 

				zoom2layer( coordinates, "coordinates" ); 
				feature.getGeometry().appendCoordinate( coordinates ); 
			}
		}

		// Stop tracking
		if ( $( ".btn-box-tool#position[name='LineString']" ).attr( "status" ) == "stop" ) {
			navigator.geolocation.clearWatch( watchID );
		}

	} /* success() */


	/** 
	 * Error message
	 * @params { STING }
	 * @return { --- }
	 */
	function error( err ) {
	  console.warn('ERROR(' + err.code + '): ' + err.message);
	} /* error() */


	// Options: throw an error if no update is received every 15 seconds.
	watchID = navigator.geolocation.watchPosition( success, error, { timeout: 5000 } );

} /* setTrackingLineString() */

