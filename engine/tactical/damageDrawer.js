window.damageDrawer = {
	
	damageimg: new Image().src = "img/damage.png",
    damagemaskimg: new Image().src = "img/damagemask.png", 
    
    getOrginalImage: function(ship){
		return shipManager.shipImages[ship.id];
	},
    
    getShipImage: function(ship){
		//{orginal:img, orginalimagedata:null, modimagedata:null};
		var images = shipManager.shipImages[ship.id];
		if (!images.modified){

			var width = images.orginal.width;
			var height = images.orginal.height;
			var canvas  = $('<canvas id="constcan" width="'+width+'" height="'+height+'"></canvas>');
			var context = canvas.get(0).getContext("2d");
			var orginal = images.orginal;
			if (!orginal.loaded){
				//console.log("image not loaded, returning orginal");
				return images.orginal;
			}
				
			context.drawImage(orginal, 0, 0);
			//var data = context.getImageData(0, 0, width, height);
			//images.modified = data;
			var img = new Image();
			img.src = canvas.get(0).toDataURL();
			
			images.modified = img;
			$(images.modified).bind("load", function(){
				images.modified.loaded = true;
				
			});
			
		}
		return images.modified;
		
		
		
		
	}     
	
	
	
}
