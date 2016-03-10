var scrollSpeed = 120;
var current = 0;
var imageWidth = 2000;
var headerWidth = 1000;

var restartPosition = -(imageWidth - headerWidth);

function scrollBg(){
    current--;
    if (current == restartPosition){
        current = 0;
    }
    $('#cloud').css("background-position",current+"px 0");
}

var init = setInterval("scrollBg()", scrollSpeed);