window.hexgrid = {

    hexlenght: 50,
    hexgridid: "hexgrid",
    hexlinewidth: 1,
    hexlinecolor: "rgba(255,255,255,0.18)",
    hexHeight: function(){return hexgrid.hexlenght*gamedata.zoom*1.5;},
    hexWidth: function(){return hexgrid.hexlenght*gamedata.zoom*0.8660254*2},
    selectedHex: null,
    
    drawHexGrid: function(){

        var renderer  = new window.canvasHexGridRenderer(hexgrid, gamedata, graphics, deployment);
        renderer.renderHexGridOnCanvas("hexgrid");

    },
    
    drawGameSpace: function(pos){
        var gamespace = gamedata.gamespace;
        
        if(gamespace != null && gamespace != ""){
            var width = parseInt(gamespace.substr(0, gamespace.indexOf("x")));
            var height = parseInt(gamespace.substr(gamespace.indexOf("x")+1));
            
            if(width == -1 || height == -1){
                return;
            }
            
            var lefttop = hexgrid.hexCoToPixel(-(width/2), (height - (height/2)));
            var righttop = hexgrid.hexCoToPixel((width-(width/2)), (height - (height/2)));
            var leftbottom = hexgrid.hexCoToPixel(-(width/2), -(height/2));
            var rightbottom = hexgrid.hexCoToPixel((width-(width/2)), - (height/2));
            
            var canvas = window.graphics.getCanvas("hexgrid");
            window.graphics.drawLine(canvas, lefttop.x, lefttop.y, righttop.x, righttop.y, 3);
            window.graphics.drawLine(canvas, lefttop.x, lefttop.y, leftbottom.x, leftbottom.y, 3);
            window.graphics.drawLine(canvas, rightbottom.x, rightbottom.y, righttop.x, righttop.y, 3);
            window.graphics.drawLine(canvas, leftbottom.x, leftbottom.y, rightbottom.x, rightbottom.y, 3);
        }
    },
    
    positionToPixel: function(pos){
        if (!pos)
            return null;
            
        var pixpos = hexgrid.hexCoToPixel(pos.x, pos.y);
        
        if (pos.xO)
            pixpos.x = pixpos.x + (pos.xO*gamedata.zoom);
        
        if (pos.yO)
            pixpos.y = pixpos.y + (pos.yO*gamedata.zoom);
        
        return pixpos;
        
    },
    
    //ATTENTION, this is bloody magic! (I don't really know how it works)
    pixelCoToHex: function(px, py){
        origoHexX = gamedata.scroll.x;
        origoHexY = gamedata.scroll.y;
        
        var hl = hexgrid.hexlenght*gamedata.zoom;
        var a = hl*0.5
        var b = hl*0.8660254 //0.86602540378443864676372317075294
        
        
    
        var x = px-b;
        var y = py-hl;
        
        x += gamedata.scrollOffset.x+hexgrid.hexWidth();
        y += gamedata.scrollOffset.y+hexgrid.hexHeight();
     
        var h;
        
        if (gamedata.scroll.y % 2 == 0){
            h = (x/(b*2))+0.5;
            
        }else{
            h = (x/(b*2))+1; 
            
        }
        

        var hx = h + origoHexX;
        
        var xmod = hx - Math.floor(hx);
        if (xmod > 0.5){
            xmod -= 0.5;
        }else{
            xmod = 0.5 -xmod;
        }
        xmod *= 2;
        var ymod = a*xmod;
        
        var start = a-ymod-gamedata.scrollOffset.y;
        
        if (py<=start){
            hy = gamedata.scroll.y;
        }else{
            var i = 0;
            while (true){
                i++;
                var hexl = 0;
                if (i%2==0){
                    hexl = hl +((a-ymod)*2);
                }else{
                    hexl = hl +((ymod)*2);
                }
                
                start += hexl;
                if (py<=start){
                    hy = gamedata.scroll.y+i;
                    break;
                }
            }
        }
        if (gamedata.scroll.y % 2 == 0){
            if (hy  % 2==0){
                hx = Math.floor(hx);
            }else{
                hx = Math.round(hx);
            }
        }else{
            if (hy  % 2==0){
                hx = Math.round(hx-1);
            }else{
                hx = Math.floor(hx);
            }
            
        }
        
        //console.log("this hex is: " + hx +","+hy);
        return {x:hx, y:hy, xO:0, yO:0};
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
        /*
        if ((v+gamedata.scroll.y)%2 == 0){
            x = h*b*2;
        }else{
            x = h*b*2+b;
        }
        */
        if ((v+gamedata.scroll.y)%2 == 0){
            x = h*b*2; //-b*2;
        }else{
            x = h*b*2-b;
        }
        
        y = v*hl*2-(a*v);
        
        x -= gamedata.scrollOffset.x+hexgrid.hexWidth();
        y -= gamedata.scrollOffset.y+hexgrid.hexHeight();
        
        return {x:x+b, y:y+hl};
    },
    
    getHexToDirection: function(d, x, y){
        var pos = {x:x, y:y};
        pos = hexgrid.positionToPixel(pos);
        var pos2 = mathlib.getPointInDirection(hexgrid.hexHeight(), d, pos.x, pos.y);
        return hexgrid.pixelCoToHex(pos2.x, pos2.y);
        
        
    },
        
    isOccupiedPos: function(pos){
        var others = Array();
        
        for (var i in gamedata.ships){
            var otherpos = shipManager.getShipPosition(gamedata.ships[i]);
            
            if (otherpos.x == pos.x && otherpos.y == pos.y && otherpos.xO == pos.xO && otherpos.yO == pos.yO){
                return true;
            }
            
        }
        
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
};
