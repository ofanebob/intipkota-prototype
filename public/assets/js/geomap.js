/*!
 * Geomap intipkota
 * @author Ofan Ebob
 * @build 2014-2015
 */
var MarkerStyles = [
	{
		height: 53,
		url: sitevar.domain+"/public/images/m1.png",
		width: 53
	},
	{
		height: 56,
		url: sitevar.domain+"/public/images/m2.png",
		width: 56
	},
	{
		height: 66,
		url: sitevar.domain+"/public/images/m3.png",
		width: 66
	},
	{
		height: 78,
		url: sitevar.domain+"/public/images/m4.png",
		width: 78
	},
	{
		height: 90,
		url: sitevar.domain+"/public/images/m5.png",
		width: 90
	}
];

var TopNavbar = jQuery('#TopNavbar').height(),
windowsHeight = Math.round(jQuery(window).height()-(TopNavbar+2)),
CanvasDOM = '#MapCanvas',
geoLat = geolocation.lat,
geoLng = geolocation.lng,
_mapCenter = geoLat+','+geoLng,
f = foursquare,
TotalVenue = f.filter(function(value) { return value !== undefined }).length,
DynamicZoom = Math.round( 20-(((TotalVenue*20)/100)/3.5) ),
_map = null,
_markerCluster = null;

jQuery(document).ready(function()
{
	var resetMapsDOM = '<div id="resetMapsDOM"><i class="glyphicon glyphicon-refresh v-align-middle"></i></div>';

	jQuery(CanvasDOM)
	.css({height:windowsHeight})
	.gmap({
		disableDefaultUI: true,
		center: _mapCenter,
		zoom: DynamicZoom,
		styles:[
			{
				stylers:
				[
					{gamma: .75},
					{lightness: -5},
					{saturation: 20}
				]
			}
		]
	})
	.bind('init',function(evt, map)
	{
		thisInit = this;

		jQuery(this).gmap('clear','markers');
		
		DefaultLoadVenue({foursquare:f},CanvasDOM);

		var _markers = jQuery(this).gmap('get', 'markers');

		_map = map;

		_markerCluster = new MarkerClusterer(_map, _markers, 
							{ minimumClusterSize: 5, styles: MarkerStyles });

		BuildMarkerCluster({map:_map,markerCluster:_markerCluster});

		jQuery(resetMapsDOM)
		.css({
			'position':'absolute',
			'top':'0px',
			'right':'0px',
			'z-index':'20099',
			'margin':'10px',
			'cursor':'pointer',
			'font-size':'18px',
			'padding':'5px 6px',
			'border-radius':'50%',
			'line-height':'1'
		})
		.addClass('text-white bg-primary border-solid border-white border-3px border-all')
		.prependTo(this);

		jQuery('#resetMapsDOM')
		.bind('click', function()
		{
			$this = jQuery(CanvasDOM);

			jQuery('.glyphicon', this)
			.animateRotate(360, 2000, "linear", function(){});

			$this.gmap('option', 'center', new google.maps.LatLng(geoLat,geoLng));

			$this.gmap('option', 'zoom', DynamicZoom);

			$this.gmap('closeInfoWindow');
		});
	});


	jQuery('body')
	.removeAttr('href')
	.css({cursor:'pointer'})
	.on('click', 'a.quick-venue-map', function(e)
	{
		e.preventDefault();

		var dataGeocode = jQuery(this).attr('data-geocode'),
		dataID = jQuery(this).attr('id'),
		thisLL = dataGeocode.split(',');
		thisLat = thisLL[0],
		thisLng = thisLL[1];

		jQuery(CanvasDOM)
		.gmap('find', 'markers', 
		{ 'property': 'id', 'value': [dataID], 'operator': 'OR' }, 
		function(marker, found)
		{
			if(found)
			{
				jQuery(CanvasDOM)
				.gmap('option', 'center', new google.maps.LatLng(thisLat,thisLng));

				jQuery(CanvasDOM)
				.gmap('option', 'zoom', 19);

				jQuery(marker).triggerEvent('click'); 
			}
		});
	});

	jQuery('body')
	.on('keyup', 'input#filtersearchbox', function(e)
	{
		var FilterQuery = jQuery(this).val();

	    if(e.keyCode == 13)
	    {
	    	if(FilterQuery.length >= 3)
	    	{
				SearchVenue(this, CanvasDOM, 
					{
						query:jQuery(this).val(),
						map:_map,
						styles:MarkerStyles
					}
				);
			}
			else if(FilterQuery.length >= 1 && FilterQuery.length <= 2)
			{
				return false;
			}
			else
			{
				jQuery(CanvasDOM)
				.gmap('option', 'zoom', DynamicZoom);

				jQuery(CanvasDOM)
				.gmap('option', 'center', new google.maps.LatLng(geoLat,geoLng));

				if(jQuery(CanvasDOM).hasClass('filter'))
				{
					jQuery(CanvasDOM).removeClass('filter');

					DefaultLoadVenue({foursquare:f,map:_map},CanvasDOM,_markerCluster);

					BuildListsVenue(f);
				}
			}
		}
	});
});


function DefaultLoadVenue(data,CanvasDOM,markerCluster)
{
	if(markerCluster)
	{
		jQuery(CanvasDOM).gmap( 'clear','markers' );
		markerCluster.clearMarkers();
	}

	jQuery(CanvasDOM).gmap('closeInfoWindow');

	jQuery.each(data.foursquare, function(e,v)
	{
		vCategoryLabel = v.venue.categories.length > 0 ? 
					'<span class="label label-primary no-overflow">'+v.venue.categories[0].name+'<span>' 
					: '';

		vCategoryID = v.venue.categories.length > 0 ? v.venue.categories[0].id : 0;

		var defaultImage = sitevar.domain+'/public/images/no-image-80x100.jpg';
		if(v.venue.photos)
		{
			vPhotos = v.venue.photos;

			var p = vPhotos.groups;
			p = p.length > 0 ? p[0].items[0] : null;
			img = p != null ? p.prefix+'80x100'+p.suffix : defaultImage;
		}
		else
		{
			img = defaultImage;
		}

		var RecontstructVenue = {
									id:v.venue.id,
									title:v.venue.name,
									lat:v.venue.location.lat,
									lng:v.venue.location.lng,
									address:v.venue.location.formattedAddress.join(', '),
									categories:{label:vCategoryLabel,id:vCategoryID},
									image:img
								},
		iconSuffix = v.venue.categories[0].icon.suffix,
		iconPrefix = v.venue.categories[0].icon.prefix,
		venueIcon = iconPrefix+'32'+iconSuffix;

		BuildMarkerFoursquare(RecontstructVenue,venueIcon,'venueMarkerMaps',CanvasDOM);
	});

	if(markerCluster)
	{
		BuildMarkerCluster(
			{
				map: data.map,
				thisint: CanvasDOM
			}
		);
	}
}


function SearchVenue(thisDom,CanvasDOM,DataEndpoint)
{
	NProgress.start();

	var Query = DataEndpoint.query.length > 0 ? 'query='+DataEndpoint.query+'&' : '';
	var APIENDPOINT = 'https://api.foursquare.com/v2/venues/explore?'+Query+'ll='+_mapCenter+'&v='+vars.date+'&radius=20000&venuePhotos=1&locale='+sitevar.lang+'&client_id='+vars.clid+'&client_secret='+vars.clsc;
	var LatLng = _mapCenter.split(','),
	lat = '',
	lng = ''

	$this = jQuery(CanvasDOM);

	$this.gmap('closeInfoWindow');

	jQuery.getJSON(APIENDPOINT)
	.done(function(data)
	{
		var foursquare = data.response.groups[0].items;

		if(foursquare.length > 0)
		{
			var CoordinateMinimum = foursquare.length >= 10 ? 
							{lat:LatLng[0],lng:LatLng[1]} : 
							{lat:foursquare[0].venue.location.lat,lng:foursquare[0].venue.location.lng};

			$this.gmap( 'clear','markers' );

			_markerCluster.clearMarkers();

			$this.gmap( 'option', 'center', new google.maps.LatLng(CoordinateMinimum.lat,CoordinateMinimum.lng));

			if(foursquare.length >= 10)
			{
				$this.gmap( 'option','zoom', 13 );
			}

			jQuery(CanvasDOM).addClass('filter');

			DefaultLoadVenue({foursquare:foursquare},CanvasDOM);

			BuildMarkerCluster(
				{
					map: DataEndpoint.map,
					thisint: CanvasDOM
				}
			);

			BuildListsVenue(foursquare);
			
			NProgress.done();
		}
		else
		{
			jQuery.notify('Venue "'+DataEndpoint.query+'" Tidak Ditemukan', {pos:'bottom-right', status:'danger'});
			
			NProgress.done();
		}
	})
	.fail(function()
	{
		jQuery.notify('Gagal Loading Venue "'+DataEndpoint.query+'"', {pos:'bottom-right', status:'danger'});
			
		NProgress.done();
	});
}


function BuildMarkerCluster(data)
{
	var _markers = jQuery(data.thisint).gmap('get', 'markers');

	_markerCluster = !data.markerCluster ? 
						new MarkerClusterer(data.map, _markers, 
							{ minimumClusterSize: 5, styles: MarkerStyles } ) : 
						data.markerCluster;

	jQuery(data.thisint)
	.gmap( 'set', 'MarkerClusterer', _markerCluster );
}


function BuildOpenWindow(thisElm, DATAVENUE, CanvasDOM)
{
	var WindowMap = '<div id="'+DATAVENUE.id+'" class="WindowMap inner-separator"><div class="innerInfoWindows media">';
	WindowMap += '<span class="pull-left">';
	WindowMap += '<img src="'+DATAVENUE.image+'" class="thumbnail nomargin" data-holder-rendered="true" style="width: 80px; height: 100px;" alt="80x100" /></span>';
	WindowMap += '<div class="media-body">';
	WindowMap += '<h4 class="media-heading">'+DATAVENUE.title+'</h4>'
	WindowMap += '<div class="inner-separator">'+DATAVENUE.categories.label+'</div>';
	WindowMap += DATAVENUE.address;
	WindowMap += '</div></div></div>';

    jQuery(CanvasDOM)
    .gmap('openInfoWindow',
    { content: WindowMap }, thisElm);
}


function BuildMarkerFoursquare(DATAVENUE, ICON, labelClass, CanvasDOM)
{
	var markerData = {
        'id': [DATAVENUE.id],
        'position': new google.maps.LatLng(DATAVENUE.lat, DATAVENUE.lng),
        'tags': [DATAVENUE.categories.id],
        'title': DATAVENUE.title,
        'optimized': false,
        'icon': sitevar.domain+'/public/images/map-marker.png'
        //labelAnchor: new google.maps.Point(23, 37),
        //labelClass: labelClass,
        /*labelStyle:
        {
            opacity: 1
        },*/
        //labelVisible: false,
        //marker: MarkerWithLabel
	}

    jQuery(CanvasDOM)
    .gmap('addMarker',markerData)
    .dblclick(function()
    {
    	//window.open('https://foursquare.com/v/'+DATAVENUE.id, '_blank');
    	$.fn.geomodal({auto:true});
    })
    .click(function()
    {
    	//console.log(this);
	    BuildOpenWindow(this, DATAVENUE, CanvasDOM);
    })
    .mouseout(function()
    {
    	jQuery(CanvasDOM).gmap('closeInfoWindow');
    });
}

function BuildListsVenue(data)
{
	var iconSuffix = '32.png';

	ListVenueHTML = '<div class="clearfix">';

	jQuery.each(data, function(e,v)
	{
		var venue = v.venue;

		/** @var Category */
		if(venue.categories)
		{
			var categories = venue.categories;
			icon = categories[0].icon.prefix+iconSuffix;
		}
		else
		{
			icon = 'https://ss3.4sqi.net/img/categories_v2/none_'+iconSuffix;
		}

		/** @var Canonical URL */
		fixCanonical = 'https://foursquare.com/v/'+venue.id;

		/** @Condition Hitung Total huruf di judul */
		nomargin = venue.name.length >= 27 ? 'class="nomargin"' : '';

		ListVenueHTML += '<div class="media foursquare noborder-last-bottom border-smoke border-bottom border-1px border-solid">';
			ListVenueHTML += '<a data-geocode="'+venue.location.lat+','+venue.location.lng+'" class="pull-left thumbnail quick-venue-map" id="'+venue.id+'" title="'+htmlentities(venue.name)+'" href="'+fixCanonical+'" target="_blank">';
			ListVenueHTML += '<img src="'+icon+'" class="waitForImages bg-primary" data-holder-rendered="true" style="width: 32px; height: 32px;" alt="32x32" />';
			ListVenueHTML += '</a>';

			ListVenueHTML += '<div class="media-body">';
				ListVenueHTML += '<div class="media-heading">';
					ListVenueHTML += '<h5 '+nomargin+'>';
					ListVenueHTML += '<a data-geocode="'+venue.location.lat+','+venue.location.lng+'" id="'+venue.id+'" class="quick-venue-map d-inline-block w-100cent" title="'+htmlentities(venue.name)+'" href="'+fixCanonical+'" target="_blank">';
					ListVenueHTML += venue.name.substr(0, 1).toUpperCase() + venue.name.substring(1);
					ListVenueHTML += '</a>';
					ListVenueHTML += '</h5>';
				ListVenueHTML += '</div>';
			ListVenueHTML += '</div>';
		ListVenueHTML += '</div>';
	});
	
	ListVenueHTML += '</div>';

	jQuery('div#scollVenue').html(ListVenueHTML);
}