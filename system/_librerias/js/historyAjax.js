
(function(window,undefined){

	// Bind to State Change
	window.onpopstate = function(event) {
		console.log(window.location.href);
		location.replace(window.location.href);
		// alert("onpopstate = " + window.location.href); // do something onpopstate
	};


})(window);

