/*!
 * General Script
 */
NProgress.configure({ showSpinner: false });

jQuery(document).ready(function()
{
	$tooltipClass = '[data-toggle="tooltip"], .tooltiped';

	jQuery($tooltipClass).tooltip();

	/* getting viewport width */
	var responsive_viewport = jQuery(window).width() + getScrollBarWidth();

	var windowsHeight = jQuery(window).height();
	var hmtlHeight = jQuery(document).find('html').height();
	var bodyHeight = jQuery(document).find('body').height();
	var wrapperHeight = jQuery(document).find('#wrapper').height();

	//var spesificPercentHeightBrowser = jQuery.browser.mozilla ? 83.5 : 83.5;
	var sideScrollHeight = Math.round(windowsHeight*(83.5/100));

	//alert(bodyHeight+' '+wrapperHeight);

	//console.log(windowsHeight);

	jQuery('#back-top')
	.css({
		right:7,
		bottom:7,
		zIndex:9999,
		opacity:'0.5'
	})
	.bind('mouseover',function()
	{
		jQuery(this)
		.css({opacity:'1'});
	})
	.bind('mouseleave',function()
	{
		jQuery(this)
		.css({opacity:'0.5'});
	})
	.children('a')
	.css({
		padding:'12px 8px',
		'border-radius':'5%'
	});

	/* if is larger than 481px */
	if(responsive_viewport >= 300)
	{
		// hide #back-top first
		jQuery("#back-top").hide();

		// fade in #back-top
		jQuery(function()
		{
			jQuery(window)
			.scroll(function()
			{
				if (jQuery(this).scrollTop() > 300)
				{
					jQuery('#back-top').fadeIn();
				}
				else
				{
					jQuery('#back-top').fadeOut();
				}
			});

			// scroll body to 0px on click
			jQuery('#back-top a')
			.bind('click', function()
			{
				return scrollToTag({tags:'body,html',direct:'up',speed:500});
			});
		});
	}


	/* Set default wrapper height */
	jQuery(document).find('#wrapper').css({'min-height':windowsHeight-60});
	
	/* Set turbolink off if page is map */
	if(jQuery(document).find('html').attr('data-page')=='map')
	{
		jQuery(document).find('a').each(function()
		{
			jQuery(this).attr({'data-no-turbolink':''});
		});
		
		jQuery('form').removeAttr('data-turboform');
	}


	/* Set default enscroll DOM */
	jQuery('#insideleft, #insideright, #sideright, .page-scroll')
	.css({'height':sideScrollHeight,width:'99,99% !important'})
	.enscroll({
	    verticalTrackClass: 'track-inside',
	    verticalHandleClass: 'handle-inside',
	    minScrollbarLength: 28,
	    easingDuration: 50
	});


	/* Set wait image display when page is finish load */
	jQuery('#inside img, #sideright img, .page-scroll img')
	.waitForImages().done(function()
	{
	    jQuery(this).fadeIn()
		.lazy({
			appendScroll: jQuery('#inside, #sideright, .page-scroll')
			//effect: "fadeIn",
			//effectTime: 1500,
			//threshold: 0
	 	});
	});


	/* Search input box */
	var removeinput = '<a href="javascript:void(0)" class="removeinputsearch"></a>';
	var TopSearchBox = '#topsearchbox';
	var RemoveInputSearch = 'a.removeinputsearch';
	var topsearchboxWidth = jQuery(TopSearchBox).width();

	$suggestOpt = jQuery(TopSearchBox).parent('div').parent('form').position().top > jQuery('#TopNavbar').position().top ? 
				{
					position:
					{
						width: topsearchboxWidth+20,
						bottom: 34,
						zIndex: 9999,
						'border-top-right-radius': 5,
						'border-top-left-radius': 5,
						'box-shadow': '0px -2px 5px rgba(0, 0, 0, 0.15)'
					},
					limit:3
				}
				 : 
				{
					position:
					{
						width: topsearchboxWidth+20,
						top: 34,
						zIndex: 9999,
						'border-bottom-right-radius': 5,
						'border-bottom-left-radius': 5,
						'box-shadow': '0px 3px 5px rgba(0, 0, 0, 0.45)'
					},
					limit:5
				};

	$classes = jQuery(TopSearchBox).parent('div').parent('form').position().top > jQuery('#TopNavbar').position().top ? 
				' bg-white border-primary border-1px border-solid border-left-right-top text-primary' : 
				' bg-white border-primary border-1px border-solid border-left-right-bottom text-primary';

	jQuery(TopSearchBox)
	.keyup(function()
	{	
		$this = this;

		if(jQuery($this).val().length > 0)
		{
			jQuery(RemoveInputSearch).show();

			if(jQuery($this).val().length >= 4)
			{
				jQuery.post(sitevar.domain+'/ajax.php',
				{
			    	'param':JSON.stringify({'city':jQuery($this).val(),'limit':$suggestOpt.limit}),
		    		'method':JSON.stringify({'m':'control','c':'search','f':'auto_suggest'})
				})
				.done(function(data)
				{
					//alert(data);
					//console.log(data);
					jQuery('#city-suggest').remove();

					if(data != 0)
					{
						jQuery($this).parent('div').parent('form')
						.prepend(
							div({'id':'city-suggest','classes':'clearfix position-absolute w-100cent'+$classes})
							.css($suggestOpt.position)
							.prepend(
								div({'classes':'clearfix'})
								.prepend(data)
							)
						);

						jQuery('.selectCity')
						.bind('click', function(e)
						{
							$text = jQuery(this).text();
							jQuery($this).val($text);
							jQuery('#city-suggest').remove();
							jQuery($this).parent('div').parent('form')
							.trigger('submit');
							//location.href = sitevar.domain+'?cari='+encodeURIComponent(jQuery(TopSearchBox).val());

						});
					}
				})
				.fail(function(respon)
				{
					jQuery.notify('Gagal Suggest: "'+respon.responseText+'"', {pos:'bottom-right', status:'danger'});
				});
			}
		}
		else
		{
			jQuery('#city-suggest').remove();
		}
	})
	.focus(function()
	{
		if(jQuery(this).val().length > 0)
		{
			jQuery(RemoveInputSearch).show();
			
			jQuery('#city-suggest').show();
		}
	})
	.blur(function()
	{
		if(jQuery(this).val().length < 1)
		{
			jQuery(RemoveInputSearch).hide();
		}
	})
	.parent('.input-group').append(removeinput)
	/*.parent('form')
	.bind('mouseover', function()
	{
		jQuery('#city-suggest').show();
	})
	.bind('mouseleave', function()
	{
		jQuery('#city-suggest').hide();
	})*/;
	
	if(jQuery(TopSearchBox).length !== 0)
	{
		if(jQuery(TopSearchBox).val().length < 1)
		{
			jQuery(RemoveInputSearch).hide();
		}
	}

	jQuery(document).keyup(function(e)
	{
		if(e.keyCode == 27)
		{
			jQuery('#city-suggest').remove();
		}
	});

	jQuery(RemoveInputSearch)
	.addClass('glyphicon glyphicon-remove no-text-decoration')
	.css({position: 'absolute',
		  color: '#fff',
		  'font-size': '10px',
		  background: '#CB003F',
		  zIndex: '999',
		  right: '50px',
		  'border-radius': '50%',
		  top: '7px',
		  padding: '5px'
	})
	.bind('click',function()
	{
		jQuery(TopSearchBox).val('').focus();
		jQuery(this).hide();
		jQuery('#city-suggest').remove();
	});
/*console.log(jQuery('footer').position().top);
console.log(jQuery('#TopNavbar').position().top);
console.log(jQuery('form[role="search"]').position().top);
console.log(jQuery('footer').outerHeight(true));
console.log(jQuery('#TopNavbar').outerHeight(true));*/
});

$.fn.animateRotate = function(angle, duration, easing, complete) {
    var args = $.speed(duration, easing, complete);
    var step = args.step;
    return this.each(function(i, e) {
        args.step = function(now) {
            $.style(e, 'transform', 'rotate(' + now + 'deg)');
            if (step) return step.apply(this, arguments);
        };

        $({deg: 0}).clearQueue().stop().animate({deg: angle}, args)
        .removeAttr('style');
    });
};

function callbackScript(src,callback){
	var script = document.createElement("script");
	script.type = "text/javascript";

	if(callback) script.onload = callback;
	
	document.getElementsByTagName("head")[0].appendChild(script);
	script.src = src;
}

function loadAndExecuteScripts(aryScriptUrls, index, callback)
{
	jQuery.getScript(aryScriptUrls[index], function()
	{
		if(index + 1 <= aryScriptUrls.length - 1) {
		loadAndExecuteScripts(aryScriptUrls, index + 1, callback);
	}
	else
	{
		if(callback)
		callback();
	}
	});
}

function div(attr)
{
	var id = attr.id ? 'id="'+attr.id+'"' : '',
	classes = attr.classes ? ' class="'+attr.classes+'"' : '';

	return jQuery('<div '+id+''+classes+' />');
}

function tags(attr)
{
	var tags = attr.tags ? attr.tags : 'span',
	id = attr.id ? ' id="'+attr.id+'"' : '',
	classes = attr.classes ? ' class="'+attr.classes+'"' : '',
	href = attr.href ? ' href="'+attr.href+'"' : '',
	target = attr.target ? ' target="'+attr.target+'"' : '';

	return jQuery('<'+tags+''+id+''+classes+''+href+''+target+' />');
}

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

function scrollToTag(set)
{
	if(set)
	{
		var scrollTag = jQuery(document).find(set.tags);
		
		var direct = set.direct ?
						set.direct == 'bottom' ? 
							{scrollBottom: scrollTag.offset().bottom} 
						: {scrollTop: scrollTag.offset().top}
					: {scrollBottom: scrollTag.offset().bottom};
		
		var speed = set.speed ? set.speed : 'slow';

		jQuery('html,body').animate(direct,speed);
	}
	else
	{
		jQuery('html,body').animate({scrollTop: jQuery('body').offset().top},'slow');
	}
}

function exitFullscreen(callback)
{
	if(document.exitFullscreen)
	{
		document.exitFullscreen();
		return callback;
	}
	else if(document.mozCancelFullScreen)
	{
		document.mozCancelFullScreen();
		return callback;
	}
	else if(document.webkitExitFullscreen)
	{
		document.webkitExitFullscreen();
		return callback;
	}
}


//Calculate the width of the scroll bar so css media queries and js widow.width match
function getScrollBarWidth () {
	var inner = document.createElement('p');
	inner.style.width = "100%";
	inner.style.height = "200px";

	var outer = document.createElement('div');
	outer.style.position = "absolute";
	outer.style.top = "0px";
	outer.style.left = "0px";
	outer.style.visibility = "hidden";
	outer.style.width = "200px";
	outer.style.height = "150px";
	outer.style.overflow = "hidden";
	outer.appendChild (inner);

	document.body.appendChild (outer);
	var w1 = inner.offsetWidth;
	outer.style.overflow = 'scroll';
	var w2 = inner.offsetWidth;
	if (w1 == w2) w2 = outer.clientWidth;

	document.body.removeChild (outer);

	return (w1 - w2);
}

function urlSlug(text)
{
	return text.replace(new RegExp(/([^\w+^\s])/g),'').replace(/\s/g,'-').toLowerCase();
}

function loadGeomodal(elm)
{
	jQuery(elm).geomodal();
}