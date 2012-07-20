
window.EWindicators = {

    indicators: Array(),
    ewCanvas: null,
    ewContext: null,
    start: null,
    fps: 0,
    clear: true,
     
    getEwCanvas: function(){
        if (EWindicators.ewCanvas == null){
            EWindicators.ewCanvas = document.getElementById("EWindicators");
            EWindicators.ewContext = graphics.getCanvas("EWindicators");
        }
        return EWindicators.ewContext;
    },
    
    drawEWindicators: function(){

        if (EWindicators.ewCanvas == null){
            EWindicators.ewCanvas = document.getElementById("EWindicators");
            EWindicators.ewContext = graphics.getCanvas("EWindicators");
        }
        
       
        
        EWindicators.clearEwCanvas();
           
        
        for (var i in EWindicators.indicators){
                 
            EWindicators.indicators[i].draw(EWindicators.indicators[i]);
         
           
        }
        
       

    },
    
    clearEwCanvas: function(){
        graphics.clearContext(EWindicators.ewContext, EWindicators.ewCanvas);
                
      
        
    }
      
    
    
    
    
}

