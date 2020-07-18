/**
 * On document loaded
 */
document.addEventListener( 'DOMContentLoaded', function (event) {
    jQuery('.mtgtools-card-link').each( function(index, element) {
        mtgtoolsCardLinks.createHoverLink(element);
    });
});

/**
 * Mtgtools Card Links Object
 */
mtgtoolsCardLinks = ( function($) {
    return {
        
        /**
         * Create a new hover-over link
         * 
         * @param {jQuery|Element} element DOM element to create a hover effect on
         */
        createHoverLink: function (element) {
        
            /**
             * Cached jQuery objects
             */
            var link = $(element);
            var popup;

            /**
             * Tracks if popup is open
             */
            var active = false;

            /**
             * Tracks ajax request to prevent duplicate call
             */
            var requestMade = false;

            /**
             * -------------------
             *   H A N D L E R S
             * -------------------
             */
            
            /**
             * Mouse-enter handler
             */
            function mouseEnter() {
                active = true;
                popupExists()
                    ? showPopup()
                    : fetchPopupMarkup();
            }
            
            /**
             * Mouse-leave handler
             */
            function mouseLeave() {
                active = false;
                if ( popupExists() ) {
                    hidePopup();
                }
            }

            /**
             * -----------
             *   A J A X
             * -----------
             */
            
            /**
             * Fetch DOM string from server
             */
            function fetchPopupMarkup() {
                if ( !requestMade ) {
                    requestMade = true;
                    $.post({
                        url: mtgtoolsCardLinkData.ajaxurl,
                        data: getAjaxData(),
                        success: handleResponse
                    });
                }
            }
            
            /**
             * Get data for ajax call
             */
            function getAjaxData() {
                data = link.data();
                data.action = 'mtgtools_get_card_popup';
                data._wpnonce = mtgtoolsCardLinkData.nonce;
                return data;    
            }

            /**
             * Handle server response
             * 
             * @param {PlainObject} data Result of the ajax request
             * @param {string} status Status of the request
             */
            function handleResponse(data, status) {
                if ('success' === status && data.success) {
                    createPopup(data.data.transients.popup);
                    if (active) {
                        showPopup();
                    }
                } else {
                    console.log("Error encountered. Data returned by server:");
                    console.log(data.data);
                }
            }

            /**
             * -------------
             *   P O P U P
             * -------------
             */
            
            /**
             * Check if popup element has been created
             */
            function popupExists() {
                return 'undefined' !== typeof popup;
            }

            /**
             * Show popup
             */
            function showPopup() {
                popup.removeClass('hidden').fadeIn();
            }

            /**
             * Hide popup
             */
            function hidePopup() {
                popup.addClass('hidden').hide();
            }

            /**
             * Create popup
             * 
             * @param {string} transient HTML string to convert to nodes
             */
            function createPopup(transient) {
                html = $.parseHTML( transient );
                popup = $(html).appendTo(link);
            }

            /**
             * -----------
             *   B I N D
             * -----------
             */
            
            /**
             * Bind event handlers
             */
            link.hover( mouseEnter, mouseLeave );
        }
        
    };
})(jQuery);