var menuHeight = $("#header").height();
var startPos = 0;
$(window).scroll(function () {
   
    var currentPos = $(this).scrollTop();
    var header = document.getElementById('header');
    if (currentPos > startPos) {
        $("#header").css("top", "-" + menuHeight + "px");
    } else {
        $("#header").css("top", 0 + "px");
    }
    startPos = currentPos;
});