window.graphics = {

    clearCanvas: function (canvasid){
        var canvas = document.getElementById(canvasid);
        var ctx = canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
    },
    
    clearContext: function(context, canvas){
        context.clearRect(0, 0, canvas.width, canvas.height);
    },

	clearSmallCanvas: function(context){
		context.clearRect(0, 0, 100, 100);
	},
	
	
    showCanvas: function (){
        $(canvasid).show(); //.animate({opacity: 0.8}, "fast")
    },

    hideCanvas: function(canvasid){
        $("canvasid").hide();//.css("opacity", "0");
        clearCanvas();
    }, 

    getCanvas: function(canvasid){
        //console.log(canvasid);
        var canvas = document.getElementById(canvasid);
        if (!canvas){
			console.log("Canvas is null");
			console.trace();
			return null;
		}
			
        var context = canvas.getContext("2d");
        return context;
    },
	
	drawCone: function(canvas, start, p1, p2, arcs, w){
		
			canvas.lineWidth = w;
			canvas.beginPath();
			canvas.moveTo(start.x,start.y);
			canvas.lineTo(p1.x,p1.y);
			canvas.arc(start.x, start.y, mathlib.getDistance(start, p1), mathlib.degreeToRadian(arcs.start), mathlib.degreeToRadian(arcs.end), false);
			//canvas.lineTo(start.x,start.y);
			canvas.closePath();
			canvas.stroke();
			canvas.fill();
			
			
	
	},
    
    drawBox: function (canvas, p, w, h, lw){
        canvas.lineWidth = lw;
        canvas.beginPath();
        canvas.moveTo(p.x-(w/2), p.y+(h/2));
        canvas.lineTo(p.x+(w/2),p.y+(h/2));
        canvas.lineTo(p.x+(w/2),p.y-(h/2));
        canvas.lineTo(p.x-(w/2),p.y-(h/2));
        canvas.closePath();
        canvas.stroke();
        canvas.fill();
    },

    drawCircle: function(canvas, x, y, r, w){
		if (r<1)
			r =1;
        canvas.lineWidth = w;
        canvas.beginPath();
        canvas.arc(x,y,r,0,Math.PI*2,true);
        canvas.closePath();
        canvas.stroke();
    },
    
    drawCircleAndFill: function(canvas, x, y, r, w){
        canvas.lineWidth = w;
        canvas.beginPath();
        canvas.arc(x,y,r,0,Math.PI*2,true);
        canvas.closePath();
        canvas.stroke();
        canvas.fill();
    },
    
	drawCircleNoStroke: function(canvas, x, y, r, w){
        canvas.lineWidth = w;
        canvas.beginPath();
        canvas.arc(x,y,r,0,Math.PI*2,true);
        canvas.closePath();
        canvas.fill();
    },
	
    drawLine: function(canvas, x1, y1, x2, y2, w){
        canvas.lineWidth = w;
        canvas.beginPath();
        canvas.moveTo(x1,y1);
        canvas.lineTo(x2,y2);
        canvas.stroke();
        
    },
    
    drawArrow: function(canvas, x, y, a, s, w){
        
        var p1, p2, p3, p4, p5, p6, p7;
        
        p1 = mathlib.getPointInDirection(s*0.5, a , x, y);
        p2 = mathlib.getPointInDirection(s*0.5, mathlib.addToDirection(a, -140), p1.x, p1.y);
        p3 = mathlib.getPointInDirection(s*0.15, mathlib.addToDirection(a, 90), p2.x, p2.y);
        p4 = mathlib.getPointInDirection(s*0.5, mathlib.addToDirection(a, 180), p3.x, p3.y);
        
        p7 = mathlib.getPointInDirection(s*0.5, mathlib.addToDirection(a, 140), p1.x, p1.y);
        p6 = mathlib.getPointInDirection(s*0.15, mathlib.addToDirection(a, -90), p7.x, p7.y);
        p5 = mathlib.getPointInDirection(s*0.5, mathlib.addToDirection(a, 180), p6.x, p6.y);
        
        canvas.lineWidth = w;
        canvas.beginPath();
        canvas.moveTo(p1.x, p1.y);
        canvas.lineTo(p2.x, p2.y);
        canvas.lineTo(p3.x, p3.y);
        canvas.lineTo(p4.x, p4.y);
        canvas.lineTo(p5.x, p5.y);
        canvas.lineTo(p6.x, p6.y);
        canvas.lineTo(p7.x, p7.y);
        canvas.closePath();
        canvas.fill();
        canvas.stroke();
        
    },

    drawX: function (canvas, x, y, l, w){
        x = parseInt(x);
        y = parseInt(y);
        l = parseInt(l);
        
        canvas.lineWidth = w;
        canvas.beginPath();
        canvas.moveTo(x-l,y-l);
        canvas.lineTo(x+l,y+l);
        canvas.stroke();
        
        
        canvas.beginPath();
        canvas.moveTo(x-l,y+l);
        canvas.lineTo(x+l,y-l);
        canvas.stroke();
        

    },
            
    drawHexagon: function(canvas, x, y, l, leftside, topleft, topright){
        var a = l*0.5
        var b = l*0.8660254 //0.86602540378443864676372317075294
        
        var p1, p2, p3, p4, p5, p6
        
        p1 = {x:x, y:y+a+l};
        p2 = {x:x, y:y+a};
        p3 = {x:x+b, y:y};
        p4 = {x:x+(2*b), y:y+a};
        p5 = {x:x+(2*b), y:y+a+l};
        p6 = {x:x+b, y:y+(2*l)};
        
        
        
        canvas.beginPath();
        
        if (leftside){
            canvas.moveTo(p1.x, p1.y);
            canvas.lineTo(p2.x, p2.y);
        }else{
            canvas.moveTo(p2.x, p2.y);
        }
        
        if (topleft){
            canvas.lineTo(p3.x, p3.y);
        }else{
            canvas.moveTo(p3.x, p3.y);
        }
        
        if (topright){
            canvas.lineTo(p4.x, p4.y);
        }else{
            canvas.moveTo(p4.x, p4.y);
        }
        
        canvas.lineTo(p5.x, p5.y);
        canvas.lineTo(p6.x, p6.y);
        canvas.lineTo(p1.x, p1.y)

        canvas.stroke();
    },
    
    drawAndRotate: function(canvas, w, h, iw, ih, angle, img, rolled){
		
        var x = Math.round(w/2);
        var y = Math.round(h/2);
        var width = iw/2;
        var height = ih/2;
        
        if (rolled)
            angle = 360 - angle;
        
        angle = angle * Math.PI / 180;              
        canvas.save();
        canvas.translate(x, y);
        if (rolled)
            canvas.scale(1, -1);
        canvas.rotate(angle);
        canvas.drawImage(img, -width / 2, -height / 2, width, height);
        canvas.rotate(-angle);
        canvas.translate(-x, -y);               
        canvas.restore();
    
    }

}
