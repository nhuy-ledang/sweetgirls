$(document).ready(function() {
  // Background parallax
  $(".bg_parallax").each(function() {
    gsap.to(this, {
      backgroundPosition: (i, el) => {
        const speed = parseFloat(el.getAttribute("data-speed")?el.getAttribute("data-speed"):1);
        const yPos = -((window.pageYOffset - el.offsetTop) * speed);
        return `50% calc(100% - ${-yPos}px)`;
      },
      ease: "none",
      scrollTrigger: {
        trigger: this,
        start: "top bottom",
        end: "bottom top",
        invalidateOnRefresh: true,
        scrub: 0,
      }
    });
  });


})

$(window).load(function() {
  // Remove preloader
  if ($('#preloader').length) {
    $('#preloader').fadeOut('slow', function() {
      $(this).remove();
    });
  }


});
