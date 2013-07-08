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
        var system = new window[name](systemJson, ship);
        
        if(system.duoWeapon || system.dualWeapon){
            for(var i in system.weapons){
                var newWeaponName = systemJson.weapons[i].name;
                var newName = newWeaponName.charAt(0).toUpperCase() + newWeaponName.slice(1);
                system.weapons[i] = new window[newName](systemJson.weapons[i], ship);
            }
        }
        
        //console.log(name);
        return system;
    }
    
}