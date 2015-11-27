function signum(x) {
    return (x < 0) ? -1 : 1;
}
function absolute(x) {
    return (x < 0) ? -x : x;
}

function drawPath(svg, path, startX, startY, endX, endY) {
    path.hide();
    // get the path's stroke width (if one wanted to be  really precize, one could use half the stroke size)
    var stroke =  parseFloat(path.attr("stroke-width"));
    // check if the svg is big enough to draw the path, if not, set heigh/width
    if (svg.attr("height") <  endY)                 svg.attr("height", endY);
    if (svg.attr("width" ) < (startX + stroke) )    svg.attr("width", (startX + stroke));
    if (svg.attr("width" ) < (endX   + stroke) )    svg.attr("width", (endX   + stroke));
    
   
     var deltaX = (endX - startY)  * .1;
    var deltaY = (endY - startY)  * .1;

    var delta  =  deltaY < absolute(deltaX) ? deltaY : absolute(deltaX);
    
    var centerX = (startX + endX)/2;
    var centerY = (startY + endY)/2;
    
    
    var segment1Center = {x:(startX+centerX)/2,y:(centerY+startY)/2};
    var segmentPente = (1/(centerY-startY)/(centerX-startX))*-1;

    var diffX = startX - centerX;
    var coef = 100*(diffX/300);
    
    var decalY = segment1Center.y + (coef * segmentPente);
    var decalX = segment1Center.x +coef;
   
    var controlPoint = {x:decalX,y:decalY};
   
    // if start element is closer to the left edge,
    // draw the first arc counter-clockwise, and the second one clock-wise
    var arc1 = 0; var arc2 = 1;
    if (startX > endX) {
        arc1 = 1;
        arc2 = 0;
    }
    // draw tha pipe-like path
    // 1. move a bit down, 2. arch,  3. move a bit to the right, 4.arch, 5. move down to the end 
    path.attr("d",  "M"  + startX + " " + startY +
                    " Q" +controlPoint.x+' '+controlPoint.y+' '+centerX+' '+centerY +
                    " T" + endX+' '+endY 
                    
            
            );
//    path.attr("d",  "M"  + startX + " " + startY +
//                    " V" + (startY + delta) +
//                    " A" + delta + " " +  delta + " 0 0 " + arc1 + " " + (startX + delta*signum(deltaX)) + " " + (startY + 2*delta) +
//                    " H" + (endX - delta*signum(deltaX)) + 
//                    " A" + delta + " " +  delta + " 0 0 " + arc2 + " " + endX + " " + (startY + 3*delta) +
//                    " V" + endY );

    path.show();
}

function connectElements(svg, path, startElem, endElem,positions) {
    var svgContainer= $("#svgContainer");

    if (endElem.length<1)
    {
        return;
    }
    // if first element is lower than the second, swap!
    if(startElem.offset().top > endElem.offset().top){
        var temp = startElem;
        startElem = endElem;
        endElem = temp;
    }

    // get (top, left) corner coordinates of the svg container   
    var svgTop  = svgContainer.offset().top;
    var svgLeft = svgContainer.offset().left;

    // get (top, left) coordinates for the two elements
    var startCoord = startElem.offset();
    var endCoord   = endElem.offset();
    
    if (positions != undefined && positions.y == 'middle')
    {
        startCoord.top -= startElem.height()/2;
    }

    // calculate path's start (x,y)  coords
    // we want the x coordinate to visually result in the element's mid point
    var startX = startCoord.left + 0.5*startElem.outerWidth() - svgLeft;    // x = left offset + 0.5*width - svg's left offset
    var startY = startCoord.top  + startElem.outerHeight() - svgTop ;        // y = top offset + height - svg's top offset

        // calculate path's end (x,y) coords
    var endX = endCoord.left + 0.1*endElem.outerWidth() - svgLeft;
    var endY = endCoord.top  - svgTop - 10;

    // call function for drawing the path
    drawPath(svg, path, startX, startY, endX, endY);

}
