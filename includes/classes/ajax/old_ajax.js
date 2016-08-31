function GetXMLHttp() {
    var xmlHttp;
    try {
        xmlHttp = new XMLHttpRequest();
    }
    catch(ee) {
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch(e) {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(e) {
                xmlHttp = false;
            }
        }
    }
    return xmlHttp;
}

var xmlRequest = GetXMLHttp();