// Search selector
var sel = function (e) {
  return document.querySelector(e)
};
// Search selectors
var selAll = function (e) {
  return document.querySelectorAll(e)
};

var mobile = false,
  scrollWidth,
  $body = $('body'),
  $windowBrowser = $(window),
  windowHeight = $(window).height(),
  headerHeight = $('.inner-header-wrap').height(),
  link = '.wrap-header nav li a';
if (navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry|Windows Phone)/)) {
  mobile = true;
}

if (scrollWidth === undefined) {
  parent = $('<div style="width: 50px; height: 50px; overflow: auto"><div/></div>').appendTo('body');
  child = parent.children();
  scrollWidth = child.innerWidth() - child.height(99).innerWidth();
  parent.remove();
}

const submitFilterCatalog = () => {
  $('#filter').submit();
};

$(document).ready(function () {

  let timer = null;
  const timeForWait = 600;

  // Product Filter
  $('input[id^=categories-checkbox-], input[id^=sizes-checkbox-], input[id^=radio-]').on('change', () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      submitFilterCatalog();
    }, timeForWait);
  });

  if($("#slider-range").length > 0) {
    $('.noUi-handle').on('click', function() {
      $(this).width(50);
    });

    const rangeSlider = document.getElementById('slider-range');
    const moneyFormat = wNumb({
      decimals: 0,
      thousand: ',',
      prefix: ''
    });

    const min = $("#slider-range").data('min');
    const max = $("#slider-range").data('max');

    const startMin = $("#slider-range").data('start-min');
    const startMax = $("#slider-range").data('start-max');

    noUiSlider.create(rangeSlider, {
      start: [startMin, startMax],
      step: 1,
      range: {
        'min': [min],
        'max': [max]
      },
      format: moneyFormat,
      connect: true
    });

    // Set visual min and max values and also update value hidden form inputs
    rangeSlider.noUiSlider.on('update', function(values, handle) {
      document.getElementById('slider-range-value1').innerHTML = values[0];
      document.getElementById('slider-range-value2').innerHTML = values[1];
      document.getElementById('min-price').value = moneyFormat.from(values[0]);
      document.getElementById('max-price').value = moneyFormat.from(values[1]);
    });

    rangeSlider.noUiSlider.on('end', () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        submitFilterCatalog();
      }, timeForWait);
    });
  }
  // End Product Filter

  // Vacancies modal window
  $('#vacancy-form').on('show.bs.modal', function (e) {

    const vacancy = $(e.relatedTarget).data('vacancy');
    const phone = $(e.relatedTarget).data('phone');
    const {title, salary, text, contacts} = vacancy;

    $(this).find('.modal-title').text(title);
    $(this).find('.sum').text(salary);
    $(this).find('.description').html(text);
    $(this).find('.tel').text(contacts || phone);
  });

  $('#vacancy-form').on('hidden.bs.modal', function (e) {
    $(this).find('.modal-title').text('');
    $(this).find('.sum').text('');
    $(this).find('.description').text('');
    $(this).find('.tel').text('');
  });

  // Orders modal window
  $('#order-form').on('shown.bs.modal', function (e) {
    const id = $(e.relatedTarget).data('id');
    $(this).find("input[name='product']").val(id);
  });

  if (mobile == true) {
    $('body').addClass('mobile');
  } else {
    $('body').addClass('desktop');
  }

  /* primary-slider */
  if ($(".primary-slider").length > 0) {
    setTimeout(function () {
      $(".primary-slider").owlCarousel({
        loop: true,
        items: 1,
        // dots:false,
        dots: true,
        dotData: true,
        nav: true,
        navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        autoHeight: true
      });
    }, 500);
  }

  /* door-slider */
  if ($(".door-slider").length > 0) {
    $(".door-slider").owlCarousel({
      loop: true,
      items: 1,
      dots: false,
      nav: true,
      margin: 5,
      navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
      animateOut: 'fadeOut',
      animateIn: 'fadeIn',
      touchDrag: false,
      mouseDrag: false,
      pullDrag: false
    });
  }

  $(".door-slider").swipe({
    swipeLeft: function (event, direction, distance, duration, fingerCount) {
      $('.owl-next').trigger('next', 1);
    },
    swipeRight: function (event, direction, distance, duration, fingerCount) {
      $('.owl-prev').trigger('prev', 1);
    }
  });

  /**/
  var itemsNum = 0;

  var owl = $('.news-slider');

  owl.on('initialized.owl.carousel', function (event) {
    var itemCount = event.item.count;
    var size = event.page.size;
    var dragLength = 100 / (itemCount / size);

    $("#range").ionRangeSlider({
      type: "single",
      min: 1,
      max: itemCount - (size - 1),
      keyboard: true,
      step: 1,
      onChange: function (data) {
        let owlTo = (data.from) - 1;
        console.log("Позиция ползунка: " + owlTo);
        owl.trigger('to.owl.carousel', [owlTo, 500, true]);
      }
    });

    $('.irs-slider.single').css('width', dragLength + "%")

  });

  // Слайдер
  owl.owlCarousel({
    loop: false,
    margin: 40,
    nav: false,
    dots: false,
    slideBy: 1,
    responsiveClass: false,
    responsive: {
      0: {
        items: 1
      },
      768: {
        items: 2
      },
      992: {
        items: 3
      }
    }

  });

  owl.on('dragged.owl.carousel', function (event) {
    var itemCount = event.item.count;
    var size = event.page.size;
    var curItem = event.item.index - 1;
    var dragLength = 100 / (itemCount / size);
    console.log(curItem);
    $("#range").data("ionRangeSlider").update({from: curItem});
    $('.irs-slider.single').css('width', dragLength + "%");
  });

  owl.on('resized.owl.carousel', function (event) {
    var itemCount = event.item.count;
    var size = event.page.size;
    var curItem = event.item.index - 1;
    var dragLength = 100 / (itemCount / size);
    $("#range").data("ionRangeSlider").update({
      max: itemCount - (size - 1),
      from: curItem
    });
    $('.irs-slider.single').css('width', dragLength + "%");
  });

  /**/
  $(".dot-slider").owlCarousel({
    items: 1,
    animateOut: 'fadeOut',
    animateIn: 'fadeIn',
    dots: true,
    dotsData: true
  });

  /**/
  $('.my-button').click(function () {
    $(this).toggleClass('open');
  });

  /**/
  $("section").on("click", function () {
    if ($('.my-button').hasClass('open')) {
      $(".navbar-header .my-button").trigger("click");
    }
  });

  /*select*/
  if ($(".selectpicker").length > 0) {
    $('.selectpicker').selectpicker();
  }

  /**/
  heightContentBox();

  /*left-menu
  -------------------------------------------------------------*/
  $(".filter-box .title-filter").click(function () {
    var $parent = $(this).parent();
    if ($parent.hasClass('open')) {
      $parent.removeClass('open');
    } else {
      $parent.addClass('open');
    }
    $parent.find('.cont-filter').slideToggle("slow", function () {
    });

    return false;
  });

  /**/
  getSrc();

  // $.validate();
  /**/
  if ($("input[data-validation='required']").length > 0) {
    $.validate({
      decimalSeparator: '-'
    });
  }

  /*input phone*/
  if ($(".phone").length > 0) {
    $(".phone").mask("38-999-999-99-99");
  }

  /*Map*/
  // heightMap();

  // if($(".map-canvas").length>0) {
  //   setTimeout(function(){
  //     initializeMap();
  //   }, 1500 );
  // }


  /* to top */
  // $(window).scroll(function () {
  //   if ($(this).scrollTop() > 200) {
  //       $('.scrollup').fadeIn();
  //   } else {
  //       $('.scrollup').fadeOut();
  //   }
  // });
  // $('.scrollup').click(function () {
  //     $("html, body").animate({
  //         scrollTop: 0
  //     }, 600);
  //     return false;
  // });


});

$(window).resize(function () {
  heightContentBox();
});


$(window).load(function () {

  /* appear-block
  ----------------------------------------------------------------*/
  if($(".appear-block").length>0) {
    $('.appear-block').each(function() {
      var $this = $(this);
      $this.addClass('appear-animation');

      if(!$body.hasClass('no-csstransitions') && ($body.width() + scrollWidth) > 767) {
        $this.appear(function() {
        var delay = ($this.attr('data-appear-animation-delay') ? $this.attr('data-appear-animation-delay') : 1);

          if(delay > 1) $this.css('animation-delay', delay + 'ms');
          $this.addClass($this.attr('data-appear-animation'));

          //start Team animate
          animateStart();

          setTimeout(function() {
            $this.addClass('appear-animation-visible');
          }, delay);
        }, {accX: 0, accY: -150});
      } else {
        $this.addClass('appear-animation-visible');
      }
    });
  }

});

/*------------------------*/
/*------------------------*/

/*------------------------*/

function heightContentBox() {
  setTimeout(function () {
    var $this = $('.delivery-box');

    //reset height
    $this.find('.inner-wrap').css('height', '');

    //add height
    var height = 0;
    $this.each(function () {
      if ($(this).height() > height) height = $(this).innerHeight();
    });
    $this.find('.inner-wrap').css('height', height);

  }, 800);
}


//Animate Start
function animateStart() {
  $('#carousel [data-animation]').each(function () {
    var $this = $(this),
      animation = 'fadeIn',
      outAnimation = 'fadeOut',
      delay = 1;

    if ($this.data('animation'))
      animation = $this.data('animation');

    if ($this.data('animationDelay'))
      delay = $this.data('animationDelay');

    if ($this.data('outAnimation'))
      outAnimation = $this.data('outAnimation');

    $this.removeClass(outAnimation);

    if ($this.closest('.col-xs-12').hasClass('active'))
      $this.css('animation-delay', delay + 'ms').addClass('animated').addClass(animation);
  });
}

//Animate Finish
function animateFinish() {
  var duration = 1;

  $('#carousel .col-xs-12.active [data-out-animation]').each(function () {
    var $this = $(this),
      animation = 'fadeIn',
      outAnimation = 'fadeOut',
      delay = 1,
      outDelay = 1;

    if ($this.data('animation'))
      animation = $this.data('animation');

    if ($this.data('outAnimation'))
      outAnimation = $this.data('outAnimation');

    if ($this.data('animationDelay'))
      delay = $this.data('animationDelay');

    if ($this.data('outAnimationDelay'))
      outDelay = $this.data('outAnimationDelay');

    $this.css('animation-delay', delay + 'ms');

    if (outDelay >= duration)
      duration = outDelay;

    $this.removeClass(animation).addClass(outAnimation);

    if ($this.data('outAnimationDelay'))
      $this.css('animation-delay', outDelay + 'ms');
    else
      $this.css('animation-delay', '1ms');
  });
}


/* Google Map
-----------------------------------------------------------------------*/

function initializeMap() {
  var styles = [
    {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
    {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
    {elementType: 'labels.text.fill', stylers: [{color: '#746855'}]},
    {
      featureType: 'administrative.locality',
      elementType: 'labels.text.fill',
      stylers: [{color: '#555555'}]
    },
    {
      featureType: 'poi',
      elementType: 'labels.text.fill',
      stylers: [{color: '#555555'}]
    },
    {
      featureType: 'poi.park',
      elementType: 'geometry',
      stylers: [{color: '#263c3f'}]
    },
    {
      featureType: 'poi.park',
      elementType: 'labels.text.fill',
      stylers: [{color: '#6b9a76'}]
    },
    {
      featureType: 'road',
      elementType: 'geometry',
      stylers: [{color: '#38414e'}]
    },
    {
      featureType: 'road',
      elementType: 'geometry.stroke',
      stylers: [{color: '#212a37'}]
    },
    {
      featureType: 'road',
      elementType: 'labels.text.fill',
      stylers: [{color: '#9ca5b3'}]
    },
    {
      featureType: 'road.highway',
      elementType: 'geometry',
      stylers: [{color: '#746855'}]
    },
    {
      featureType: 'road.highway',
      elementType: 'geometry.stroke',
      stylers: [{color: '#1f2835'}]
    },
    {
      featureType: 'road.highway',
      elementType: 'labels.text.fill',
      stylers: [{color: '#f3d19c'}]
    },
    {
      featureType: 'transit',
      elementType: 'geometry',
      stylers: [{color: '#2f3948'}]
    },
    {
      featureType: 'transit.station',
      elementType: 'labels.text.fill',
      stylers: [{color: '#555555'}]
    },
    {
      featureType: 'water',
      elementType: 'geometry',
      stylers: [{color: '#17263c'}]
    },
    {
      featureType: 'water',
      elementType: 'labels.text.fill',
      stylers: [{color: '#515c6d'}]
    },
    {
      featureType: 'water',
      elementType: 'labels.text.stroke',
      stylers: [{color: '#17263c'}]
    }
  ]
  var dataMapLatitude = $('.map-canvas').data('map-latitude');
  var dataMapLongitude = $('.map-canvas').data('map-longitude');
  var myCenter = new google.maps.LatLng(dataMapLatitude, dataMapLongitude);

  var dataCursorLatitude = $('.map-canvas').data('cursor-latitude');
  var dataCursorLongitude = $('.map-canvas').data('cursor-longitude');
  if ($('body').width() > 767) {
    var myLatlng = new google.maps.LatLng(dataCursorLatitude, dataCursorLongitude);
  }
  if ($('body').width() < 768) {
    var myLatlng = new google.maps.LatLng(dataMapLatitude, dataMapLongitude);
  }
  var image = '/images/icons/marker.png';
  var marker = new google.maps.Marker({
    position: myLatlng,
    title: 'Warszawa',
    icon: image,
  });

  var mapProp = {
    center: myCenter,
    zoom: 16,
    styles: styles,
    // draggable: false,
    // scrollwheel: false,
    disableDefaultUI: true,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  var map = new google.maps.Map(document.getElementById("map-canvas"), mapProp);
  marker.setMap(map);

  google.maps.event.addListener(marker, 'click', function () {

    infowindow.setContent(contentString);
    infowindow.open(map, marker);

  });
}


/* Height Map
------------------------------------------------------------------------------*/
function heightMap() {
  $("#map-canvas").css("height", "");
  setTimeout(function () {
    if ($body.width() + scrollWidth > 767) {
      var boxHeight = $('.contacts-box').innerHeight();
      $("#map-canvas").css("height", boxHeight);
    } else {
      $("#map-canvas").css("min-height", 300);
    }
    ;
  }, 400);
}

/**/
function getSrc() {
  setTimeout(function () {

    $('.dot-slider .owl-dot').each(function () {
      var $this = $(this);

      var imageUrl = $this.find('img').attr('src');
      $this.find('div').css('background-image', 'url(' + imageUrl + ')');

    });

  }, 300);
}
