
//Este script recibe como argumento el ipaddress y devuelve la localización del mismo en un .json

var ipaddress = process.argv[2];
//var lastName = process.argv[3]; // Will be set to 'Worthington'
function localizacion(direccion){
    var geoip = require('geoip-lite');
    var ip = direccion;
    var geo = geoip.lookup(ip);
    console.log(geo);
    return geo;
    
}

localizacion(ipaddress);
