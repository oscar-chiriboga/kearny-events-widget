/**
 * Kearny Events Widget — Carousel navigation + dot indicators
 */
( function () {
    'use strict';

    function initCarousel( wrapper ) {
        var track = wrapper.querySelector( '.kearny-events__track' );
        if ( ! track ) { return; }

        var prev  = wrapper.querySelector( '[data-kearny-arrow="prev"]' );
        var next  = wrapper.querySelector( '[data-kearny-arrow="next"]' );
        var cards = track.querySelectorAll( '.kearny-events__card' );
        var dots  = [];

        // Build dot indicators
        if ( cards.length > 1 ) {
            var dotsWrap = document.createElement( 'div' );
            dotsWrap.className = 'kearny-events__dots';
            dotsWrap.setAttribute( 'aria-hidden', 'true' );
            for ( var d = 0; d < cards.length; d++ ) {
                var dot = document.createElement( 'button' );
                dot.className = 'kearny-events__dot';
                dot.setAttribute( 'type', 'button' );
                dot.setAttribute( 'aria-label', 'Go to event ' + ( d + 1 ) );
                ( function( index ) {
                    dot.addEventListener( 'click', function () {
                        var cardWidth = cards[0].offsetWidth;
                        var gap = parseInt( getComputedStyle( track ).gap, 10 ) || 20;
                        track.scrollTo( { left: index * ( cardWidth + gap ), behavior: 'smooth' } );
                    } );
                } )( d );
                dots.push( dot );
                dotsWrap.appendChild( dot );
            }
            // Insert dots after the carousel wrapper
            wrapper.parentNode.insertBefore( dotsWrap, wrapper.nextSibling );
        }

        function getActiveIndex() {
            var cardWidth = cards[0] ? cards[0].offsetWidth : 1;
            var gap = parseInt( getComputedStyle( track ).gap, 10 ) || 20;
            return Math.round( track.scrollLeft / ( cardWidth + gap ) );
        }

        function updateDots() {
            var active = getActiveIndex();
            for ( var i = 0; i < dots.length; i++ ) {
                dots[i].classList.toggle( 'is-active', i === active );
            }
        }

        function step() {
            var firstCard = cards[0];
            if ( ! firstCard ) { return 280; }
            var gap = parseInt( getComputedStyle( track ).gap, 10 );
            if ( isNaN( gap ) ) { gap = 20; }
            return firstCard.offsetWidth + gap;
        }

        function updateArrows() {
            if ( ! prev && ! next ) { return; }
            var maxScroll = track.scrollWidth - track.clientWidth;
            var atStart = track.scrollLeft <= 2;
            var atEnd   = track.scrollLeft >= maxScroll - 2;
            if ( prev ) {
                prev.setAttribute( 'aria-disabled', atStart ? 'true' : 'false' );
                prev.disabled = atStart;
            }
            if ( next ) {
                next.setAttribute( 'aria-disabled', atEnd ? 'true' : 'false' );
                next.disabled = atEnd;
            }
        }

        if ( prev ) {
            prev.addEventListener( 'click', function () {
                track.scrollBy( { left: -step(), behavior: 'smooth' } );
            } );
        }
        if ( next ) {
            next.addEventListener( 'click', function () {
                track.scrollBy( { left: step(), behavior: 'smooth' } );
            } );
        }

        track.addEventListener( 'scroll', function () {
            window.requestAnimationFrame( function () {
                updateArrows();
                updateDots();
            } );
        }, { passive: true } );

        var resizeTimer;
        window.addEventListener( 'resize', function () {
            clearTimeout( resizeTimer );
            resizeTimer = setTimeout( function () {
                window.requestAnimationFrame( function () {
                    updateArrows();
                    updateDots();
                } );
            }, 100 );
        } );

        track.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'ArrowRight' ) {
                e.preventDefault();
                track.scrollBy( { left: step(), behavior: 'smooth' } );
            } else if ( e.key === 'ArrowLeft' ) {
                e.preventDefault();
                track.scrollBy( { left: -step(), behavior: 'smooth' } );
            }
        } );

        updateArrows();
        updateDots();
    }

    function init() {
        var wrappers = document.querySelectorAll( '.kearny-events__carousel-wrapper' );
        for ( var i = 0; i < wrappers.length; i++ ) {
            initCarousel( wrappers[ i ] );
        }
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
