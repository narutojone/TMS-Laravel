<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'TMS')</title>

    {{-- Vendor Styles --}}
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/2.6.0/metisMenu.min.css" integrity="sha256-rnNuZyhPoWYWTsQ0Yx2q/2vB2VGCUkAZ7rlD3NFAF44=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" integrity="sha256-j+P6EZJVrbXgwSR5Mx+eCS6FvP9Wq27MBRC/ogVriY0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css" integrity="sha256-2kJr1Z0C1y5z0jnhr/mCu46J3R6Uud+qCQHA39i1eYo=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.min.css" integrity="sha256-nFp4rgCvFsMQweFQwabbKfjrBwlaebbLkE29VFR0K40=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.2.2/quill.snow.min.css" integrity="sha256-Znjl9CBkDhlEEudOz+eVBsdXj7sAnEu2JrYhEvlmzMo=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.8.1/css/froala_editor.pkgd.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css" crossorigin="anonymous" />

    {{-- App Style --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jasny-bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>

    @yield('head')
    
    <!-- Start of synegait Zendesk Widget script -->
        <script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(e){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var e=this.createElement("script");n&&(this.domain=n),e.id="js-iframe-async",e.src="https://assets.zendesk.com/embeddable_framework/main.js",this.t=+new Date,this.zendeskHost="synegait.zendesk.com",this.zEQueue=a,this.body.appendChild(e)},o.write('<body onload="document._l();">'),o.close()}();
        /*]]>*/</script>
    <!-- End of synegait Zendesk Widget script -->


        <script type="text/JavaScript">


          zE(function() {
            zE.setLocale('no');
            var userName = $('#datapass').data("field-name");
            var emailVar = $('#datapass').data("field-email");
            zE.identify({name: userName, email: emailVar}); 
          });

        window.zESettings = {
          webWidget: {

            contactForm: {
              title: {
                'en-US': 'Oppdateringer',
                '*': 'Oppdateringer'
              },
              attachments: true
            },

            launcher: {
              label: {
                'en-US': 'Oppdateringer',
                '*': 'Oppdateringer'
              }
            }
          }
        };
        </script>

</head>
<body>
    <div id="datapass" data-field-name="{{ Auth::user()->name }}" data-field-email="{{ Auth::user()->email }}" ></div>
    <div id="wrapper">
        @include('layouts.navigation')

        <div id="page-wrapper" class="gray-bg">
            @include('layouts.topnavbar')

            @yield('heading')

            @include('layouts.flash-alerts')

            @yield('content')

            @include('layouts.footer')
        </div>
    </div>

    {{-- Vendor Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/2.6.0/metisMenu.min.js" integrity="sha256-wvsvg6CUaxY3v5FqgZSOKq3EGprnRr5FvO85WKmJiUQ=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js" integrity="sha256-qE/6vdSYzQu9lgosKxhFplETvWvqAAlmAuR+yPh/0SI=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js" integrity="sha256-EPrkNjGEmCWyazb3A/Epj+W7Qm2pB9vnfXw+X6LImPM=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" integrity="sha256-LOnFraxKlOhESwdU/dX+K0GArwymUDups0czPWLEg4E=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js" integrity="sha256-sLYUdmo3eloR4ytzZ+7OJsswEB3fuvUGehbzGBOoy+8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js" integrity="sha256-urCxMaTtyuE8UK5XeVYuQbm/MhnXflqZ/B9AOkyTguo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.2.2/quill.min.js" integrity="sha256-WZ/mZOnB8IfFF8ej+QncOYo8wmjUQaI3YcEAg7xM1T4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/jquery.mark.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/jasny-bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/dropzone.js') }}"></script>
    <script src="{{ asset('js/switchery.min.js') }}"></script>
    <script src="{{ asset('js/froala_editor.min.js') }}"></script>
    <script src="{{ asset('js/froala-plugins.min.js') }}"></script>
    <script src="{{ asset('js/tree-view.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.4.4/vue.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.7.1/clipboard.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    {{-- App Script --}}
    <script>
/*
*
*   INSPINIA - Responsive Admin Theme
*   version 2.6.2
*
*/

$(document).ready(function () {

    // Add body-small class if window less than 768px
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }
    $('.colorpicker-element').colorpicker();
    // Metis Menu
    $('#side-menu').metisMenu();

    // Collapse ibox function
    $('.collapse-link').on('click', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.find('div.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    // Close ibox function
    $('.close-link').on('click', function () {
        var content = $(this).closest('div.ibox');
        content.remove();
    });

    // Fullscreen ibox function
    $('.fullscreen-link').on('click', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        $('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function () {
            $(window).trigger('resize');
        }, 100);
    });

    // Minimalize menu
    $('.navbar-minimalize').on('click', function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();

    });

    // Full height of sidebar
    function fix_height() {
        var heightWithoutNavbar = $("body > #wrapper").height() - 61;
        $(".sidebard-panel").css("min-height", heightWithoutNavbar + "px");

        var navbarHeigh = $('nav.navbar-default').height();
        var wrapperHeigh = $('#page-wrapper').height();

        if (navbarHeigh > wrapperHeigh) {
            $('#page-wrapper').css("min-height", navbarHeigh + "px");
        }

        if (navbarHeigh < wrapperHeigh) {
            $('#page-wrapper').css("min-height", $(window).height() + "px");
        }

        if ($('body').hasClass('fixed-nav')) {
            if (navbarHeigh > wrapperHeigh) {
                $('#page-wrapper').css("min-height", navbarHeigh + "px");
            } else {
                $('#page-wrapper').css("min-height", $(window).height() - 60 + "px");
            }
        }

    }

    fix_height();

    // Fixed Sidebar
    $(window).bind("load", function () {
        if ($("body").hasClass('fixed-sidebar')) {
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });
        }
    });

    $(window).bind("load resize scroll", function () {
        if (!$("body").hasClass('body-small')) {
            fix_height();
        }
    });

    // Add slimscroll to element
    $('.full-height-scroll').slimscroll({
        height: '100%'
    });

    // Switchery
    window.switcheryElements = [];
    document.querySelectorAll('.js-switch').forEach(function (elem) {
        keyName = $(elem).attr('id');
        window.switcheryElements[keyName] = new Switchery(elem, {
            color: '#1AB394',
            size: ($(elem).data('size') == 'small') ? 'small': 'default'
        })
    });

    // Froala editor
    $( "textarea.wysiwyg" ).each(function( index ) {
        // Set image upload visibility options
        imageUploadVisibility = 'private';
        if($(this).data('visibility')) {
            if($(this).data('visibility').toLowerCase() === 'public') {
                imageUploadVisibility = 'public';
            }
        }
        // Initialize Froala editor
        $(this).froalaEditor({
            key: '{{ env('FROALA_LICENSE_KEY') }}',
            imageManagerLoadURL: '',
            imageUploadURL: '{{ route('editor.image.store') }}' + '?visibility='+ imageUploadVisibility,
            imageUploadParams: {_token: '{{csrf_token()}}'},
            imageUploadMethod: 'POST',
        });
    });

    // Chosen
    $('.chosen-select:not(.chosen-select-hidden)').chosen({
        allow_single_deselect: true,
    });

    $('.chosen-select-hidden').chosen({
        allow_single_deselect: true,
        width: "100%",
    });

    // Prevent multiple form submission at once (i.e. double cliking submit button)
    $("form").submit(function (e) {
        var submitButton = $(this).find('[type="submit"]:focus');
        submitButton.prop('disabled', true);

        // Keep submit button values in hidden inputs. Disabled html inputs are not sent to the server
        $('<input>').attr({
            type: 'hidden',
            name:  submitButton.attr('name'),
            value: submitButton.attr('value'),
        }).insertBefore(submitButton);

        setTimeout(function() {
            submitButton.prop('disabled', false);
        },1500);
    });

});


// Minimalize menu when screen is less than 768px
$(window).bind("resize", function () {
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }
});

function SmoothlyMenu() {
    if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        $('#side-menu').hide();
        // For smoothly turn on menu
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
            }, 200);
    } else if ($('body').hasClass('fixed-sidebar')) {
        $('#side-menu').hide();
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
            }, 100);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        $('#side-menu').removeAttr('style');
    }
}
    </script>

    @yield('script')
    @yield('javascript')
</body>
</html>
