/**
 * Emojify all text nodes in a document
 * @author Tim Whitlock
 */
( function( window, document ){
    
    // find our own url amongst script sources
    //
    function myUrl(){
        var i = -1,
            baseurl = '',
            scripts = document.documentElement.getElementsByTagName('script');
        while( ++i < scripts.length ){
            baseurl = scripts[i].getAttribute('src');
            if( baseurl && /js\/emoji(\.min)?\.js/.test(baseurl) ){
                return baseurl;
            }
        }
        return baseurl;
    }

    // Configure:
    var confClass  = 'emojified',
        confTheme  = 'android',
        confScript = myUrl(),
        confQuery  = confScript.split('?')[1];
                
    // establish emoji theme and add to wrapper class
    if( /theme=(\w+)/.exec(confQuery) ){
        confTheme = RegExp.$1;
    }
        
        
    // No point loading Android theme if user is on Android
    if( 'android' === confTheme && -1 !== navigator.userAgent.indexOf('; Android') ){
        return;
    }    


    // logging only when console is available.
    //
    function log( text ){
        window.console && console.log && console.log(text);
    }
    
 
 
    // utility converts surrogate pair to single integer
    //
    function compileSurrogate( hi, lo ){
        return ( (hi - 0xD800) * 0x400 ) + (lo - 0xDC00) + 0x10000;
    }
    
    

    // find emoji in a string
    //
    function findEmoji( text ){
        if( ! text ){
            return [];
        }
        // single emoji range:   [\u203C-\uFFFF]
        // surrogate pair range: [\uD800-\uDBFF][\uDC00-\uDFFF]
        var found = [],
            reg = /[\u203C-\uFFFF][\uDC00-\uDFFF]?/g,
            matches, match, lo, hi, index;

        while( matches = reg.exec(text) ) {
            match = matches[0];
            index = reg.lastIndex;
            hi = match.charCodeAt(0);
            lo = match.charCodeAt(1);
            // single emoji if no low sibling
            if( isNaN(lo) ){
                found.push( [ --index, 1, hi, match ] );
                //log('single at '+index+' '+hi.toString(16) );
            }
            // else two single emojis if sibling is not in surrogate range
            else if( hi < 0xD800 || hi > 0xDBFF ){
                found.push( [ --index, 1, lo, match ] );
                found.push( [ --index, 1, hi, match ] );
                //log('two at '+index+' '+hi.toString(16)+' '+lo.toString(16) );
            }
            // else bingo - a surrogate pair we have
            else {
                found.push( [ index-=2, 2, compileSurrogate(hi,lo), match ] );
                //log( 'surrogate at '+index+' '+compileSurrogate(hi,lo).toString(16) );
            }
        }
        return found;
    }
        
    
    
    /**
     * Convert function to be run on any window
     * All inner functions are closures on the window/document context
     */
    function convert( window ){
        
        var document = window.document,
            body = document.body,
            head = document.head || document.getElementsByTagName('head')[0];
    
        // ignore non-html pages
        if( ! body || ! head ){
            return;
        }
    

        // simple span element creator
        //
        function createElement( className, innerText, tagName  ){
            var span = document.createElement( tagName||'span' );
            span.className = className || '';
            innerText && span.appendChild( createText(innerText) );
            return span;
        }
    
        
        
        // simple text node creator
        //
        function createText( innerText ){
            return document.createTextNode( innerText );
        }
        
        
        
        // convert emoji in a text node
        //
        function emojifyTextNode( textNode ){
    
            var text = textNode.nodeValue,
                found = findEmoji( text );
            if( ! found.length ){
                return;
            }
    
            // build new element stucture inside single span to keep child node count the same
            var wrapNode = createElement(confClass), 
                i = -1, next = 0, index, code, length;
    
            while( ++i < found.length ){
                match  = found[i][3];
                index  = found[i][0];
                length = found[i][1];
                code   = found[i][2];
                // content before emoji element if we've skipped some text
                if( index > next ){
                    wrapNode.appendChild( createText( text.substring( next, index ) ) );
                }
                // splice in emoji element
                wrapNode.appendChild( createElement( 
                    'emoji emoji-'+code.toString(16), match
                ) );
                // ready for next
                emojifyCount++;
                next = index + length;
            }
            // pick up any remaining text
            if( text.length > next ){
                wrapNode.appendChild( createText( text.substr(next) ) );
            }
            textNode.parentNode.replaceChild( wrapNode, textNode );
        }
    
        
        
        // convert emoji in an element attribute
        //
        function emojifyAttribute( element, attr, text ){
            text = text || element.getAttribute(attr);
            var found = findEmoji( text );
            if( ! found.length ){
                return;
            }          
            // can't do replacement in element attribute, can only set classname and add to count
            var classes = element.className ? element.className.split(/\s+/) : [];
            classes.push(confClass);
            element.className = classes.join(' ');
            emojifyCount += found.length;
        }
        
        
        
        // Recursively find every text node and its parent element
        //
        function descend( parent ){
            // skip node if it's already converted in a previous run
            if( -1 !== parent.className.indexOf(confClass) ){
                return;
            }
            // skip node if it has no children
            var length = parent.childNodes.length;
            if( ! length ){
                // support field input values
                parent.hasAttribute('value') && emojifyAttribute( parent, 'value', parent.value );
                return;
            }
            // skip tags we know will never contain emoji
            switch( parent.tagName ){
            case 'STYLE':
            case 'SCRIPT':
                return;
            }
            // ok to descend into this element
            var i = -1, child;
            while( ++i < length ){
                child = parent.childNodes[i];
                switch( child.nodeType ){
                case 1:
                    descend( child );
                    break;
                case 3:
                    emojifyTextNode( child );
                    break;
                }
            }
        }
        
        
        // convert all text nodes starting with body of current window
        var emojifyCount = 0;
        descend( body );
        //log('Emojified: '+emojifyCount);
        
        // load CSS and Emoji fonts if needed and not already loaded
        if( emojifyCount && window.OSEmojiLoaded !== confTheme ){
            var id = 'osemojicss',
                link = document.getElementById(id);
            link && head.removeChild(link);         
            link = createElement('','','link');
            link.setAttribute('href', confScript.replace(/\/js\/emoji(\.min)?\.js/,'/css/emoji-'+confTheme+'.css') );
            link.setAttribute('rel','stylesheet');
            link.setAttribute('id', id );
            head.appendChild( link );
            window.OSEmojiLoaded = confTheme;
        }
    
    }
    
    
    // expose function to global scope
    window.OSEmoji = {
        run: convert
    };
    
    convert( window );
    
} )( window, document );
