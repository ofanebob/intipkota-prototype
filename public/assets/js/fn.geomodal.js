;
(function($)
{

    var geomodal = function(link, options) {

        var $this = this,
		$spesificPercentHeightBrowser = jQuery.browser.mozilla ? 41 : 45,
		$scrollHeight,
		$venue_id,
		$validate = vars.date,
		$locale = sitevar.lang,
		$refpercent = '#sideright',
		$wrapper = '#wrapper',
		$OverlayVenueInfo = 'OverlayVenueInfo',
		options = {},

        defaults = {
			wrapper: $wrapper,
			refpercent: $refpercent,
			OverlayVenueInfo: $OverlayVenueInfo
		};

        $.extend(this, $.fn, {
            init : function () {
                options = $.extend(defaults, options);

                link.on('click', $this.launch);
            },
            
            launch : function (e)
            {
				
				var $wrapperW = jQuery(options.wrapper).width(),
				$containerSideW = $wrapperW*33.3/100,
				$siderightW = jQuery(options.refpercent).width(),
				$spesificWidthOverlay = Math.round($wrapperW - $containerSideW),
				$OverlayVenueInfoID = '#'+ options.OverlayVenueInfo;

				$titleAttr = jQuery(this).attr('title');

				if (typeof $titleAttr !== typeof undefined && $titleAttr !== false)
				{
				    $venuename = $titleAttr.length > 0 ? $titleAttr : jQuery(this).attr('data-original-title');
				}
				else
				{
					$venuename = options.title;
				}

				$venueid = options.id ? options.id : jQuery(this).attr('id');

				jQuery($OverlayVenueInfoID)
				.animate({width:'toggle', opacity: 'toggle'},'900',function()
				{
					jQuery(this).remove();
				});

				if(jQuery($OverlayVenueInfoID).attr('data-id') != $venueid)
				{
					NProgress.start();
					
					var api_request = 'https://api.foursquare.com/v2/venues/'+$venueid+'?v='+$validate+'&locale='+$locale+'&client_id='+vars.clid+'&client_secret='+vars.clsc;
					
					jQuery.getJSON(api_request)
					.done(function(detail_venue)
					{
						var venueResponse = detail_venue.response.venue;
						
						var classColumnIconInfo = venueResponse.tips.groups[0].items.length > 0 ?'col-lg-6' : 'col-lg-12';
						
						var vlat = venueResponse.location.lat,
						vlng = venueResponse.location.lng,
						vlatlng = vlat+','+vlng,
						MapEndpoint = 'https://maps.google.com/maps?q='+vlatlng+'&amp;hl=id&amp;z=16&amp;t=v&amp;hnear='+vlatlng+'&amp;output=embed',

						$VenueMetaInfo, /* @var element div */
						$VenueRating, /* @var element span */
						$VenueCategory, /* @var element span */
						$VenueMeta, /* @var element div */
						$TipsLists, /* @var element li */
						$CoverVenue, /* @var image URL */
						$photoLists, /* @var element li */

						$venue_id = [],
						$photoLists = [],
						$VenueCategory = [],
						$TipsLists = [];

						$venue_id.push(venueResponse.id);

							/* START: Meta Info & Icon */

							var vrate = venueResponse.rating ? venueResponse.rating : 0;

							if(vrate!=0)
							{
								var RatingColor = rating_color(vrate);
								$VenueRating = tags({tags:'span',classes:'metaIconInfo label label-'+RatingColor[1]+' no-overflow',id:'VenueRating'})
												.text('Rating: '+vrate);
							}

							if(venueResponse.categories.length > 0)
							{
								vCategory = venueResponse.categories;

								for(i=0;i<vCategory.length;i++)
								{
									var catname = vCategory[i].name;
									$VenueCategory.push(
										tags({tags:'span',classes:'metaIconInfo label label-primary no-overflow'})
										.text(catname)
									);
								}
							}

							if(venueResponse.photos.groups.length != 0)
							{
								var vphoto = venueResponse.photos.groups[0].items;

								vphoto = vphoto.slice(0,5);

								$CoverVenue = vphoto[0].prefix+'800x200/blur20'+vphoto[0].suffix;

								for(i=0;i<vphoto.length;i++)
								{
									$photoLists.push(
										tags({tags:'li',classes:'photoLists d-inline-block'})
										.prepend(
											tags({tags:'img',classes:'thumbnail'})
											.attr({
												'src':vphoto[i].prefix+'150x150'+vphoto[i].suffix,
												'width':'150',
												'height':'150'
											})
										)
									);
								}
							}
							else
							{	
								$CoverVenue = 'https://maps.googleapis.com/maps/api/staticmap?center='+venueResponse.location.lat+','+venueResponse.location.lng+'&zoom=15&size=800x400&sensor=false&markers='+venueResponse.location.lat+','+venueResponse.location.lng;

								$photoLists.push(
									tags({tags:'li',classes:'photoLists d-inline-block'})
									.prepend(
										tags({tags:'img',classes:'thumbnail'})
										.attr({
											'src':sitevar.domain+'/public/images/no-image-80x80.jpg',
											'width':'150',
											'height':'150'
										})
									)
								);
							}

							$VenueMetaInfo = div({id:'VenueMetaInfo',classes:'position-relative'})
												.prepend(
													$VenueRating,
													$VenueCategory
												);
							
							/* END: Meta Info & Icon */

							if(venueResponse.tips.groups[0].items.length > 0)
							{	
								var vtips = venueResponse.tips.groups[0].items;

								for(i=0;i<vtips.length;i++)
								{
									$TipsLists.push(
										tags({tags:'li',classes:'noborder-last-bottom inner-separator border-bottom border-solid border-1px border-smoke'})
										.prepend(
											tags({tags:'span'}).text(vtips[i].text),
											'<br />',
											tags({tags:'small',classes:'text-gray d-inline-block'})
											.text(date('j F, Y',vtips[i].createdAt))
										)
									);
								}

								$VenueMeta = 
								div({classes:'col-lg-6 nopadding'})
								.prepend(
									div({id:'VenueTips'})
									.prepend(
										tags({tags:'h3',classes:'text-primary page-header nomargin no-border important'})
										.prepend(
											tags({tags:'i',classes:'glyphicon glyphicon-comment v-align-top'}),
											' Tips'
										),
										div({id:'VenueTipsScroll',classes:'overflow-y border-right border-solid border-1px border-smoke'})
										.prepend(
											tags({tags:'ul',classes:'taglist-flat'})
											.prepend($TipsLists)
										)
									)
								);
							}

						jQuery('body').prepend(
							/* @prepend Add Overlay Element */
							div({
								id: $OverlayVenueInfo,
								classes: 'position-fixed bg-white border-solid border-3px border-right border-smoke'
							})
							.prepend(
								/* @prepend Add Separator & Container Element */
								//div({classes:'separator'}).css({padding:'5px'}),
								div({classes:'container-fluid nopadding'})
								.prepend(
									/* @prepend Close Button */
									tags({tags:'a',href:'javascript:void(0)',classes:'VenueInfoClose btn btn-default'})
									.prepend(
										/* @prepend Glyphicon X */
										tags({tags:'i',classes:'glyphicon glyphicon-remove no-text-decoration'})
									),
									/* @prepend Add BuildHTML variable & container Venue Meta Element */
									div({id:'ContentVenue',classes:'position-relative'})
									.prepend(
										/* @prepend Venue Cover Image/Background Element */
										div({id:'VenueCover',classes:'bg-size-cover vp_big_type'})
										.css({'background-image':'url('+$CoverVenue+')'})
										.prepend(
											tags({tags:'span',id:'PlasticCover'})
										),
										/* @prepend Title Venue Element */
										tags({tags:'h3',id:'TitleVenue',classes:'text-white nomargin'})
										.prepend(
											tags({tags:'span',id:'InnerTitleText'})
											.prepend(
												venueResponse.name,
												'&nbsp;',
												tags({tags:'a',id:'venueSource',href:venueResponse.canonicalUrl,target:'_blank',classes:'small text-white important glyphicon glyphicon-link'})
											),
											tags({tags:'address',classes:'small nomargin text-white important'})
											.text(venueResponse.location.formattedAddress.join(', '))
										),
										/* @prepend Meta Info Element */
										$VenueMetaInfo,
										/* @prepend Photo Venue Lists */
										tags({tags:'ul',id:'VenuePhotos',classes:'taglist-flat d-inline-block w-100cent text-center'})
										.prepend(
											$photoLists
										)
									),
									div({classes:'container-fluid clearfix'})
									.prepend(
										/* @prepend Add Venue Meta Element */
										div({id:'VenueMeta',classes:'inner-separator'})
										.prepend(
											/* @prepend Add Venue Info Element */
											div({id:'VenueMapInfo',classes:'text-right '+classColumnIconInfo+' nopadding'})
											.prepend(
												/* @prepend Add Venue Map Element */
												div({id:'VenueMap'})
												.prepend(
													jQuery('<iframe id="VenueMapIframe" class="no-overflow thumbnail nomargin w-100cent" src="'+MapEndpoint+'" allowfullscreen="" frameborder="0"></iframe>')
												)
											),
											$VenueMeta
										)
									)
								)
							)
							.css({height: '100%',zIndex: '999',width: $spesificWidthOverlay})
							.attr('data-id',$venue_id)
						);

						$scrollHeight = Math.round(jQuery($OverlayVenueInfoID).height()*($spesificPercentHeightBrowser/100));

						jQuery('#OverlayVenueInfo #VenueTipsScroll')
						.css({'max-height':$scrollHeight,width:'99.99%'})
						.enscroll({
						    verticalTrackClass: 'track-inside',
						    verticalHandleClass: 'handle-inside',
						    minScrollbarLength: 28,
						    easingDuration: 50
						});

						jQuery('#OverlayVenueInfo iframe#VenueMapIframe')
						.css({height:$scrollHeight+30})
						.error(function()
						{
							jQuery(this).fadeOut();
						});
				
						jQuery('#OverlayVenueInfo .VenueInfoClose')
						.css({
							position: 'absolute',
							right: '0',
							zIndex: '99',
							margin: '60px 10px 10px'
						})
						.bind('click', function()
						{
							jQuery($OverlayVenueInfoID).remove();
						});

						jQuery('[data-toggle="tooltip"]').tooltip();
					
						NProgress.done();
					})
					.fail(function()
					{
						NProgress.done();
						jQuery.notify('Gagal Loading "'+$venuename+'"...', {pos:'bottom-right', status:'danger'});
					});
				}

				jQuery(document).keyup(function(e)
				{
					if(e.keyCode == 27)
					{
						jQuery($OverlayVenueInfoID).remove();
					}
				});

            } // Batas Launch : function()

        });

        this.init();
        
    };

	$.fn.geomodal = function(options,callback)
	{
        new geomodal(this, options);
        
        $(this).removeAttr('href').addClass('cursor-pointer');
        
		if(callback) callback();

		return this;
	};
})(jQuery);