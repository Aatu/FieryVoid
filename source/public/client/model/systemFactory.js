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
        if (systemJson.jsClass)
            return new window[systemJson.jsClass](systemJson, ship);
        else
            return new ShipSystem(systemJson, ship);
    }
    
}