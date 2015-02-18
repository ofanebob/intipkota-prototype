jQuery(document).ready(function()
{
	var wrapperW = jQuery('#wrapper').width();
	var containerSideW = wrapperW*33.5/100;
	var siderightW = jQuery('#sideright').width();
	var spesificWidthOverlay = Math.round(wrapperW-containerSideW);

	/**
	 * Quick Map Venue
	 */
	jQuery('a.quick-view-map')
	.removeAttr('href')
	.css({cursor:'pointer'})
	.bind('click',function(e)
	{
		var validate = vars.date,
		locale = sitevar.lang,
		venuename = jQuery(this).attr('title').length > 0 ? jQuery(this).attr('title') : jQuery(this).attr('data-original-title'),
		venueid = jQuery(this).attr('id');

		var venue_id = [];
		
		e.preventDefault();

		if(jQuery('#OverlayVenueInfo').length > 0)
		{
			jQuery('#OverlayVenueInfo').animate({width:'toggle',opacity: 'toggle'},'900',function(){
				jQuery(this).remove();
			});
		}

		if(jQuery('#OverlayVenueInfo').attr('alt') != venueid)
		{
			NProgress.start();

			jQuery('<div id="OverlayVenueInfo"></div>').prependTo('body');

			var api_request = 'https://api.foursquare.com/v2/venues/'+venueid+'?v='+validate+'&locale='+locale+'&client_id='+vars.clid+'&client_secret='+vars.clsc;
			
			jQuery.getJSON(api_request)
			.done(function(detail_venue)
			{
				var venueResponse = detail_venue.response.venue;
				
				var classColumnIconInfo = venueResponse.tips.groups[0].items.length > 0 ?'col-lg-6' : 'col-lg-12'

				venue_id.push(venueResponse.id);

				var venueURL = '<a id="venueSource" class="small text-white important glyphicon glyphicon-link" href="'+venueResponse.canonicalUrl+'" target="_blank"></a>';
				var venueTitle = '<span id="InnerTitleText">'+venueResponse.name+' '+venueURL+'</span>';

				var venueAddress = '<address class="small nomargin text-white important">'+venueResponse.location.formattedAddress.join(', ')+'</address>';

				var PlasticCover = '<span id="PlasticCover"></span>';
				
				buildHTML = '<div class="separator" style="padding:5px"></div>';

				buildHTML += '<div class="container-fluid nopadding">';

					buildHTML += '<a href="javascript:void(0)" class="VenueInfoClose btn btn-default"><i class="glyphicon glyphicon-remove no-text-decoration"></i></a>';

					buildHTML += '<div class="position-relative" id="ContentVenue">';

					buildHTML += '<h3 id="TitleVenue" class="text-white nomargin">'+venueTitle+venueAddress+'</h3>';

					/* START: Meta Info & Icon */
						buildHTML += '<div id="VenueMetaIconInfo" class="position-relative">';
						//buildHTML += '<div class="page-header separator" id="VenueStats">'+venueResponse.stats.usersCount+'+ orang pernah kesini!</div>';

						var vrate = venueResponse.rating ? venueResponse.rating : 0;

						if(vrate!=0)
						{
							RatingColor = rating_color(vrate);
							buildHTML += '<span class="metaIconInfo label label-'+RatingColor[1]+' no-overflow" id="VenueRating">';
							buildHTML += 'Rating: '+vrate+'</span>';
						}
						
						var iconConfigName = vars.BgIcon+vars.SizeIcon;

						if(venueResponse.categories.length > 0)
						{
							//vCategory = venueResponse.categories.slice(0,2);
							vCategory = venueResponse.categories;

							for(i=0;i<vCategory.length;i++)
							{
								//var caticon = vCategory[i].icon.prefix+iconConfigName+vCategory[i].icon.suffix;
								var catname = vCategory[i].name;
								buildHTML += '<span class="metaIconInfo label label-primary no-overflow">'+catname+'</span>';
							}
						}
						/*else
						{
							var icon_prefix = 'https://ss3.4sqi.net/img/categories_v2/none_';
							var caticon = icon_prefix+'/'+iconConfigName+'.png';
							var catname = 'Umum';
							buildHTML += '<div title="'+catname+'" data-toggle="tooltip" data-placement="bottom" class="metaIconInfo label label-primary d-inline-block no-overflow"><img src="'+caticon+'" width="55" height="55" alt="'+catname+'" /></div>';
						}*/
						buildHTML += '</div>';
					/* END: Meta Info & Icon */

					if(venueResponse.photos.groups.length != 0)
					{
						var vphoto = venueResponse.photos.groups[0].items;

						/*if(venueResponse.photos.groups[0].items.length > 0)
						{*/
							vphoto = vphoto.slice(0,5);

							buildHTML += '<div id="VenueCover" class="bg-size-cover vp_min_type" style="background-image:url('+vphoto[0].prefix+'800x200/blur20'+vphoto[0].suffix+');">'+PlasticCover+'</div>';
							buildHTML += '<ul class="taglist-flat d-inline-block w-100cent text-center" id="VenuePhotos">';

							for(i=0;i<vphoto.length;i++)
							{
								buildHTML += '<li class="photoLists d-inline-block">';
								buildHTML += '<img class="thumbnail" src="'+vphoto[i].prefix+'150x150'+vphoto[i].suffix+'" width="150" height="150" />';
								buildHTML += '</li>';
							}

							buildHTML += '</ul>';
						/*}
						else
						{
							buildHTML += '<div id="VenueCover" class="bg-size-cover vp_big_type" style="background-image:url('+vphoto[0].prefix+'800x400/blur5'+vphoto[0].suffix+');">'+PlasticCover+'</div>';

							buildHTML += '<div class="separator"></div>';
						}*/
					}
					else
					{
						buildHTML += '<div id="VenueCover" class="bg-size-cover vp_big_type" style="background-image:url(https://maps.googleapis.com/maps/api/staticmap?center='+venueResponse.location.lat+','+venueResponse.location.lng+'&zoom=15&size=800x400&sensor=false&markers='+venueResponse.location.lat+','+venueResponse.location.lng+');">'+PlasticCover+'</div>';
						buildHTML += '<ul class="taglist-flat d-inline-block w-100cent text-center" id="VenuePhotos">';
						buildHTML += '<li class="photoLists d-inline-block">';
						buildHTML += '<img class="thumbnail" src="'+sitevar.domain+'/public/images/no-image-80x80.jpg" width="150" height="150" />';
						buildHTML += '</li></ul>';
					}

					buildHTML += '</div>';

					buildHTML += '<div class="container-fluid clearfix"><div class="inner-separator" id="VenueMeta">';

						buildHTML += '<div id="VenueMapInfo" class="text-right '+classColumnIconInfo+' nopadding">';

							buildHTML += '<div id="VenueMap"></div>';

						buildHTML += '</div>';

						if(venueResponse.tips.groups[0].items.length > 0)
						{
							buildHTML += '<div class="col-lg-6 nopadding"><div id="VenueTips">';
							buildHTML += '<h3 class="text-primary page-header nomargin no-border important"><i class="glyphicon glyphicon-comment v-align-top"></i> Tips</h3>';
							buildHTML += '<div id="VenueTipsScroll" class="overflow-y border-right border-solid border-1px border-smoke">';
							buildHTML += '<ul class="taglist-flat">';
							
							var vtips = venueResponse.tips.groups[0].items;

							for(i=0;i<vtips.length;i++)
							{
								buildHTML += '<li class="noborder-last-bottom inner-separator border-bottom border-solid border-1px border-smoke">';
								buildHTML += vtips[i].text;
								buildHTML += '<br /><small class="text-gray d-inline-block">'+date('j F, Y',vtips[i].createdAt)+'</small></li>';
							}

							buildHTML += '</ul></div></div></div>';
						}

					buildHTML += '</div></div>';

				buildHTML += '</div>'; // Container

				//console.log('C:'+containerSideW+', SL:'+sideleftW+', IL:'+insideLeftW+', IR:'+insideRightW);

				jQuery('#OverlayVenueInfo')
				.html(buildHTML)
				.addClass('position-absolute bg-white border-solid border-3px border-right border-smoke')
				.css({height:'100%',zIndex:'999',width:spesificWidthOverlay})
				.attr('alt',venue_id[0]);

				var spesificPercentHeightBrowser = jQuery.browser.mozilla ? 41 : 45;
				var scrollHeight = Math.round(jQuery('#OverlayVenueInfo').height()*(spesificPercentHeightBrowser/100));

				jQuery('#OverlayVenueInfo #VenueTipsScroll')
				.css({'max-height':scrollHeight,width:'100%'})
				.enscroll({
				    verticalTrackClass: 'track-inside',
				    verticalHandleClass: 'handle-inside',
				    minScrollbarLength: 28,
				    easingDuration: 50
				});

				var vlat = venueResponse.location.lat;
				var vlng = venueResponse.location.lng;
				var vlatlng = vlat+','+vlng;
				var MapEndpoint = 'https://maps.google.com/maps?q='+vlatlng+'&amp;hl=id&amp;z=16&amp;t=v&amp;hnear='+vlatlng+'&amp;output=embed';

				jQuery('<iframe id="VenueMapIframe" class="no-overflow thumbnail nomargin w-100cent" src="'+MapEndpoint+'" allowfullscreen="" frameborder="0"></iframe>')
				.prependTo('#OverlayVenueInfo #VenueMap');

				jQuery('#OverlayVenueInfo iframe#VenueMapIframe')
				.css({height:scrollHeight+25})
				.error(function()
				{
					jQuery(this).fadeOut();
				});
			
				NProgress.done();
		
				jQuery('#OverlayVenueInfo .VenueInfoClose')
				.css({
					position: 'absolute',
					right: '0',
					zIndex: '99',
					margin: '10px'
				})
				.bind('click', function()
				{
					jQuery('#OverlayVenueInfo').remove();
				});

				jQuery('[data-toggle="tooltip"]').tooltip();
			})
			.fail(function()
			{
				NProgress.done();
				jQuery.notify('Gagal Loading "'+venuename+'"...', {pos:'bottom-right', status:'danger'});
			});
		}
	});

	jQuery(document).keyup(function(e)
	{
		if(e.keyCode == 27)
		{
			jQuery('#OverlayVenueInfo').remove();
		}
	});
});

function rating_color(v)
{
	//v = parseInt(v);

	if(v > 0)
	{
		if(v <= 4.9)
		{
			return ['888','default'];
		}
		else if(v >= 5 && v <= 7)
		{
			return ['FFC800','warning'];
		}
		else if(v >= 7.1)
		{
			return ['00B551','success'];
		}
		else
		{
			return ['888','default'];
		}
	}
}