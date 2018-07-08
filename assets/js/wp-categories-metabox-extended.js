/**
 * WP Categories Metabox Extended JS.
 *
 * Handle AJAX calls pertaining to the WP Admin Editor.
 *
 * @author Maria Daniel Deepak Edward Lawrence.
 * @since  1.0.0
 */

/*global
    ajax_object
 */
(function ($) {

    var WPCategoriesMetaboxExtended = ( function () {

        'use strict';

        var DOM = {},
            isExistingTermsSet = false;

        /**
         * @since 1.0.0
         */
        function init() {
            cacheDOM();
            bindEvents();
            loadTerms();
        }

        /**
         * Cache DOM elements to be used later.
         *
         * @since 1.0.0
         */
        function cacheDOM() {
            DOM.$jsAjaxObject  = ajax_object;
            DOM.$termsUL       = $( '#wpcme-terms-ul' );
            DOM.$searchInput   = $( '#wpcme-search-term' );
            DOM.$ajaxSpinner   = $( '.wpcme-spinner' );
            DOM.$selectedTerms = $( '.selected-terms' );
            DOM.$clearBtn      = $( '#wpcme-search-clear' );
        }

        function bindEvents() {
            DOM.$searchInput.keyup( searchInputKeyUpHandler );
            $( DOM.$termsUL  ).on( 'click', '.wpcme-terms-ul-li-select', termsListItemChangeHandler );
            DOM.$clearBtn.click( clearBtnClickHandler );
        }

        function searchInputKeyUpHandler() {
            loadTerms();
        }

        function termsListItemChangeHandler() {
            var selectedID = $( this ).val();

            if ( $( this ).is( ':checked' ) ) {
                addTermToSelectedTermContainer( selectedID );
            } else {
                removeTermFromSelectedTermContainer( selectedID );
            }
        }

        function clearBtnClickHandler() {
            DOM.$searchInput.val( '' ).keyup();
        }

        /**
         * Load the Terms/Categories on initial page load.
         *
         * @since 1.0.0
         */
        function loadTerms() {

            $.ajax({
                url: DOM.$jsAjaxObject.ajax_url,
                method: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    DOM.$ajaxSpinner.show();
                },
                data: {
                    'action'      : DOM.$jsAjaxObject.load_terms_action,
                    'nonce'       : DOM.$jsAjaxObject.nonce,
                    'screen'      : DOM.$jsAjaxObject.screen,
                    'search_term' : DOM.$searchInput.val(),
                    'post_id'     : DOM.$jsAjaxObject.post_id
                }
            })
                .success( function ( response ) {
                    DOM.$termsUL.empty();

                    if ( ! response.success ) {
                        $( '<li />' )
                            .html( ajax_object.messages.something_wrong )
                            .appendTo( DOM.$termsUL );

                        return;
                    }

                    var liHTML;

                    if ( response.data.terms.length === 0 ) {
                        $( '<li />' )
                            .html( ajax_object.messages.no_results )
                            .appendTo( DOM.$termsUL );
                        return;
                    }

                    $.each( response.data.terms, function( key, value ) {

                        liHTML = '<label class="selectit">';
                        liHTML += '<input type="checkbox" id="in-category-' + key + '" class="wpcme-terms-ul-li-select" value="' + key + '" />';
                        liHTML += ' ' + value;
                        liHTML += '</label>';

                        $( '<li />' )
                            .attr( 'class', 'wpcme-terms-ul-li' )
                            .attr( 'id', 'category-' + key )
                            .attr( 'data-term-id', key )
                            .html( liHTML )
                            .appendTo( DOM.$termsUL );
                    });

                    selectExistingPostTerms( response.data.post_terms )
                })
                .error( function () {
                    $( '<li />' )
                        .html( ajax_object.messages.something_wrong )
                        .appendTo( DOM.$termsUL );
                })
                .always( function () {
                    DOM.$ajaxSpinner.hide();
                });
        }

        function selectExistingPostTerms( existingTerms ) {

            var termOpter = $( '.wpcme-terms-ul-li-select' ),
                userOptedTermID;

            if ( isExistingTermsSet ) {
                $.each( DOM.$selectedTerms.children(), function() {
                    userOptedTermID = $( this ).val();
                    termOpter.filter( 'input[value="' + userOptedTermID + '"]' ).prop( 'checked', 'checked' );
                });

                return;
            }

            $.each( existingTerms, function( index, value ) {
                termOpter.filter( 'input[value="' + value + '"]' ).prop( 'checked', 'checked' );
                addTermToSelectedTermContainer( value );
            } );

            isExistingTermsSet = true;
        }

        function addTermToSelectedTermContainer( termID ) {
            $( '<input />' )
                .attr( 'name', 'post_category[]' )
                .attr( 'type', 'hidden' )
                .attr( 'id', 'in-category-' + termID )
                .val( termID )
                .appendTo( DOM.$selectedTerms );
        }

        function removeTermFromSelectedTermContainer( termID ) {
            DOM.$selectedTerms.find( 'input[name="post_category[]"][value="' + termID + '"]' ).remove();
        }

        return {
            init: init
        }
    }() );

    $( document ).ready( function () {
        WPCategoriesMetaboxExtended.init();
    });

}) ( jQuery );