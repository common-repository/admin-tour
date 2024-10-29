jQuery( document ).ready( function( $ ) {
	// Dismiss ajax request.
	var watDismissAjax = function() {
		$.post( WAT.ajax_url, {
			action: 'wat_dismiss_pointer',
			nonce: WAT.security_nonce,
			screen: WAT.current_screen,
		},
		function( res ) {

		}, 'json' );
	};

	// Remove overlay.
	var watRemoveOverlay = function() {
		$( 'div#wpcontent' ).removeClass( 'wat-open' );
	};

	// Add overlay.
	var watAddOverlay = function() {
		$( 'div#wpcontent' ).addClass( 'wat-open' );
	};

	/**
	 * Init Pointers.
	 */
	var watInit = function() {
		setTimeout( function() {
			var pointers = WAT.pointers;
			if ( pointers.screen_info ) {
				delete pointers.screen_info;
			}
			pointers = Object.values( pointers );
			pointers = pointers.filter(
				function( p ) {
					return p !== false && $( p.tagget_element ).eq( 0 ).is( ':visible' )
				}
			);
			if ( pointers.length > 0 ) {
					// Get last array key.
					var lastPointerIndex = pointers.length - 1;
					// Register JS pointers.
					$.each( pointers, function( index, item ) {
						var initPointer = $( item.tagget_element ).eq( 0 ).pointer( {
							pointerClass: 'wat-pointer',
							pointerWidth: 320,
							content: '<h3 class="wat-pointer-title">' + item.title + '<span class="dashicons dashicons-no" title="' + WAT.i18n.close_button_title + '"></span></h3><p>' + item.content + '</p>',
							position: {
								edge: item.position.edge,
								align: item.position.align
							},
							buttons: function( event, t ) {
								currentIndex = index + 1;
								if ( lastPointerIndex === index ) {
									WAT.i18n.next_button = WAT.i18n.finish_button ? WAT.i18n.finish_button : WAT.i18n.next_button;
								}
								buttonHtml = '<span class="wat-status">' + currentIndex + '/' + pointers.length + '</span><a class=\"button button-primary wat-next\" href=\"#\">' + WAT.i18n.next_button + '</a>';
								if ( currentIndex > 1 ) {
									buttonHtml += '<a class=\"button button-primary wat-prev\" href=\"#\">' + WAT.i18n.prev_button + '</a>';
								}
								if ( item.url && item.url != '' ) {
									buttonHtml += ' <a class=\"button button-secondary wat-go-to\" href=\"' + item.url + '\">' + WAT.i18n.go_to_button + '</a>';
								}
								button = $( buttonHtml ),
								wrapper = $( '<div class=\"wat-pointer-buttons\" />' );
								button.bind( 'click.pointer', function( e ) {
									if ( $( this ).hasClass( 'wat-next' ) ) {
										e.preventDefault();
										if ( $( this ).parents( '.wat-pointer' ).hide().next( '.wat-pointer' ).length > 0 ) {
											$( this ).parents( '.wat-pointer' ).hide().next( '.wat-pointer' ).show();
										}
										t.element.pointer( 'close' );
										e.preventDefault();
									} else if ( $( this ).hasClass( 'wat-prev' ) ) {
										if ( $( this ).parents( '.wat-pointer' ).prev( '.wat-pointer' ).length > 0 ) {
											$( this ).parents( '.wat-pointer' ).hide().prev( '.wat-pointer' ).show();
										}
										e.preventDefault();
									}
								} );
								wrapper.append( button );
								return wrapper;
							},
							close: function() {
								if ( lastPointerIndex === index ) {
									watDismissAjax();
									watRemoveOverlay();
								}
								if ( pointers[ index + 1 ] && pointers[ index + 1 ].tagget_element ) {
									$( pointers[ index + 1 ].tagget_element ).pointer( 'open' );
								}
							},
							show: function( event, t ) {
								watAddOverlay();
							}
						});

						if ( 0 === index) {
							initPointer.pointer( 'open' );
						}
					} );
				}
			} );
		}

		// Dismiss all pointers.
		$( document ).on( 'click', '.wat-pointer-title span.dashicons-no', function() {
			watDismissAjax();
			watRemoveOverlay();
			$( this )
			.parents( '.wat-pointer' )
			.hide()
		} );

		// Remove query param
		const watURL = new URL( location );
		if ( watURL.searchParams.has( 'wat_start_tour' ) ) {
			watURL.searchParams.delete( 'wat_start_tour' );
			history.replaceState( null, null, watURL );
		}

		watInit();
} );