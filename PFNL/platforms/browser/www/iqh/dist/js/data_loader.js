/**

  author: Olivier Dupras-Tessier
  copyright: (c) 2015-2016 Olivier Dupras-Tessier
  contact: olivier.dupras.tessier@gmail.com
  last-modified: 2016-04-25@21:45:00 UTC/GMT -4 hours 


*/


/**
 * multiple DOM functions 
 * @return {url}; 
 */
var url = {
	cgi: function() {
		return [ "http://igeomedia.com", "cgi-bin/mapserv?map=/home/odupras/public_html/data" ].join( "/" )
	}, 
  root: function() {
    return [ "http://igeomedia.com", "~odupras" ].join( "/" ); 
  }, 
  data: function() {
    return [ url.root(), "data" ].join( "/" );  
  }, 
  process: function() {
    return [ url.root(), "process" ].join( "/" ); 
  }, 
  php: function() {
    return [ url.process(), "php" ].join( "/" ); 
  }, 
  python: function() {
  	return [ url.process(), "python" ].join( "/" ); 
  }, 
  file: function(fn, name) {
  	return [ fn, name ].join( "/" ); 
  }
}
var max=0;
/**
 * List JQuery append animation function
 * @params {JSON}
 */
var indentDiv = {
  replaceWith: function( obj ) {
    $( obj.last ).fadeOut( 500 )
      .replaceWith( $( obj.html ) )
      .fadeIn( 500 ); 
  }, 
  append: function( obj ) {
    $( obj.parent ).append( $( obj.html ) ); 
  },  
  prepend: function( obj ) {
    $( obj.parent ).prepend( $( obj.html ) )
      .hide( 0 )
      .fadeIn(); 
  }, 
  remove: function( obj ) {
  	$( obj.node ).remove(); 
  },
  show: function(obj){
    $(obj.node).show();  
  },
  link: function(obj){
    $(obj.node).attr("href",obj.info);  
  }, 
  append2: function( obj ) {
    $( obj.node ).append( $( obj.html ) ); 
  }    
}; 



/**
 * Create store <ul />
 * @params {$(elem), string}
 */
function createStore( elem, obj ) {
	var store_class = "store-"+obj; 
	var store = "<ul class='hidden "+store_class+"'/> "; 

	elem.append( store ); 	
} 


/**
 * add data <li /> to a store object
 * @params {string, json{}}
 */
function bindDataStore( obj, properties ) {
	var elem = $( ".store-" + obj ); 
	
	elem.append( "<li />" );
	for ( var key in properties ) {
		if ( typeof properties[key] != "object" ) {
			var text = $( "<div/>" ).html( properties[key] ).text();
			elem.children().last().attr( key, text ); 
		}
	} 
} 


/**
 * Check if the argument is an jquery object or not
 * @params {string/$(elem)}
 * @return {$(elem)}
 */
function elemType( obj ) {
	var results; 
	( obj instanceof jQuery ) ? results = obj : results = $( obj ); 

	return results
}


/**
 * Reset children <select/><option/>  
 * @params {string/$(elem), string}
 */
function resetSelectOption( obj, text ) {
	var elem = elemType( obj ); 

	elem.empty()
		.append( "<option style='display:none'>" + text + "</option>" ); 
}


/**
 * Reset forms <select/><option/>  
 * @params {string/$(elem)}
 */
function resetSelectForm( obj ) {
	var elem = elemType( obj ); 
	var online = ( $( ".user-panel" ).attr( "online" ) == "true" ) ? true : false; 

	elem.children()
		.each( function() {
			var child_id = $( this ).attr( "id" ); 
			var empty_text = $( this ).children().first().val(); 

			if ( child_id == "pfnl" ) {
				disabledEvt( this, false ); 
				bindLayersOption( child_id ); 
				$( "a.form-reset" ).toggleClass( "hidden" ); 
			} else { 
				if ( child_id == "iqh" && online ) layer2show( $( this ).val(), false ); 
				disabledEvt( this, true ); 
				resetSelectOption( this, empty_text ); 	
			}
		}); 

}


/**
 * EVENT: disabled/enabled element  
 * @params {string/$(elem), string}
 */
function disabledEvt( obj, bool ) {
	var elem = elemType( obj ); 

	elem.attr( 'disabled', bool )
		.trigger( "liszt:updated" ); 
}


/**
 * Implement <option /> from dataStore 
 * @params { string }
 */
function bindLayersOption( obj, lvl ) {

	if ( typeof obj == "object" ) {
		var parent = $( obj ); 
		var selected = $( "option:selected", obj ); 
		var obj = selected.attr( "child" ); 
		var lvl = selected.attr( "id" ); 
	}

	var store = $( ".store-" + obj ); 
	var combo = $( "select#" + obj ); 
	var empty_text = combo.children().first().val(); 

	// Preset comboBox
	resetSelectOption( combo, empty_text ); 

	// Create <select/><option/> and attributs
	store.children().each( function() {
		var properties = {}; 
		var name = $( this ).attr( "name" ); 
		var id = $( this ).attr( "id" ); 
		var option = "<option id='" + id + "'> " + name + "</option>"; 

		if ( typeof $( this ).attr( "child" ) != "undefined" ) properties.child = $( this ).attr( "child" ); 
		if ( typeof $( this ).attr( "group" ) != "undefined" ) properties.group = $( this ).attr( "group" );

		// Add attributs to the last <option/> element
		if ( ( lvl === null ) | ( lvl === properties.group ) ) {
			combo.append( option ); 
			for ( var key in properties ) {
				combo.children().last().attr( key, properties[key] ); 
			}

			// Only if the <select/> had child attributs
			disabledEvt( combo, false ); 
		}
	}); 

	// Disabled the parent <select/> element 
	if (parent !== undefined) {
		var reset = $( "a.form-reset" ); 
		if ( reset.hasClass( "hidden" ) ) reset.toggleClass( "hidden" ); 
		disabledEvt( parent, true ); 
	}
	
} /** bindLayersOption() */


/** 
 * Get each layer levels path 
 * @params { STRING }
 * @return { json{} }
 */
function postLayerPath( obj ) {

  if ( window.screen.width > 980 || window.cordova !== undefined ) {  
		var results = [
			{ type: "tile", opacity:0.7, visible:true, source: "tilewms", minRes: null, maxRes: 2250, serverType: "mapserver", name: "Polygones forestiers", url: url.file( url.cgi(), [ "iqh", "map&" ].join( "." ) ), params:{ LAYERS: obj, FORMAT: 'image/png', VERSION: '1.0.0', TILED: true }, tileGrid: tileGrid }, 
		  { type: "vector", opacity:0.7, visible:true, source: "geojson", minRes: 200, maxRes: 500, name: "grille20", title: "Feuillets 1:20\'000", url: url.file( url.data(), [ "iqh", obj, "grille20", [ obj, "geojson" ].join( "." ) ].join( "/" ) ) }, 
		  { type: "vector", opacity:0.7, visible:true, source: "geojson", minRes: 500, maxRes: 1200, name: "grille50", title: "Feuillets 1:50\'000", url: url.file( url.data(), [ "iqh", obj, "grille50", [ obj, "geojson" ].join( "." ) ].join( "/" ) ) }, 
		  { type: "vector", opacity:0.7, visible:true, source: "geojson", minRes: 1200, maxRes: 2250, name: "grille250", title: "Feuillets 1:250\'000", url: url.file( url.data(), [ "iqh", obj, "grille250", [ obj, "geojson" ].join( "." ) ].join( "/" ) ) }
		]
	}	else {
		var results = [
			{ type: "tile", opacity:0.7, visible:true, source: "tilewms", minRes: null, maxRes: 2250, serverType: "mapserver", name: "Polygones forestiers", url: url.file( url.cgi(), [ "iqh", "map&" ].join( "." ) ), params:{ LAYERS: obj, FORMAT: 'image/png', VERSION: '1.0.0', TILED: true }, tileGrid: tileGrid }, 
		]
	}

	return results; 

} /** postLayerPath( STRING ) */


/**
 * Define style of each grid feature load from GeoJSON location
 * @params { --- }
 * @return { array[ new ol.style.Style() ] }
 */
function setGridFeatureStyle() {
	var style; 
	var production = this.getProperties().Production; 
	if ( production == 0 || production <= 20 ) { 
		style = { fill: { color: 'rgba( 254, 229, 217, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' }, image: { image:'circle', fill: { color: 'rgba( 254, 229, 217, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' } }, zIndex: 1 }; 
	} else if ( production > 20 || production <= 40 ) {
		style = { fill: { color: 'rgba( 252, 174, 145, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' }, image: { image:'circle', fill: { color: 'rgba( 252, 174, 145, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' } }, zIndex: 1 }; 
	} else if ( production > 40 || production <= 60 ) {
		style = { fill: { color: 'rgba( 251, 106, 74, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' }, image: { image:'circle', fill: { color: 'rgba( 251, 106, 74, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' } }, zIndex: 1 }; 
	} else if ( production > 60 || production <= 80 ) {
		style = { fill: { color: 'rgba( 222, 45, 38, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' }, image: { image:'circle', fill: { color: 'rgba( 222, 45, 38, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' } }, zIndex: 1 }; 
	} else if ( production > 80 || production <= 100 ) {
		style = { fill: { color: 'rgba( 165, 15, 21, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' }, image: { image:'circle', fill: { color: 'rgba( 165, 15, 21, 0.5 )' }, stroke: { color: 'rgba( 0, 0, 0, 0.1 )' } }, zIndex: 1 }; 
	}

	return [ constructor['style'](style) ]; 
} /** setGridFeatureStyle() */


/**
 * Get a specific layer propertie
 * @params { STRING, STRING }
 * @return { BOOL }
 */
function getLayerGroup( obj, properties ) {
	var results = false; 
	map.getLayerGroup().getLayers().forEach( function( layer ) {
		if ( layer.get( "name" ) == obj ) {
			if ( properties == "exist" ) results = true; 
			if ( properties == "visible" ) results = layer.get( "visible" );  
		}
	} ); 

	return results; 
} /** getLayerGroup() */


/**
 * create and add new group of layers to .map
 * @params { DOM_element }
 * @return { --- }
 */
function setLayerGroup( elem ) {
	var iqh = $( elem ).val(); 
	var exist = getLayerGroup( iqh, "exist" ); 

	if ( $( ".user-panel" ).attr( "online" ) ) {
		if ( !exist ) { // create layerGroup if it isn't exist  
			var myData = postLayerPath( iqh ); 
			var myLayers = []; 

			for ( var i in myData ) { 
				var layer = constructor[ myData[i].type ]( myData[i] ); 
				if ( myData[i].minRes !== null ) layer.setMinResolution( myData[i].minRes ); 
				if ( myData[i].maxRes !== null ) layer.setMaxResolution( myData[i].maxRes ); 
				myLayers.push( layer ); 
			}

			var myGroup = new ol.layer.Group( { name:iqh, layers:myLayers } ); 
			map.getLayers().insertAt( 1, myGroup ); // Insert "myGroup" layer at the bottom of the layer array - insertAt() prevent zIndex 
			//map.addLayer( myGroup ); 
		} else { 
			var visible = getLayerGroup( iqh, "visible" ); 
			if ( !visible ) layer2show( iqh, true ); // set visible true if it's not
		}
	}

} /** setLayerGroup( STRING ) */ 


/** 
 * Build layer store used by the html <select /> store
 * @params { json{} }
 */
function postLayerStore( data ) {

	for ( var key in data ){
		var myData = []; 
		var elem = $( "div.wrapper" ); 
		var node = data[key]; 

		createStore( elem, key ); 
		for ( var i in node ) {
			if ( myData.indexOf( node[i].name ) == -1 ) bindDataStore( key, node[i] ); 
			myData.push( node[i].name ); 
		}
	}

	bindLayersOption( "pfnl" ); 

} /** postLayerStore( json{} )*/


/** 
 * Recover data from SQLite DB and reassemble theme 
 * @params { json{} }
 */
function postIqhData( data ) {
  var response = $.parseJSON( data ); 
	var myData = { pfnl: [], espece: [], iqh: [], meta: [] }; 
	
	for ( var i in response ) {
		myData.pfnl.push( { id: response[i].pId, name: response[i].pName, child: 'espece' } ); 
		myData.espece.push( { id: response[i].eId, name: response[i].eName, group: response[i].pId, child: 'iqh' } );  
		myData.iqh.push( { id: response[i].iId, name: response[i].iName, group: response[i].eId, layer: postLayerPath( response[i] ) } );
		myData.meta.push( { date: response[i].iDate, region: response[i].rName, user: response[i].uName, company: response[i].uCompany } ); 		
	}

	postLayerStore( myData ); 

	$( "select[section='iqh']" ).each( function() {
		bindFormsOption( "iqh", $( this ) ); 
	} );

} /** postIqhData( json{} ) */


/** 
 * Get IQH store in SQLite database 
 * @params { --- }
 * @return { --- }
 */
function getIqh() {
  $.post( url.file( url.php(), "getIqh.php" ) )
    .success( function( data ) { postIqhData( data ) } ) 
	  .error( function( data ) { postError( data ) } ); 
} /** getIQH() */


/**
 * Implement <option /> from dataStore 
 * @params {string}
 */
function bindFormsOption( obj, selector ) {

	var store = $( ".store-" + obj ); 
	var combo = ( !selector ) ? $( "select#" + obj ) : selector;
	var empty_text = combo.children().first().val(); 
 
	// Preset comboBox
	//resetSelectOption( combo, empty_text ); 


	/* 
	 * Translate the label group from SQLiteDB values
	 * @params { STRING }
	 * @return { STRING }
	 */
	function changeLabel( obj ) {
		var results = ( obj == "share" ) ? "Partage" :  
			( obj == "personal" ) ? "Personnel" : 
			( obj == "public" ) ? "Publique" : 
			obj; 

		return results; 
	} /* changeLabel() */


	// Create <select/><option/> OR <select/><optgroup/><option/> AND attributs
	var optgroup = $( "<optgroup/>" ); 
	store.children().each( function() {
		var properties = {}; 
		var name = $( this ).attr( "name" ).replaceAll( "\'", "&#39;" ); 
		var option = $( "<option/>" ).html( name ).attr( "name", name ); 

		if ( $( this ).attr( "value" ) ) option.attr( "value", value ); 

		// use GLOBAL variable to detect if optgroup already exist
		if ( $( this ).attr( "optgroup" ) ) {
			var label = changeLabel( $( this ).attr( "optgroup" ) ); 
			$( this ).attr( "optgroup", label ); 
			/*if ( $( this ).attr( "optgroup" ) !== optgroup.attr( "label" ) ) {
				optgroup = $( "<optgroup/>" ).attr( "label", $( this ).attr( "optgroup" ) ); 
				combo.append( optgroup.prop( "outerHTML" ) ); 
			}*/
			if ( $( "optgroup[label='" + label + "']" ).length < 1 ) { 
				optgroup = $( "<optgroup/>" ).attr( "label", $( this ).attr( "optgroup" ) ); 
				combo.append( optgroup.prop( "outerHTML" ) ); 
			} 

		}

		var exist; 
		if ( $( this ).attr( "id" ) !== undefined ) {
			var id = $( this ).attr( "id" )
			option.attr( "id", id ); 
			exist = combo.find( "option[name='"+ name +"'][id='"+ id +"']" ); 
		} else if ( $( this ).attr( "value" ) !== undefined ) {
			var value = $( this ).attr( "value" )
			option.attr( "value", value );
			exist = combo.find( "option[name='"+ name +"'][value='"+ value +"']" ); 
		} else {
			exist = combo.find( "option[name='"+ name +"']" ); 
		}

		// Add attributs to the last <option/> element 
		if ( exist.length < 1 ) { 
			if ( $( this ).attr( "optgroup" ) ) {
				combo.find( "optgroup[label='" + $( this ).attr( "optgroup" ) + "\']" )
					.append( option.prop( "outerHTML" ) ); 
			} else {
				combo.append( option.prop( "outerHTML" ) );
			}
			
		/*	
  		for ( var key in properties ) {
				combo.children().last().attr( key, properties[key] ); 
			}
		*/
		}

	
	} ); 

}


/**
 * Create from-group <select />
 * @params {$(elem), string}
 */
function createSelect( elem, obj ) {
	var results = "<div class='form-group'>"
    + "<select class='form-control' id='" + obj + "'>"
    + "<option style='display:none'> Aucun</option>"
    + "</select></div>"; 

	$( results ).insertAfter( elem.children( "td:first-child" ) ); 	
} 


/*
 * Add different type of table field
 * @params { $( elem ), array[ STRING ], STRING }
 */ 
function addTableRow( table, field ) {
	var node; 
	var results; 

	table.append( "<tr />" )
	for ( var i in field ) {
		results = "<td> " + field[i] + "</td>"; 		
		table.children().last().append( results ); 
	}

} /** addTableRow() */


/**
 * Implement <table /> from dataStore 
 * @params {string}
 */
function bindSimpleTable( obj ) {
	var store = $( ".store-" + obj ); 
	var table = $( "table#" + obj ).children( "tbody" ); 

	// Create <table/><tr/> row
	store.children().each(function() {
		var name = $( this ).attr( "name" ); 
		var field; 

		( obj == "depot" ) ? field = [ name, "De&#769po&#770t e&#769pais", 0 ] : field = [ name, 0 ]; 

		if ( name != "Aucun" ) addTableRow( table, field );
		 
	}); 

}


/** 
 * WARNING - USED AS CALLBACK ON LOAD()
 * Decode data.json to build thematic layer <select /> store
 * @params {data.json}
 */
function mushroomStore( data ) {
 	var json = $.parseJSON(data);  
	var elem = $( "div.wrapper" ); 

 	for ( var i in json.attribut ) {
 		var node = json.attribut[i]; 
 		var name = node.name; 
 		var type = node.type; 

 		if ( node.pointer !== undefined ) var pointer = node.pointer; 
 		
 		createStore( elem, name ); 
 		for ( var i in node.data ) {
 			var li = node.data[i]; 
 			bindDataStore( name, li ); 
 		} 		

 		if ( $( "table#" + name ).length != 0 && type == "simple" ) bindSimpleTable( name ); 			
 		if ( $( "select#" + name ).length != 0 ) bindFormsOption( name ); 

 	}

} /** mushroomStore( json{} ) */


/** 
 * WARNING - USED AS CALLBACK ON LOAD()
 * Decode data.json to build thematic layer <select /> store
 * @params {data.json}
 */
function srsStore( data ) {
 	var json = data ;  
	var elem = $( "div.wrapper" ); 

	createStore( elem, "srs" ); 
	for ( var i in json.srs ) {
		bindDataStore( "srs", json.srs[i] );
	}
	bindFormsOption( "srs" );
} /* srsStore() */


/** 
 * WARNING - USED AS CALLBACK ON LOAD()
 * Decode data.json to build thematic layer <select /> store
 * @params {data.json}
 */
function projectStore( data ) {
	var elem = $( "div.wrapper" ); 

	if ( $( "ul.store-project" ).length >= 1 ) { $( "ul.store-project" ).remove(); }
	createStore( elem, "project" ); 
	for ( var i in data ) {
		bindDataStore( "project", data[i] );
	}

	bindFormsOption( "project" );
} /* projectStore() */


function validationStore( data ) {
	var json = data ;  
	var elem = $( "div.wrapper" ); 

	for ( var i in json.attribut ) {
 		var node = json.attribut[i]; 
 		var name = node.name; 
 		var type = node.type; 
 		
 		if ( $( "ul.store-" + name ).length >= 1 ) { $( "ul.store-" + name ).remove(); }
		createStore( elem, name ); 
 		for ( var i in node.data ) {
 			var li = node.data[i]; 
 			bindDataStore( name, li ); 
 		} 

 		$( "select[section='" + name + "']" ).each( function() {
			bindFormsOption( name, $( this ) );
	 	} ); 
		
 	}		



}



