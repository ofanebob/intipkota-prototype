$(function() { 
	demo.add(function() {			
		$('#map_canvas').gmap({'disableDefaultUI':true}).bind('init', function(evt, map) { 
			//$('#map_canvas').gmap('addControl', 'tags-control', google.maps.ControlPosition.TOP_LEFT);
			$('#map_canvas').gmap('addControl', 'radios', google.maps.ControlPosition.TOP_LEFT);
			var southWest = map.getBounds().getSouthWest();
			var northEast = map.getBounds().getNorthEast();
			var lngSpan = northEast.lng() - southWest.lng();
			var latSpan = northEast.lat() - southWest.lat();
			var images = ['http://google-maps-icons.googlecode.com/files/friends.png', 'http://google-maps-icons.googlecode.com/files/home.png', 'http://google-maps-icons.googlecode.com/files/girlfriend.png', 'http://google-maps-icons.googlecode.com/files/dates.png', 'http://google-maps-icons.googlecode.com/files/realestate.png', 'http://google-maps-icons.googlecode.com/files/apartment.png', 'http://google-maps-icons.googlecode.com/files/family.png'];
			var tags = ['jQuery', 'Google maps', 'Plugin', 'SEO', 'Java', 'PHP', 'C#', 'Ruby', 'JavaScript', 'HTML'];
			//$('#tags').append('<option value="all">All</option>');
			$.each(tags, function(i, tag) {
				//$('#tags').append(('<option value="{0}">{1}</option>').format(tag, tag));
				$('#radios').append(('<label style="margin-right:5px;display:block;"><input type="checkbox" style="margin-right:3px" value="{0}"/>{1}</label>').format(tag, tag));
			});
			for ( i = 0; i < 100; i++ ) {
				var temp = [];
				for ( j = 0; j < Math.random()*5; j++ ) {
					temp.push(tags[Math.floor(Math.random()*10)]);
				}
				$('#map_canvas').gmap('addMarker', { 'icon': images[(Math.floor(Math.random()*7))], 'tags':temp, 'bound':true, 'position': new google.maps.LatLng(southWest.lat() + latSpan * Math.random(), southWest.lng() + lngSpan * Math.random()) } ).click(function() {
					var visibleInViewport = ( $('#map_canvas').gmap('inViewport', $(this)[0]) ) ? 'I\'m visible in the viewport.' : 'I\'m sad and hidden.';
					$('#map_canvas').gmap('openInfoWindow', { 'content': $(this)[0].tags + '<br/>' +visibleInViewport }, this);
				});
			}
			$('input:checkbox').click(function() {
				$('#map_canvas').gmap('closeInfoWindow');
				$('#map_canvas').gmap('set', 'bounds', null);
				var filters = [];
				$('input:checkbox:checked').each(function(i, checkbox) {
					filters.push($(checkbox).val());
				});
				if ( filters.length > 0 ) {
					$('#map_canvas').gmap('find', 'markers', { 'property': 'tags', 'value': filters, 'operator': 'OR' }, function(marker, found) {
						if (found) {
							$('#map_canvas').gmap('addBounds', marker.position);
						}
						marker.setVisible(found); 
					});
				} else {
					$.each($('#map_canvas').gmap('get', 'markers'), function(i, marker) {
						$('#map_canvas').gmap('addBounds', marker.position);
						marker.setVisible(true); 
					});
				}
			});
			
			/*$("#tags").change(function() {
				$('#map_canvas').gmap('closeInfoWindow');
				$('#map_canvas').gmap('set', 'bounds', null);
				if ( $(this).val() == 'all' ) {
					$.each($('#map_canvas').gmap('get', 'markers'), function(i, marker) {
						marker.setVisible(true); 
					});
				} else {
					$('#map_canvas').gmap('find', 'markers', { 'property': 'tags', 'value': $(this).val() }, function(marker, found) {
						if (found) {
							$('#map_canvas').gmap('addBounds', marker.position);
						}
						marker.setVisible(found); 
					});
				}
			});*/
		});
	}).load();
});