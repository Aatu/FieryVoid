window.SystemFactory = 
{
    createSystemsFromJson: function(systemsJson, ship)
    {
        var systems = Array();
        for (var i in systemsJson)
        {
            var system = SystemFactory.createSystemFromJson(systemsJson[i], ship);
            systems[system.id] = system;
        }
        
        return systems;
    },
    
    createSystemFromJson: function(systemJson, ship)
    {
        if (systemJson.fighter)
            return new Fighter(systemJson, ship);
        
        var name = systemJson.name.charAt(0).toUpperCase() + systemJson.name.slice(1);
        //console.log(name);
        return new window[name](systemJson, ship);
    }
    
}