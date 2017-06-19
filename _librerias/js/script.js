
jQuery(document).ready(function() {
    "use strict";

    // Sidemenu One Page Smooth Scrolling
    $(function() {
        $('a[href*=#]:not([href=#])').click(function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 1000);
                }
            }
        });
    });
    var lastId,
    topMenu = $(".sidemenu-inner ul"),
    topMenuHeight = topMenu.outerHeight() + 15,
    // All list items
    menuItems = topMenu.find("a"),
    scrollItems = menuItems.map(function() {
        var item = $($(this).attr("href"));
        if (item.length) {
            return item;
        }
    });
    $(window).scroll(function() {
        // Get container scroll position
        var fromTop = $(this).scrollTop() + topMenuHeight;

        // Get id of current scroll item
        var cur = scrollItems.map(function() {
            if ($(this).offset().top < fromTop)
                return this;
        });
        // Get the id of the current element
        cur = cur[cur.length - 1];
        var id = cur && cur.length ? cur[0].id : "";

        if (lastId !== id) {
            lastId = id;
            // Set/remove active class
            menuItems
                .parent().removeClass("selected")
                .end().filter("[href=#" + id + "]").parent().addClass("selected");
        }
    });

    // Sidemenu Toggle Button Animation
    var toggles = document.querySelectorAll(".c-hamburger");
    for (var i = toggles.length - 1; i >= 0; i--) {
      var toggle = toggles[i];
      toggleHandler(toggle);
    };
    function toggleHandler(toggle) {
      toggle.addEventListener( "click", function(e) {
        e.preventDefault();
        (this.classList.contains("is-active") === true) ? this.classList.remove("is-active") : this.classList.add("is-active");
      });
    }

    // Menu Opening and Body Blur
    $(".c-hamburger").on("click",function(){
        $(this).next(".sidemenu").toggleClass("slidein");
        $("body").toggleClass("menu-opened");
    });
    $("html").on("click",function(){
        $(".sidemenu").removeClass("slidein");
        $("body").removeClass("menu-opened");
        $(".c-hamburger").removeClass("is-active");
    });
    $(".c-hamburger, .sidemenu").on("click",function(e){
        e.stopPropagation();
    });
    $(".sidemenu li a").on("click",function(){
        setTimeout(function(){
            $(".sidemenu").removeClass("slidein");
            $("body").removeClass("menu-opened");
            $(".c-hamburger").removeClass("is-active");
        },1000);
    });


    // Categories Selectors
    $(".categories a").on("click",function(){
        $(this).addClass("selected");
        $(this).siblings().removeClass("selected");
    });

    // Materialize
    $('select').material_select();
    $('.materialboxed').materialbox();
    $('.parallax').parallax();
    $(".portfolio-img > a").on("click",function(){
        $(this).parent().find(".materialboxed").trigger("click");
        return false;
    });


}); // Document.Ready Ends Here

jQuery(window).load(function(){
    "use strict";

    // Map Height
    $(".map > div").each(function(){
        var map_height = $(this).parent().parent().siblings().find(".get-in-touch").innerHeight();
        $(this).css({
            "height":map_height
        });
    });

    // Column Title Height
    $(".column-title").each(function(){
        var column_title_height = $(this).parent().siblings().innerHeight();
        $(this).css({
            "height":column_title_height
        });
    });

    // Related Carousel
    $('.related-carousel').owlCarousel({
        autoplay:true,
        autoplayTimeout:2500,
        smartSpeed:2000,
        autoplayHoverPause:true,
        loop:true,
        dots:false,
        nav:true,
        margin:30,
        mouseDrag:true,
        items:3,
        responsive:{
            1200:{items:3},
            992:{items:3},
            600:{items:2},
            0:{items:1}
        }
    });

    // Testimonials Carousel
    var total_testimonail = $(".testimonials-carousel .owl-stage").find(".owl-item").length;
    $(".total").html(total_testimonail);
    $(".current").html('1');
    $(".testimonials-carousel").on('change.owl.carousel',function(){
        setTimeout(function(){
            var current_testimonail = $(".testimonials-carousel .owl-stage").find(".owl-item.active").index()+1;
            $(".current").html(current_testimonail);
        },300);
    });

}); // Window.Load Ends Here
