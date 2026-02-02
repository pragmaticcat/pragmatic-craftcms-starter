import $ from 'jquery';
import AOS from 'aos';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ScrollSmoother } from 'gsap/ScrollSmoother';
import { SplitText } from 'gsap/SplitText';
import { Fancybox } from '@fancyapps/ui';
import { Carousel } from '@fancyapps/ui/dist/carousel/';
import { Autoplay } from '@fancyapps/ui/dist/carousel/carousel.autoplay.js';

$(function(){
  AOS.init();

  gsap.registerPlugin(ScrollTrigger, ScrollSmoother);

  window.smoother = ScrollSmoother.create({
    wrapper: '#smooth-wrapper',
    content: '#smooth-content',
    smooth: 1.1, // time for the content to catch up to the scroll position
    smoothTouch: 0.1, // Better not to use it or use a small value
    effects: true,
  });

  // Initialize Fancybox
  Fancybox.bind('[data-fancybox]', {
    // Your custom options
  });

  $(window).on("scroll", function() {
    if($(window).scrollTop() > 100){
      $('header').addClass('opaque');
    }else{
      $('header').removeClass('opaque');
    }
  } );

  let carousels = document.querySelectorAll(".f-carousel");
  for (let carousel of carousels) {
    let carouselPlugins = carousel.classList.contains('f-carousel-not-autoplay') ? { } : { Autoplay };
    const myCarousel = new Carousel(carousel, {
      slidesPerPage: carousel.dataset?.slidesperpage ?? 1,
      infinite: !carousel.classList.contains('f-carousel-not-infinite'),
      fill: true,
      adaptiveHeight: true,
      Dots: {
        maxCount: 2
      },
      Autoplay: {
        timeout: 2000,
        showProgress: false
      },
      Navigation: {
        nextTpl: "<svg id=\"text_gràfica\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 53.57 53.57\"><circle class=\"circle\" cx=\"26.78\" cy=\"26.78\" r=\"26.78\"/><line class=\"line\" x1=\"12.21\" y1=\"27.38\" x2=\"40.27\" y2=\"26.23\"/><polygon class=\"polygon\" points=\"37.13 30.45 36.42 29.75 39.89 26.25 36.14 23.04 36.8 22.28 41.36 26.19 37.13 30.45\"/></svg>",
        prevTpl: "<svg id=\"text_gràfica\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 53.57 53.57\"><circle class=\"circle\" cx=\"26.78\" cy=\"26.78\" r=\"26.78\"/><line class=\"line\" x1=\"41.86\" y1=\"26.69\" x2=\"13.79\" y2=\"27.83\"/><polygon class=\"polygon\" points=\"16.94 23.61 17.65 24.32 14.18 27.82 17.92 31.03 17.27 31.78 12.71 27.88 16.94 23.61\"/></svg>",
      },
    }, carouselPlugins);
  }

  for (let splitElement of $("[data-split]")) {
    var split = new SplitText(splitElement, {type: "chars,words,lines"});
    gsap.from(split.words, {
      duration: $(splitElement).data("split-duration") ?? .25,
      delay: $(splitElement).data("split-delay") ?? 0,
      y: $(splitElement).data("split-y") ?? 80,
      autoAlpha: 0,
      stagger: 0.05
    });
  }
});
