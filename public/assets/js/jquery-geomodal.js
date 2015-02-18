;(function($, window, document, undefined)
{
	var geomodal = 'geomodal',
	$element,
	$defaults,
	$geomodal,
	$settings,
	$wrapperW,
	$containerSideW,
	$refpercent,
	$siderightW,
	$spesificWidthOverlay,
	$OverlayVenueInfo,
	$OverlayVenueInfoID,
	$validate,
	$locale,
	$venue_id,
	defaults = {
		wrapper: '#wrapper',
		refpercent: '#sideright',
		OverlayVenueInfo: 'OverlayVenueInfo'
	};

    function GeoModal( element, options )
    {
        $element = element;
        $settings = $.extend( {}, defaults, options) ;
        
        $defaults = defaults;
        $geomodal = geomodal;
 
		$wrapper = $settings.wrapper;
		
		$wrapperW = jQuery(wrapper).width();
		
		$containerSideW = wrapperW*33.5/100;
		
		$refpercent = $settings.refpercent;
		
		$siderightW = jQuery(refpercent).width();
		
		$spesificWidthOverlay = Math.round(wrapperW-containerSideW);
		
		$OverlayVenueInfo = $settings.OverlayVenueInfo;
		
		$OverlayVenueInfoID = '#'+OverlayVenueInfo;

		$validate = vars.date;
		
		$locale = sitevar.lang;

		$venue_id = [];

        this.init();
    }

    Plugin.prototype = {
	    init: function()
	    {
		    if ( $.isFunction( $settings.complete ) ) {
		        $settings.complete.call( this );
		    }
		}
    };

    $.fn[geomodal] = function( options )
    {
        return this.each(function()
        {
            if(!$.data(this, 'plugin_' + geomodal))
            {
                $.data(this, 'plugin_' + geomodal, 
                new Plugin( this, options ));
            }
        });
    }
})(jQuery, window, document);