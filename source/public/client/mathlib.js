window.mathlib = {

	distance: function (x1,y1,x2,y2){
		var a = Math.sqrt(Math.pow((x2-x1),2) + Math.pow((y2-y1),2));
		return a;
	},
	
        arrayIsEmpty: function(array){
            for(var i in array){
                return false;
            }

            return true;
        },
    
	addToDirection: function(current, add){
        add = add % 360;

		var ret = 0;
		if (current + add > 360){
			ret =  0+(add-(360-current));
				
		}else if (current + add < 0){
			ret = 360 + (current + add);
		}else{	
			ret = current + add;
		}

		return ret;
	},
	
	getPointBetween: function(start, end, percentage){
		var x = Math.floor(start.x + percentage * (end.x - start.x));
		var y = Math.floor(start.y + percentage * (end.y - start.y));
	
		return {x:x, y:y};
	},
	
	getDistance: function(start, end){
//		console.log(start, end);
		if (!end || !start)
			console.trace();
		return Math.sqrt((end.x-start.x)*(end.x-start.x) + (end.y-start.y)*(end.y-start.y));
	},
    
    getDistanceBetweenShips: function(s1, s2){
        var start = shipManager.getShipPositionInWindowCoWithoutOffset(s1);
        var end = shipManager.getShipPositionInWindowCoWithoutOffset(s2);
        var dis = Math.sqrt((end.x-start.x)*(end.x-start.x) + (end.y-start.y)*(end.y-start.y));
        
        return dis;
    },
    
    getDistanceBetweenShipsInHex: function(s1, s2){
        var dis = mathlib.getDistanceBetweenShips(s1, s2);
        return (dis / hexgrid.hexWidth());
    },
    
	getDistanceHex: function(start, end){
		var dis = Math.sqrt((end.x-start.x)*(end.x-start.x) + (end.y-start.y)*(end.y-start.y));
		return (dis / hexgrid.hexWidth());
	},
	
	getPointInDistanceBetween: function(start, end, distance){
		var totalDist = mathlib.getDistance(start, end);
		var perc = 1;
		if (totalDist != 0){
			perc = distance / totalDist;
		}		
			
		return mathlib.getPointBetween(start, end, perc);
	},
	
	isOver: function(start, end, point){
		if (mathlib.getDistance(start, point) > mathlib.getDistance(start,end))
			return true;
			
		return false;
	},
	
	getFacingBetween: function(angle1, angle2, percentage, right){
		//console.log(angle1  + " " + angle2);
		var total;
		var difference;
		if (right){
			if ( angle1 > angle2){
				difference = 360 - angle1 + angle2;
			}else{
				difference = angle2 - angle1;
			}
			
		}else{
			if (angle1 < angle2){
				difference = (angle1 + (360-angle2))*-1;
			}else{
				difference = angle2 - angle1;
			}
		
		}
		
		var step = difference*percentage;

		if (angle1 + step > 360)
			return angle1 + step - 360;
		
		if (angle1 + step < 0)
			return 360 + angle1 + step;
		
		return angle1 + step;
	
	},
	
	addToHexFacing: function(facing, add){
	
		if ((facing + add) > 5){
			return mathlib.addToHexFacing(0, (facing + add - 6));
		}
		
		if ((facing + add) < 0){
			return mathlib.addToHexFacing(6, facing + add);
		}
		
		return facing + add;
	
	},
	
	getPointInDirection: function( r, a, cx, cy){
            
		x = cx + r * Math.cos(a* Math.PI / 180);
		y = cy + r * Math.sin(a* Math.PI / 180);
		
		return {x:Math.round(x), y:Math.round(y)};
    },
	
	getSizeArc: function(start, end){
		a = 0;
		if (start > end){
			a = 360 - start + end;
		
		}else{
			a = end-start;
		}
	
		console.log("size: " + a);
		
		return a;
	
	},
	
	isInArc: function(direction, start, end){
		//console.log("direction: "+direction + " start: " + start + " end: " + end);
		
		//direction: 300 start: 360 end: 240
		if (start == end)
			return true;
			
		if ((direction == 0 && start == 360) || (direction == 0 && end == 360))
			return true;
	
		if (start > end){
			
			return (direction >= start || direction <= end);
				
		}else if (direction >= start && direction <= end){
			return true;
		}
	
		return false;
		
	},
	
	radianToDegree: function(angle){
			return angle * (180.0 / Math.PI);
	},
	
	degreeToRadian: function(angle){
		//radian * (180.0 / Math.PI) = degree
		return (angle / (180.0 / Math.PI));
	},
	
        // IMPORTANT: Both the 'observer' and 'position' parameters
        // should be HEX COORDINATES!!!
	getCompassHeadingOfPosition: function(observer, position){
            
            var oPos = shipManager.getShipPosition(observer);
//		var tPos = hexgrid.pixelCoToHex(position.x, position.y);
            var tPos = position;

            if( oPos.x > 100 || oPos.y > 100 || tPos.x > 100 || tPos.y > 100 ){
               console.log("getCompassHeadingOfPosition: pixel coordinate iso hex coordinate?");
               console.log("oPos: " + oPos.x + "," + oPos.y);
               console.log("tPos: " + tPos.x + "," + tPos.y);
            }


            if (oPos.x == tPos.x && oPos.y == tPos.y){
                            oPos =  shipManager.movement.getPreviousLocation(observer);
            }

            oPos = hexgrid.hexCoToPixel(oPos.x, oPos.y);
            tPos = hexgrid.hexCoToPixel(tPos.x, tPos.y);

            return mathlib.getCompassHeadingOfPoint(oPos, tPos);
	},
	
	getCompassHeadingOfShip: function(observer, target){
	

		var oPos = shipManager.getShipPosition(observer);
		var tPos = shipManager.getShipPosition(target);

		if (oPos.x == tPos.x && oPos.y == tPos.y){
			if (shipManager.hasBetterInitive(observer, target)){
				oPos =  shipManager.movement.getPreviousLocation(observer);
				
			}else{
				tPos =  shipManager.movement.getPreviousLocation(target);
				
			}
		
		}

		
		oPos = hexgrid.hexCoToPixel(oPos.x, oPos.y);
		tPos = hexgrid.hexCoToPixel(tPos.x, tPos.y);
		
		
		
		return mathlib.getCompassHeadingOfPoint(oPos, tPos);
		
	},
	
	getCompassHeadingOfPoint: function(observer, target){
		var dX = target.x - observer.x;
		var dY = target.y - observer.y;
		var heading = 0.0;
		//console.log("dX: " +dX+ " dY: " + dY);				
		if (dX == 0){
			if (dY>0){
				heading = 180.0;
			}else{
				heading = 0.0;
			}
			
		}else if (dY == 0){
			if (dX>0){
				heading = 90.0;
			}else{
				heading = 270.0;

			}
		}else if (dX>0 && dY<0 ){
			//console.log("h:1");
			heading = mathlib.radianToDegree(Math.atan(dX/Math.abs(dY)));
		}else if (dX>0 && dY>0 ){
			//console.log("h:2");
			heading = mathlib.radianToDegree(Math.atan(dY/dX)) + 90;
		}else if (dX<0 && dY>0){
			//console.log("h:3");
			heading = mathlib.radianToDegree(Math.atan(Math.abs(dX)/dY)) + 180;
		}else if (dX<0 && dY<0){
			//console.log("h:4");
			heading = mathlib.radianToDegree(Math.atan(dY/dX)) + 270;
		}
		/*
		}else if (dX>0 && dY>0 ){
			console.log("h:1");
			heading = mathlib.radianToDegree(Math.atan(dX/dY));
		}else if (dX>0 && dY<0 ){
			console.log("h:2");
			heading = mathlib.radianToDegree(Math.atan(Math.abs(dY)/dX)) + 90;
		}else if (dX<0 && dY<0){
			console.log("h:3");
			heading = mathlib.radianToDegree(Math.atan(dX/dY)) + 180;
		}else if (dX<0 && dY>0){
			console.log("h:4");
			heading = mathlib.radianToDegree(Math.atan(dY/Math.abs(dX))) + 270;
		}
		*/
		heading = mathlib.addToDirection(Math.round(heading), -90);

		return heading;
	}


}
