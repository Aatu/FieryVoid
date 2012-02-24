window.hexgrid = {

	hexlenght: 50,
	hexgridid: "hexgrid",
	hexlinewidth: 1,
	hexlinecolor: "rgba(255,255,255,0.18)",
    hexHeight: function(){return hexgrid.hexlenght*gamedata.zoom*1.5;},
    hexWidth: function(){return hexgrid.hexlenght*gamedata.zoom*0.8660254*2}, 
	
	drawHexGrid: function(){
	
		
			
		var hl = hexgrid.hexlenght*gamedata.zoom;
		var a = hl*0.5
		var b = hl*0.8660254 //0.86602540378443864676372317075294
		
		var horisontalcount = (gamedata.gamewidth/hexgrid.hexWidth())+2;
		var verticalcount = gamedata.gameheight/hexgrid.hexHeight()+2;
		
		var canvas = window.graphics.getCanvas("hexgrid");
		graphics.clearCanvas("hexgrid");
		canvas.save();
		canvas.fillStyle   = hexgrid.hexlinecolor;
		canvas.strokeStyle = hexgrid.hexlinecolor;
		
		
		if (gamedata.zoom <= 0.8)
			canvas.strokeStyle = "rgba(255,255,255,0.16)";
		
		if (gamedata.zoom <= 0.7)
			canvas.strokeStyle = "rgba(255,255,255,0.13)";
		
		if (gamedata.zoom <= 0.6)
			canvas.strokeStyle = "rgba(255,255,255,0.1)";
		
		
			
		if (gamedata.zoom < 0.7)
			return;
		
		canvas.lineWidth = hexgrid.hexlinewidth;

			
		for (var v = 0; v<=verticalcount; v++){
			for (var h = 0 ; h<=horisontalcount;h++){
				
				var x, y;
				
				if ((v+gamedata.scroll.y)%2 == 0){
					x = h*b*2;
				}else{
					x = h*b*2-b;
				}
				
				y = v*hl*2-(a*v);

                x -= gamedata.scrollOffset.x+hexgrid.hexWidth();
                y -= gamedata.scrollOffset.y+hexgrid.hexHeight();
				
				if (v==0 && h == 0)
					window.graphics.drawHexagon(canvas, x, y, hl, true, true, true);
				else if (v==0 && h != 0){
					window.graphics.drawHexagon(canvas, x, y, hl, false, true, true);
				}else if ((v+gamedata.scroll.y)%2 == 0 && h == 0)
					window.graphics.drawHexagon(canvas, x, y, hl, true, false, false);
				else if (v != 0 && (v+gamedata.scroll.y)%2 != 0 && h == 0){
					window.graphics.drawHexagon(canvas, x, y, hl, true, true, false);
				}else{
					window.graphics.drawHexagon(canvas, x, y, hl, false, false, false);
				}		
			}
		}
		
		canvas.restore();
		
	},
	
	
	
	pixelCoToHex: function(px, py){
		var hl = hexgrid.hexlenght*gamedata.zoom;
		var a = hl*0.5
		var b = hl*0.8660254 //0.86602540378443864676372317075294
		
		//TODO: finish this
	
	},
	
	hexCoToPixel: function(hx, hy){
		origoHexX = gamedata.scroll.x;
		origoHexY = gamedata.scroll.y;
		
		var hl = hexgrid.hexlenght*gamedata.zoom;
		var a = hl*0.5
		var b = hl*0.8660254 //0.86602540378443864676372317075294
		
		var h = hx - origoHexX;
		var v = hy - origoHexY;
		var x, y;
		
		if ((v+gamedata.scroll.y)%2 == 0){
			x = h*b*2;
		}else{
			x = h*b*2+b;
		}
		
		y = v*hl*2-(a*v);
		
		x -= gamedata.scrollOffset.x+hexgrid.hexWidth();
        y -= gamedata.scrollOffset.y+hexgrid.hexHeight();
		
		return {x:x+b, y:y+hl};
	},
	
	getHexToDirection: function(d, x, y){
		if (y%2==0)
			return hexgrid.getHexToDirectionEven(d, x, y);
		else
			return hexgrid.getHexToDirectionUneven(d, x, y);
		
	},
	
	getHexToDirectionEven: function(d, x, y){
		if (d == 0){
			return {x:x+1, y:y, xO:0, yO:0};
		}
		if (d == 60){
			return {x:x, y:y+1, xO:0, yO:0};
		}
		if (d == 120){
			return {x:x-1, y:y+1, xO:0, yO:0};
		}
		if (d == 180){
			return {x:x-1, y:y, xO:0, yO:0};
		}
		if (d == 240){
			return {x:x-1, y:y-1, xO:0, yO:0};
		}
		if (d == 300){
			return {x:x, y:y-1, xO:0, yO:0};
		}
		
		return {x:x, y:y, xO:0, yO:0}
	
	},
	
	getHexToDirectionUneven: function(d, x, y){
		if (d == 0){
			return {x:x+1, y:y, xO:0, yO:0};
		}
		if (d == 60){
			return {x:x+1, y:y+1, xO:0, yO:0};
		}
		if (d == 120){
			return {x:x, y:y+1, xO:0, yO:0};
		}
		if (d == 180){
			return {x:x-1, y:y, xO:0, yO:0};
		}
		if (d == 240){
			return {x:x, y:y-1, xO:0, yO:0};
		}
		if (d == 300){
			return {x:x+1, y:y-1, xO:0, yO:0};
		}
		
		return {x:x, y:y, xO:0, yO:0}
	
	},
	
	isOccupiedPos: function(pos){
		var others = Array();
		
		for (var i in gamedata.ships){
			var otherpos = shipManager.getShipPosition(gamedata.ships[i]);
			
			if (otherpos.x == pos.x && otherpos.y == pos.y && otherpos.xO == pos.xO && otherpos.yO == pos.yO){
				console.log("occupied")
				return true;
			}
			
		}
		
		console.log("unoccupied")
		return false;
		
	},
	
	getOffsetPositionInHex: function(pos, direction, percentage, unoccupied){
	
		posPix = hexgrid.hexCoToPixel(pos.x, pos.y);
		var dis = hexgrid.hexlenght*percentage;
		
		var newpos = mathlib.getPointInDirection( dis, direction, posPix.x, posPix.y)
		
		pos.xO = Math.round(newpos.x - posPix.x);
		pos.yO = Math.round(newpos.y - posPix.y);
	
		var i = 0;
		while (unoccupied && hexgrid.isOccupiedPos(pos)){
			i++;	
			var disadd = i*5;
			newpos = mathlib.getPointInDirection( dis+disadd, direction, posPix.x, posPix.y)
			
			pos.xO = Math.round(newpos.x - posPix.x);
			pos.yO = Math.round(newpos.y - posPix.y);
						
			
			/*
			if (hexgrid.isOccupiedPos(pos))
				pos.yO += 5;
			
			if (hexgrid.isOccupiedPos(pos))
				pos.yO += -10;
			*/
			
		}
			
		return pos;
	
	}
	
	




}
