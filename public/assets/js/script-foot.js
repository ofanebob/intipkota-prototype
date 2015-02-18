if(typeof videojs !== "undefined")
{
	videojs.options.flash.swf = sitevar.domain+"/public/assets/flash/video-js.swf";
}

jQuery(document).ready(function()
{
	if(typeof videojs !== "undefined")
	{
		var video = videojs('footage-video')
		.ready(function()
		{
			var player = this;

			player.on('ended', function()
			{
				return scrollToTag({tags:'#kota-favorit',direct:'up',speed:500});
			})
			.volume(0.5);
		});
	}

	$popoverClass = '[data-toggle="popover"], .popovers';

	/*if(varfoot.data_page !== 'map')
	{
		jQuery('body').removeAttr('style');
	}*/

	jQuery(document).find('html').attr({'data-page':varfoot.data_page});

	if(jQuery('html').attr('data-page')=='home')
	{
		jQuery(document).find('#TopNavbar').addClass('navbar-fixed-top');
	}
	else
	{
		jQuery(document).find('#TopNavbar').removeClass('navbar-fixed-top');
	}

	jQuery($popoverClass)
	.popover(
		{
			html:'true',
			title:'<i class="glyphicon glyphicon-bullhorn"></i> Info',
			trigger:'hover'
		}
	);

    if( typeof panoramio !== "undefined" && panoramio !== null )
    {
		var targetScroll = '.load_more_panoramio';
		var panoramioPhotos = panoramio.photos;
		var panoramioPhotosTotal = panoramioPhotos.length;
		//$replacer = 'http://static.panoramio.com/photos/small/$3';
		var panoramioPush;

		jQuery(document)
		.find('#insideright')
		.scroll(function(event)
		{
			$this = this;

			var lastPhotos = jQuery($this).find('ul#panoramioImages li:last-child').attr('id').split('-');
	        var lastPhotosID = parseInt(lastPhotos[1]);

			var visible = jQuery($this).find(targetScroll).visible();

		    if(visible)
		    {
		    	var LimitPanoramio = panoramioPhotos.slice(lastPhotosID,lastPhotosID+4);

		    	panoramioPush = [];

				jQuery.each(LimitPanoramio, function(e,panoramio)
				{
					if( (e+lastPhotosID+1) <= panoramioPhotosTotal )
					{
						//$imagesFix = panoramio.photo_file_url.replace( new RegExp(/(.+)([\/])([0-9]+.+)/),$replacer);
						$imagesFix = panoramio.photo_file_url;

			        	panoramioPush.push(
			        		tags({
			        			tags:'li',
			        			id:'panoramio-'+(e+lastPhotosID+1),
			        			classes:'clearfix border-bottom border-smoke border-1px border-solid marginspace-bottom'
			        		})
			        		.attr('data-id',panoramio.photo_id)
			        		.prepend(
								tags({tags:'h5',classes:'nomargin'}).css({'margin-bottom':10})
								.prepend(
									tags({tags:'i',classes:'glyphicon glyphicon-paperclip'}),
									'&nbsp;',
									tags({tags:'span'}).text(panoramio.photo_title)
								),
								tags({tags:'a',classes:'thumbnail shaping d-inline-block w-100cent'})
								.attr({'href':panoramio.photo_url,'target':'_blank'})
								.prepend(
									tags({tags:'img',classes:'d-inline-block w-100cent'})
									.attr({'alt':panoramio.photo_title,'src':$imagesFix})
								)
			        		)
			        	);
			        }
			        else
			        {
			        	panoramioPush.push(0)
			        }
				});

				if(panoramioPush == 0)
				{
					jQuery($this)
					.find(targetScroll).remove();
				}
				else
				{
					jQuery($this).find('ul#panoramioImages')
					.append(panoramioPush);
				}
		    }
		});
	}


    if ( typeof foursquare !== "undefined" && foursquare !== null )
    {
    	//console.log(foursquare);

		var targetScrollFsq = '.load_more_foursquare',
		foursquareTotal = foursquare.response.groups[0].items.length,
		FsqPush,

		$VenueRating, /* @var element span */
		$VenueCategory, /* @var element span */
		$VenueSpecials, /* @var element span */
		$VenuePhotos, /* @var element img */
		$VenuePhotosURL,
		$fixCanonical

		jQuery(document)
		.find('#sideright')
		.scroll(function(event)
		{
			$this = this;

			var lastVenue = jQuery($this).find('div#foursquareVenues div.foursquare:last-child').attr('id').split('-');
	        var lastVenueID = parseInt(lastVenue[1]);

			var visible = jQuery($this).find(targetScrollFsq).visible();

		    if(visible)
		    {
		    	var LimitVenue = foursquare.response.groups[0].items.slice(lastVenueID,lastVenueID+4);

		    	FsqPush = [];

				jQuery.each(LimitVenue, function(e,FsqVenue)
				{
					Fsq = FsqVenue.venue;

					if( (e+lastVenueID+1) <= foursquareTotal )
					{
						var vrate = Fsq.rating ? Fsq.rating : 0;

						if(vrate!=0)
						{
							var RatingColor = rating_color(vrate);
							$VenueRating = tags({tags:'span',classes:'label label-'+RatingColor[1]})
											.text('Rating: '+vrate);
						}

						$VenueCategory = Fsq.categories ? tags({tags:'span',classes:'label label-primary'}).text(Fsq.categories[0].name) : '';

						$VenueSpecials = Fsq.specials ? Fsq.specials.count > 0 ? 
										tags({
											tags:'span',
											classes:'specials-mini glyphicon glyphicon-certificate text-warning v-align-top'
										}) : '' : '';

						$VenuePhotosURL = Fsq.photos ? Fsq.photos.groups.length > 0 ?
						Fsq.photos.groups[0].items[0].prefix+'80x100'+Fsq.photos.groups[0].items[0].suffix
						: sitevar.domain+'/public/images/no-image-80x100.jpg' : '';

						$VenuePhotos = tags({
											tags:'img',
											classes:'thumbnail waitForImages'
										})
										.attr({
											'src':$VenuePhotosURL,
											'data-holder-rendered':'true',
											'alt':'80x100'
										})
										.css({
											width: 80,
											height: 100
										});

						$fixCanonical = 'https://id.foursquare.com/v/'+urlSlug(Fsq.name)+'/'+Fsq.id;

			        	FsqPush.push(
			        		div({
			        			id:'fsq-'+(e+lastVenueID+1),
			        			classes:'media foursquare border-smoke border-bottom border-1px border-solid'
			        		})
			        		.attr('data-id',Fsq.id)
			        		.prepend(
			        			tags({tags:'a',id:Fsq.id,classes:'pull-left quick-view-map cursor-pointer'})
			        			.attr({'href':$fixCanonical,'target':'_blank'})
			        			.prepend($VenuePhotos),
			        			div({classes:'media-body'}).prepend(
			        				div({classes:'media-heading'}).prepend(
			        					$VenueSpecials,
			        					' ',
			        					$VenueCategory,
			        					' ',
			        					$VenueRating
			        				),
			        				div({classes:'break-word-all'}).prepend(
			        					tags({tags:'h5'}).prepend(
						        			tags({tags:'a',id:Fsq.id,classes:'quick-view-map d-inline-block w-100cent cursor-pointer'})
						        			.attr({'href':$fixCanonical,'target':'_blank'})
						        			.prepend(Fsq.name)
			        					),
			        					tags({tags:'address'}).prepend(Fsq.location.formattedAddress.join(', '))
			        				)
			        			)
			        		)
			        	);
			        }
			        else
			        {
			        	FsqPush.push(0)
			        }
				});

				if(FsqPush == 0)
				{
					jQuery($this)
					.find(targetScrollFsq).remove();
				}
				else
				{
					jQuery($this).find('div#foursquareVenues')
					.append(FsqPush);
					jQuery('.quick-view-map').geomodal();
				}
		    }
		});
	}


	if(varfoot.paging)
	{
		var cookieName = varfoot.paging.f;
		
	    if( jQuery.cookie )
	    {
	        var cookiePaging = jQuery.cookie(jQuery.md5(cookieName));

	        if ( cookiePaging !== null && typeof cookiePaging !== "undefined" )
	        {

	        	jQuery('.paginationNumber').parent('li').removeClass('active');

		        jQuery('#'+cookieName+' #inner'+cookieName)
		        .load(sitevar.domain+'/ajax.php',
			    {
			    	'param':JSON.stringify({'pages':cookiePaging-1}),
		    		'method':JSON.stringify(varfoot.paging)
				},
		        function(response, status, xhr)
		        {
					if( xhr.status != 200 )
					{
						jQuery.notify('Gagal Loading: "'+xhr.statusText+'"', {pos:'bottom-right', status:'danger'});
					}
					else
					{
						jQuery($popoverClass)
						.popover(
							{
								html:'true',
								title:'<i class="glyphicon glyphicon-bullhorn"></i> Info',
								trigger:'hover'
							}
						);
					}
		        });

		        jQuery('.paginationNumber#'+(cookiePaging)+'-page').parent('li').addClass('active');
	        }
		    else
		    {
		    	jQuery('#1-page').parent('li').addClass('active');
		    }
	    }

	    jQuery('body')
	    .on('click', '.paginationNumber', function(e)
	    {
	        NProgress.start();
			
			e.stopPropagation();
			e.preventDefault();

	        var clicked_id = jQuery(this).attr('id').split('-');
	        var page_num = parseInt(clicked_id[0]);
	       
	        jQuery('.paginationNumber').parent('li').removeClass('active');

	        jQuery('#'+cookieName+' #inner'+cookieName)
	        .fadeTo(200, .3)
	        .load(sitevar.domain+'/ajax.php',
		    {
		    	'param':JSON.stringify({'pages':page_num-1}),
	    		'method':JSON.stringify(varfoot.paging)
			},
	        function(response, status, xhr)
	        {
	        	jQuery(this).fadeTo(200, 1);

				if( xhr.status != 200 )
				{
					jQuery.notify('Gagal Loading: "'+xhr.statusText+'"', {pos:'bottom-right', status:'danger'});
				}
				else
				{
				    if(jQuery.cookie)
				    {
				        jQuery.cookie(jQuery.md5(cookieName), page_num);
				    }

					jQuery($popoverClass)
					.popover(
						{
							html:'true',
							title:'<i class="glyphicon glyphicon-bullhorn"></i> Info',
							trigger:'hover'
						}
					);
				}

				NProgress.done();
	        });

	        jQuery(this).off('click').parent('li').addClass('active');
	    });
	}

	jQuery('.quick-view-map').geomodal();
});