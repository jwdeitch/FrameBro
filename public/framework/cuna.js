/**
 * Created by Jon Garcia on 10/5/15.
 * This file handles all ajax requests set up in the backend via the AjaxRequest API
 */
c = new Cuna();

$( document ).ready( function() {

    /**
     * bind elements to the dom()
     */
    bindElements();
    bindRequest();

    //$(document).on('mousemove', 'body', function() {
    //    bindRequest();
    //});
});

var bindElements = function() {
    /**
     * make every element with class of datepicker a datepicker element.
     */
    $( ".datepicker" ).datepicker({
        changeYear: true,
        yearRange: "c-60:c-00"
    });

    /**
     * if text item is of class text-list, let's make it add as list
     */
    if ($.fn.textList !== undefined) {
        $(".text-list").textList();
    }

    $(".file-upload").upload();

};

$(document).on({
    ajaxStart: function() {
        varLoading = '<div class="modal-loading"></div>';
        $('body').append(varLoading).addClass("loading");
    },
    ajaxStop: function() {
        $('body').removeClass("loading");
        $('.modal-loading').remove();
    }
});

/**
 * bind events
 * */
var bindRequest = function() {
    $.each(c.settings.Ajax, function () {
        c.currentRequest = this;
        $(c.currentRequest.selector).on(c.currentRequest.event, function (e) {
            e.preventDefault();
            var isForm = $(this).closest('form').is('form') ? $(this).closest('form') : false;

            var processData = {};
            var request;
            if (isForm) {
                $(isForm).on('submit', function (e) {

                    processData = new FormData();
                    $.each(e.target.elements, function( input, value ) {
                        if ( value.type === "checkbox" || value.type === 'radio' ) {
                            if ( value.checked === true ) {
                                processData.append(value.name, value.value);
                            }
                        } else if ( value.className.indexOf('ckeditor') > -1 ) {
                            thisValue = CKEDITOR.instances[value.name].getData();
                            processData.append(value.name, thisValue);
                        } else {
                            processData.append(value.name, value.value);
                        }
                    });
                    processData.append('ajax', JSON.stringify(c.currentRequest));
                    request = processData;

                    e.preventDefault();
                });
                $(isForm).submit();
                //return;
            } else {
                $(this).val() ? processData['value'] = $(this).val() :
                    ( $(this).attr('value') ? processData['value'] = $(this).attr('value') : null );
                $(this).attr('data-collect') ? processData['data-collect'] = $(this).attr('data-collect') : null;
                $(this).attr('href') ? processData['href'] = $(this).attr('href') : null;
                $(this).text() ? processData['text'] = $(this).text() : null;

                $.extend(processData, { ajax:c.currentRequest});
                request = JSON.stringify(processData);
            }
            c.Request('/AjaxController', request, c.currentRequest.httpMethod);
        });
    });
};

/**
 * after ajax request.
 */
c.complete( function (e) {
    if (c.responseStatus === 200 && c.requestUrl === '/AjaxController') {
        if ( c.currentRequest.customHandler ) {
            var callback = new Function( c.currentRequest.customHandler );
            callback( c.response );
        } else {

            /**
             * if there's a redirect message, drop everything and do it.
             */
            if (c.responseRedirect !== undefined) {
                window.location.href = c.responseRedirect;
            } else {

                /**
                 * otherwise, let's continue updating the DOM.
                 * @type {*|string|string}
                 */
                var jsMethod = c.currentRequest.method;
                var effect = c.currentRequest.effect;
                var wrapper = c.currentRequest.wrapper;
                var el = $(c.response).css('display', 'none');
                $('#window-alerts').remove();
                $(wrapper)[jsMethod](el);
                $(el)[effect]();
            }

            /**
             * reset a few values and bind events to DOM again.
             * @type {null}
             */
            c.responseStatus = c.currentRequest = c.response = null;
            bindRequest();
            bindElements();
            bindCKeditor();
        }
    }
});


/**
 * Object Cuna Framework
 * @constructor
 */
function Cuna() {
    return {
        /**
         * event to fire after ajax request.
         * @type {Event}
         */
        requestComplete: new Event('requestComplete'),

        /**
         * Sends ajax request to server and dispatches event.
         * @param url
         * @param requestData
         * @param method
         * @constructor
         */
        Request: function (url, requestData, method) {
            var that = this;
            that.response = null;
            that.responseStatus = null;
            var contentType = (requestData instanceof FormData) ? false : 'application/json';
            method = method || 'POST';
            $.ajax({
                method: method,
                url: url,
                data: requestData,
                cache: false,
                dataType: 'json',
                processData: false, // Don't process the files
                contentType: contentType
            }).done(function (response) {
                that.response = response.data;
                that.responseStatus = response.status;
                that.responseRedirect = response.redirect;
                that.requestUrl = url;
                document.dispatchEvent(that.requestComplete);
            });
            return this;
        },

        requestUrl: '',
        /**
         * this page's ajax settings
         */
        settings: {},
        /**
         * the current request params
         */
        currentRequest: {},
        /**
         * the response data after an ajax call
         */
        response: null,
        /**
         * the response status code after an ajax call
         */
        responseStatus: null,

        /**
         * if there's a response redirect, we redirect to that location
         */
        responseRedirect: null,

        /**
         * event listener
         * @param callback
         */
        complete: function( callback ) {
            var that = this;
            document.addEventListener('requestComplete', function (e) {
                if (typeof callback === 'function') {
                    callback(e);
                    that.jumpToAlert('window-alerts');
                }
            }, false);
        },

        /**
         * if there's an alert in the screen, let's jump to it.
         */
        jumpToAlert: function( anchor ) {
            var url = location.href;
            location.href = "#" + anchor;
            history.replaceState(null, null, url);
        }
    }
}

var bindCKeditor = function() {
    $(".ckeditor").each(function(){
        CKEDITOR.replace( this.name );
    });
};