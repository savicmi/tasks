$(document).ready(function() {

    // Reveal CSS animation as you scroll down a page
    new WOW().init();

    $('.my-navbar .navbar-nav a').on('click', function (event) {
        event.preventDefault();

        // set active menu item
        $(this).parent().addClass('active');
        $(this).parent().siblings().removeClass('active');

        // scroll to the certain section
        var href = 'section' + $(this).attr('href');
        if ($(href).length > 0)
            $(href).animatescroll({padding: parseInt($('.my-navbar').outerHeight())});

    });

    // Changes active menu item on scroll
    $(window).scroll(function() {
        // current scroll position
        var scrollPos = $(document).scrollTop() + $('.my-navbar').outerHeight();

        if ($(window).width() >= 780) {
            if ($(document).scrollTop() > 2 * $('.my-navbar').outerHeight())
                $('.my-navbar').css({
                    '-webkit-box-shadow': '0 0 5px 0 rgba(0, 0, 0, 0.1)',
                    '-moz-box-shadow': '0 0 5px 0 rgba(0, 0, 0, 0.1)',
                    'box-shadow': '0 1px 5px 0 rgba(0, 0, 0, 0.1)'
                });
            else
                $('.my-navbar').css({
                    '-webkit-box-shadow': 'none',
                    '-moz-box-shadow': 'none',
                    'box-shadow': 'none'
                });
        }

        $('.my-navbar .navbar-nav a').each(function () {
            var curr_link = $(this);
            var ref_element = $('section' + curr_link.attr("href"));

            if (ref_element.position().top <= scrollPos && ref_element.position().top + ref_element.height() > scrollPos)
                curr_link.parent().addClass("active");
            else
                curr_link.parent().removeClass("active");
        });
    });

    var $nav_coll = $('.navbar-collapse');
    $nav_coll.removeClass('collapsing').addClass('fadeInLeft').addClass('animated');

    // Settings for flex slider within about section
    $('#about_slider').flexslider({
        selector            : ".slides > li",
        animation           : "fade",
        easing              : "swing",
        animationLoop       : true,
        smoothHeight        : false,
        startAt             : 0,
        slideshow           : true,
        slideshowSpeed      : 7000,
        animationSpeed      : 3500,
        initDelay           : 0,
        randomize           : false,

        // Usability features
        pauseOnAction       : false,
        pauseOnHover        : false,
        useCSS              : true,
        touch               : true,

        // Primary Controls
        controlNav: false,
        directionNav: false,

        // Secondary Navigation
        keyboard: false,
        pausePlay: false
    });

    // store the slider in a local variable
    var $window = $(window),
        flexslider = { vars:{} };

    // Carousel with fun images
    $window.load(function() {
        $('#fun_images').flexslider({
            animation: "slide",
            animationLoop: true,
            smoothHeight: false,
            slideshowSpeed: 4000,
            animationSpeed: 3000,
            pauseOnAction: true,
            pauseOnHover: false,
            controlNav: false,
            directionNav: true,
            pausePlay: false,
            itemWidth: 957,
            itemMargin: 0,
            minItems: 2,
            maxItems: 2,
            move: 1,
            controlsContainer: $(".custom-controls-container"),
            customDirectionNav: $(".custom-navigation a"),
            start: function(slider) { // fires when the slider loads the first slide
                var slide_count = slider.count - 1;

                $(slider)
                    .find('img.lazy:eq(0)')
                    .each(function() {
                        var src = $(this).attr('data-src');
                        $(this).attr('src', src).removeAttr('data-src');
                    });
            },
            before: function (slider) { // fires asynchronously with each slider animation
                var slides = slider.slides,
                    index = slider.animatingTo,
                    $slide = $(slides[index]),
                    $img = $slide.find('img[data-src]'),
                    current = index,
                    nxt_slide = current + 1,
                    prev_slide = current - 1;

                $slide
                    .parent()
                    .find('img.lazy:eq(' + current + '), img.lazy:eq(' + prev_slide + '), img.lazy:eq(' + nxt_slide + ')')
                    .each(function () {
                        var src = $(this).attr('data-src');
                        $(this).attr('src', src).removeAttr('data-src');
                    });
            }
        });
    });

    var about_height = $('section#about').height();
    var caption_height = ($('.flexslider').find('.flex-caption')).height();
    var difference_height = parseInt(caption_height)-parseInt(about_height);

    if (difference_height > 0) {
        $('#about').css('height', caption_height + 'px');
    }

    $window.resize(function() {
        $('#about').css('height', 'auto');
        var about_height = $('section#about').height();
        var caption_height = ($('.flexslider').find('.flex-caption')).height();
        var difference_height = parseInt(caption_height)-parseInt(about_height);

        if (difference_height > 0) {
            $('#about').css('height', caption_height + 'px');
        }

        flexslider.vars.minItems = 2;
        flexslider.vars.maxItems = 2;

        $('#fun_images ul.slides li').css('width', parseInt(($(window).width())/2)+'px');
        $('#fun_images ul.slides > li img').css({
            'display': 'block',
            'max-width': '100% !important',
            'width': '100% !important',
            'height': 'auto'
        });
    });

    var $about_text = $('.flexslider ul.slides li:first-child .flex-caption');
    var $clone = $about_text.clone();
    $('.flexslider ul.slides li').not('li:first-child').append($clone);

    // Hide all team info except the first (all team)
    $('.about-members > div').not('div#all').hide();
    $('.about-members > h3.name').text($('div#all h3.name').text());
    $('.about-members > p.description').html($('div#all p.description').html());
    $('.about-members > div .name, .about-members > div .description').hide();

    // Click on a link inside the team section
    $('.team-members li > a').click(function (event) {
        event.preventDefault();

        $(this).parent().addClass('active');
        $(this).parent().siblings().removeClass('active');

        var href = $(this).attr('href');
        $('.about-members > div').fadeOut('slow').hide();
        $(href).fadeIn('slow').show();
        $(href).find('.images a').hide().filter(":first-child").show();

        $('.about-members > h3.name').text($(href).find('div:first-child h3.name').text());
        $('.about-members > p.description').html($(href).find('div:first-child p.description').html());
    });

    // Change image and text on image hover
    var old_src, new_src;
    $('.images a').hover(function(event) {
        old_src = $(this).find('img').attr('src');
        new_src = $(this).next().find('img').attr('src');
        $(this).find('img').attr('src', new_src);

        $('.about-members > h3.name').text($(this).parent().parent().find('h3.name').text());
        $('.about-members > p.description').html($(this).parent().parent().find('p.description').html());
    }, function(event) {
        $(this).find('img').attr('src', old_src);
    });

    // Sending message
    $('#send').click(function (event) {
        event.preventDefault();
        var $formObject = $(this).closest('form');

        $formObject.find('.alert-danger').html('');

        var valid = validate($formObject.attr('id'));

        if (valid == 0) {

            $formObject.find('.alert-danger').html('').slideUp('slow');

            var fields = {
                fullname: $.trim($formObject.find('#fullname').val()),
                email: $.trim($formObject.find('#email').val()),
                message: $.trim($formObject.find('#message').val())
            };

            /* here goes communication to a PHP file through Ajax etc. */

        }
        else {
            $formObject.find('.alert-danger').slideDown('slow');
        }

        $(this).blur();

    });

    // Input data validation
    function validate(form_id) {
        var errors = 0;
        $("form#" + form_id + " :input").each(function () {
            var input = $(this);

            // Condition for fullname, allow between 2 and 50 letters including spaces
            if (input.attr('id') == 'fullname') {
                if (/^([a-z ]|[^\u0000-\u007F$]){2,50}$/i.test($.trim(input.val()))) {
                    // it's ok
                }
                else {
                    errors++;
                    $("form#" + form_id).find('.alert-danger').append('The name field is required.<br>');
                }
            }

            // Condition for email
            if (input.attr('id') == 'email') {
                if (/^([\w'-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i.test($.trim(input.val()))) {
                    // it's ok
                }
                else {
                    errors++;
                    $("form#" + form_id).find('.alert-danger').append('The email field is required.<br>');
                }
            }

            // Condition for message, allow between 5 and 500 characters including spaces
            if (input.attr('id') == 'message') {
                if (/^(.|[ \r\n]){5,500}$/im.test($.trim(input.val()))) {
                    // it's ok
                }
                else {
                    errors++;
                    $("form#" + form_id).find('.alert-danger').append('The message field is required.<br>');
                }
            }
        });

        return errors;
    }

});
