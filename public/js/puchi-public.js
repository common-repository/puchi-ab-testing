(function( $ ) {
	'use strict';
	const PUCHI_TRACKING = {
		"anchor": "a",
		"button": "button",
		"submit": "[type='submit']"
	}
	
	const PUCHI_ELEM = {
		"a" : "Link",
		"button": "Button",
		"[type='submit']": "Form Submit"
	}
	
	function puchiFetchTrigger(elem, type){
		return (type != "[type='submit']") ? PUCHI_ELEM[type] + ': ' + elem.html() : PUCHI_ELEM[type] + ': ' + elem.attr('value');
	}
	
	$(document).ready(function(){
		$('.puchi-content').each(function(){
			var self = $(this),
				base64 = self.data('puchi');
				
			if (base64 != '') {
				var data = JSON.parse($.base64.decode(base64)),
					track = data.tracker.map(item => PUCHI_TRACKING[item]);
					
				$.each(track,function(i,v){
					self.find(v).each(function(){
						var trigger = $(this);
						trigger.on('click', function(e){
							if (!$(this).hasClass('fetched')) {
								var elem = this, $elem = $(this);
								
								e.stopPropagation();
								e.preventDefault();
								
								$.post( puchi_data.api_url + "add_split_click/" ,{
									trigger : puchiFetchTrigger($elem,v),
									data : base64
								}, function(result){
									$elem.addClass('fetched');
									elem.click();
								});
							}
						});
					});
				});
			}
		});
	});

})( jQuery );
