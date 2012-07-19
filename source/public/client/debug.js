function debugMarker(hx, hy){
		
		var canvas = window.graphics.getCanvas("ships");
	
		canvas.fillStyle    = hexgrid.hexlinecolor;;
		canvas.font         = 'italic 12px sans-serif';
		canvas.textBaseline = 'top';
		
		var pos = hexgrid.hexCoToPixel(hx, hy);
		
		$("#ships").css("top", pos.y -100 + "px").css("left", pos.x -100 + "px");
		
		
     	var img = new Image();
		img.src = 'ships/primus.png'; 
		
		
		
		$(img).on("load", function(){
		
			var x = 100;
			var y = 100;
			var width = 200*gamedata.zoom*0.5;
			var height = 200*gamedata.zoom*0.5;
			
			i = 1;
			
			
			rotate();

			//canvas.translate(100, 100);
			//canvas.rotate(10 * Math.PI / 180);
			//canvas.drawImage(img, 0, 0, 200, 200);
			
			function rotate(){
				i += 5;
				if (i>359)
					i = 0;
				
				var angle = 30 * Math.PI / 180;				
				canvas.save();
				canvas.clearRect(0, 0, 200, 200);
				canvas.translate(x, y);
				canvas.rotate(angle);
				canvas.drawImage(img, -width / 2, -height / 2, width, height);
				canvas.rotate(-angle);
				canvas.translate(-x, -y);				
				canvas.restore();
			
				//setTimeout(rotate, 25);
			}
			
		});
		/*
		// Once it's loaded draw the image on the canvas.
		img.addEventListener('load', function () {
		// Original resolution: x, y.
		context.drawImage(this, 0, 0);

		// Now resize the image: x, y, w, h.
		context.drawImage(this, 160, 0, 120, 70);

		// Crop and resize the image: sx, sy, sw, sh, dx, dy, dw, dh.
		context.drawImage(this, 8, 20, 140, 50, 0, 150, 350, 70);
		}, false);

		*/
		
		

	
}