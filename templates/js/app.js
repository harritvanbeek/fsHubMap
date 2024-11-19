"use strict";

var d = new Date();
var controler = {
  view : './view/com_',
  version : d.getTime()
}

var setDebug = true;
var logger = function()
{
    var oldConsoleLog = null;
    var pub = {};

    pub.enableLogger =  function enableLogger() 
    {
        if(oldConsoleLog == null)
            return;
            window['console']['log'] = oldConsoleLog;
    };

	pub.disableLogger = function disableLogger()
    {
        oldConsoleLog = console.log;
        window['console']['log'] = function() {};
    };

    return pub;
}();

$(document).ready(
    function(){    
	    if(setDebug){
	    	logger.enableLogger();
	    }else{
	    	var cssRule =
	    		"color: red;" +
	    		"font-size: 60px;" +
	    		"font-weight: bold;";
			console.log("%cStop!", cssRule);
		
			var cssRule =
		    	"font-weight: bold;" +
		    	"font-size: 12pt;";
			console.log("%cThis is a browser feature intended for developers. If someone told you to copy-past somthing here to enable a website feature or 'hack' someone`s account, it is a scam and will give them access to your account", cssRule);
			logger.disableLogger();
	    }  
    }
 );

 var App = angular.module('App', ['ngSanitize', 'ui.router']); 
