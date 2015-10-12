jQuery.noConflict();

(function($){
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
    function close_accordion_section() {
        $('.accordion .accordion-section-title').removeClass('active');
        $('.accordion .accordion-section-content').slideUp(300).removeClass('open');
    } 
function addGAtoForm(){
        if (typeof(ga) !== 'undefined') {
            $("form").each(function() {
                var formName = $(this).closest('form').attr('name');
                if (!formName || 0 === formName.lengthformName){
                    formName = 'OnPage '+ document.getElementsByTagName("title")[0].innerHTML;
                }
                $('form :input').blur(function () {
                    if($(this).val().length > 0 && !($(this).hasClass('completed'))) {
                        ga('send', 'event', 'Form - ' + formName, 'completed', $(this).attr('name'), 'gaEvent');
                        $(this).addClass('completed')
                    }
                    else if(!($(this).hasClass('completed')) && !($(this).hasClass('skipped'))) {
                        ga('send', 'event', 'Form - ' + formName, 'skipped', $(this).attr('name'), 'gaEvent');
                        $(this).addClass('skipped');        
                    }
                });
            });
        }
    }    
    function fixupExtLinks(){
        $('a').each(function() {		
            if(window.location.hostname && window.location.hostname !== this.hostname  && !this.href.match(/^mailto\:/i)) {
                $(this).attr('target','_blank');
            }
        });
    }
    function addGAtoLinks(){    
        var filetypes = /\.(zip|exe|dmg|pdf|doc.*|xls.*|ppt.*|mp3|txt|rar|wma|mov|avi|wmv|flv|wav)$/i;
        var baseHref = '';
        if ($('base').attr('href') != undefined) baseHref = $('base').attr('href');
        if (typeof(ga) !== 'undefined')
        {
            $('a').on('click', function(event) {
              var el = $(this);
              var track = true;
              var href = (typeof(el.attr('href')) != 'undefined' ) ? el.attr('href') :"";
              var isThisDomain = true;

              if (href.match(/^https?\:/i)) {
                var domain = href.match(/http[s]?\:\/\/(.*?)[\/$]/)[1]
                var isThisDomain = (window.location.hostname && window.location.hostname == domain) ? true : false;
              } 
              if (!href.match(/^javascript:/i) && !href.match(/^#/i)) {
                var elEv = []; elEv.value=0, elEv.non_i=false;
                if (href.match(/\/share/i)){
                    if (el.find('.fa-facebook').length != 0){
                        elEv.action = "Share_FB";
                    }
                    else if (el.find('.fa-twitter').length != 0){
                        elEv.action = "Share_Twitter";
                    }                
                    elEv.category = "Share";
                    elEv.label = "Header";
                    elEv.loc = href;
                }
                else if (href.match(/facebook\.com/i)){
                    elEv.action = "Goto_FB";
                    elEv.category = "Share";
                    elEv.label = "Social_Menu"; 
                    elEv.loc = href;
                }  
                else if (href.match(/twitter\.com/i)){
                    elEv.action = "Goto_Twitter";
                    elEv.category = "Share";
                    elEv.label = "Social_Menu"; 
                    elEv.loc = href;
                }  
                else if (el.data('did')!= ''){
                    elEv.category = "Frontpage";
                    elEv.action = "View";
                    elEv.label = href;
                    elEv.loc = href;
                }
                else if (href.match(/^mailto\:/i)) {
                  elEv.category = "email";
                  elEv.action = "click";
                  elEv.label = href.replace(/^mailto\:/i, '');
                  elEv.loc = href;
                }
                else if (href.match(filetypes)) {
                  var extension = (/[.]/.exec(href)) ? /[^.]+$/.exec(href) : undefined;
                  elEv.category = "download";
                  elEv.action = "click-" + extension[0];
                  elEv.label = href.replace(/ /g,"-");
                  elEv.loc = baseHref + href;
                }
                else if (href.match(/^https?\:/i) && !isThisDomain) {
                  elEv.category = "external";
                  elEv.action = "click";
                  elEv.label = href.replace(/^https?\:\/\//i, '');
                  elEv.loc = href;
                }
                else if (href.match(/^tel\:/i)) {
                  elEv.category = "telephone";
                  elEv.action = "click";
                  elEv.label = href.replace(/^tel\:/i, '');
                  elEv.loc = href;
                }
                else track = false;
                if (track) {
                  ga('send','event', elEv.category, elEv.action, elEv.label, elEv.value);
                  if ( el.attr('target') == undefined || el.attr('target').toLowerCase() != '_blank') {
                    setTimeout(function() { location.href = elEv.loc; }, 400);
                    return false;
                  }
                }
              }
            });
        }   
    }    
    var cookieWarning= {
        init: function(){
            $("#dismissCookieWarning").click(function(e){
                e.preventDefault(),
                $(".cookie").slideToggle();
                $(".cookie").remove();
                var i=new Date;
                i.setDate(i.getDate()+30),
                document.cookie="cookiewarningdismissed=true; path=/; expires="+i.toUTCString()
            })
            if (cookieWarning.getCookie("cookiewarningdismissed")) $(".cookie").remove();
        },
        getCookie: function(c_name){
            var i,x,y,ARRcookies=document.cookie.split(";");
            for (i=0;i<ARRcookies.length;i++)
            {
                x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
                y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
                x=x.replace(/^\s+|\s+$/g,"");
                if (x==c_name)
                {
                    return unescape(y);
                }
            }
        }
    };  

    var newPosts= {
        init: function(){
            var nextLink = $('#load').attr('href');
            
            /**
             * Load new posts when the link is clicked.
             */
            $('#NewsContainer').on('click', '#load', function(e) {
                e.preventDefault();
                $("#loader").remove();
                $('<div/>', {id: 'loader',style: 'display:none'}).appendTo('body');
                //$('body').append('<div id="" style="display:none">');
                $("#loader").load(nextLink + ' #grid > *', function(){
                    $("#grid").append($("#loader").html());
                    $("#loader").html(' ');
                    equalheight('section.news ul li');
                    $("#loader").remove();
                    $('<div/>', {id: 'loader',style: 'display:none'}).appendTo('body');                    
                    $("#loader").load(nextLink + ' .load_button > *', function(){
                        $(".load_button").html($("#loader").html());
                        $("#loader").remove();
                    }); 
                }); 
            });
        }
    }    
    $(document).ready(function(){
        cookieWarning.init();
        newPosts.init();
        fixupExtLinks();
        addGAtoLinks();
        addGAtoForm();          
        $(".mobile_button, .site_nav ul li a.up").click(function(){
            $(".site_nav ul").slideToggle();
        });


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
        onScrollInit( $('.os-animation') );
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
})(jQuery);     