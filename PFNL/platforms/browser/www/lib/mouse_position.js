var myFormat = function(dgts)
{
  return (
    function(coord1) {
        var coord2 = [coord1[1], coord1[0]]; 
      return "(latitude,longitude) :  " + ol.coordinate.toStringXY(coord2,dgts);
  });        
}

var mousePositionControl = new ol.control.MousePosition({
    coordinateFormat: myFormat(4), // <--- change here
    projection: 'EPSG:4326',
    className: 'custom-mouse-position',
    target: document.getElementById('mouse-position'),
    undefinedHTML: '&nbsp;'
});