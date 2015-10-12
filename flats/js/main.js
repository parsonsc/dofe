$(document).ready(function(){

	$(".mobile_button, .site_nav ul li a.up").click(function(){
		$(".site_nav ul").slideToggle();
	});

	$(".cookie").click(function(){
		$(this).slideToggle();
	});
});
// SMOOTH SCROLL
$(function() {
  $('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
});

// WAYPOINTS
$(function(){
    function onScrollInit( items, trigger ) {
        items.each( function() {
        var osElement = $(this),
            osAnimationClass = osElement.attr('data-os-animation'),
            osAnimationDelay = osElement.attr('data-os-animation-delay');
          
            osElement.css({
                '-webkit-animation-delay':  osAnimationDelay,
                '-moz-animation-delay':     osAnimationDelay,
                'animation-delay':          osAnimationDelay
            });

            var osTrigger = ( trigger ) ? trigger : osElement;
            
            osTrigger.waypoint(function() {
                osElement.addClass('animated').addClass(osAnimationClass);
                },{
                    triggerOnce: true,
                    offset: '100%'
            });
        });
    }

    onScrollInit( $('.os-animation') );
    // onScrollInit( $('.staggered-animation'), $('.staggered-animation-container') );
});

// FAQ'S accordion
$(document).ready(function() {
  function close_accordion_section() {
    $('.accordion .accordion-section-title').removeClass('active');
    $('.accordion .accordion-section-content').slideUp(300).removeClass('open');
  }

  $('.accordion-section-title').click(function(e) {
    // Grab current anchor value
    var currentAttrValue = $(this).attr('href');

    if($(e.target).is('.active')) {
      close_accordion_section();
    }else {
      close_accordion_section();

      // Add active class to section title
      $(this).addClass('active');
      // Open up the hidden content panel
      $('.accordion ' + currentAttrValue).slideDown(300).addClass('open'); 
    }

    e.preventDefault();
  });
});



// LOAD MORE
$(function(){
    $("section.news ul li").slice(0, 6).fadeIn(); // select the first ten
    $("#load").click(function(e){ // click event for load more
        e.preventDefault();
        $("section.news ul li:hidden").slice(0, 3).fadeIn(); // select next 10 hidden divs and show them
        if($("section.news ul li:hidden").length == 0){ // check if any hidden divs still exist
            alert("No more News articles"); // alert if there are none left
        }
    });
});