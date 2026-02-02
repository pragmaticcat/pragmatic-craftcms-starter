/* CSS */
import "@js/parts/css";

/**
 * jQuery
 */
import $ from 'jquery';
window.$ = window.jQuery = $;

/**
 * GSAP
 */
import gsap from 'gsap';
window.gsap = gsap;

/* JS */
import "@js/parts/lazyloading";


import "./scripts";


/**
 * Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
 */
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR");
  });
}
