/*!
 * jQuery Notify Bootstrap
 */
(function(plugin)
{
    var component;

    if(jQuery)
    {
        component = plugin(jQuery);
    }

    if(typeof define == "function" && define.amd)
    {
        define("notify", function()
        {
            return component || plugin(jQuery);
        });
    }

})(function(jQuery)
{

    var containers = {},
        messages   = {},

        notify = function(options)
        {

            if(jQuery.type(options) == 'string')
            {
                options = { message: options };
            }

            if(arguments[1])
            {
                options = jQuery.extend(options, jQuery.type(arguments[1]) == 'string' ? {status:arguments[1]} : arguments[1]);
            }

            return (new Message(options)).show();
        };

    var Message = function(options)
    {
        var jQuerythis = this;

        this.options = jQuery.extend({}, Message.defaults, options);

        this.uuid = "ID"+(new Date().getTime())+"RAND"+(Math.ceil(Math.random() * 100000));
        this.element = jQuery([

            '<div class="alert notify-message">',
                '<button type="button" class="close" aria-hidden="true">&times;</button>',
                '<div>'+this.options.message+'</div>',
            '</div>'

        ].join('')).data("notifyMessage", this);

        // status
        if(this.options.status == 'error')
        {
            this.options.status = 'danger';
        }

        this.element.addClass('alert-'+this.options.status);
        this.currentstatus = this.options.status;

        messages[this.uuid] = this;

        if(!containers[this.options.pos])
        {
            containers[this.options.pos] = jQuery('<div class="notify notify-'+this.options.pos+'"></div>').appendTo('body').on("click", ".notify-message", function()
            {
                jQuery(this).data("notifyMessage").close();
            });
        }
    };


    jQuery.extend(Message.prototype, {

        uuid: false,
        element: false,
        timout: false,
        currentstatus: "",

        show: function()
        {

            if(this.element.is(":visible")) return;

            var jQuerythis = this;

            containers[this.options.pos].css('zIndex', this.options.zIndex).show().prepend(this.element);

            var marginbottom = parseInt(this.element.css("margin-bottom"), 5);

            this.element.css({"opacity":0, "margin-top": -1*this.element.outerHeight(), "margin-bottom":0}).animate({"opacity":1, "margin-top": 0, "margin-bottom":marginbottom}, function()
            {

                if(jQuerythis.options.timeout)
                {

                    var closefn = function()
                    {
                    	jQuerythis.close();
                    };

                    jQuerythis.timeout = setTimeout(closefn, jQuerythis.options.timeout);

                    jQuerythis.element
                    .hover(function()
                    {
                    	clearTimeout(jQuerythis.timeout);
                    },
                    function()
                    {
                    	jQuerythis.timeout = setTimeout(closefn, jQuerythis.options.timeout);
                    });
                }

            });

            return this;
        },

        close: function(instantly)
        {

            var jQuerythis = this,
                finalize = function()
                {
                    jQuerythis.element.remove();

                    if(!containers[jQuerythis.options.pos].children().length)
                    {
                        containers[jQuerythis.options.pos].hide();
                    }

                    jQuerythis.options.onClose.apply(jQuerythis, []);

                    delete messages[jQuerythis.uuid];
                };

            if(this.timeout) clearTimeout(this.timeout);

            if(instantly)
            {
                finalize();
            }
            else
            {
                this.element.animate({"opacity":0, "margin-top": -1* this.element.outerHeight(), "margin-bottom":0}, function()
                {
                    finalize();
                });
            }
        },

    });

    Message.defaults = {
        message: "",
        status: "default",
        timeout: 5000,
        pos: 'top-center',
        zIndex: 10400,
        onClose: function()
        {}
    };
    
    return jQuery.notify = notify
});