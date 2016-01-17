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
        var system = new window[name](systemJson, ship);
        
        if(system.duoWeapon || system.dualWeapon){
            for(var i in system.weapons){
                var newWeaponName = systemJson.weapons[i].name;
                var newName = newWeaponName.charAt(0).toUpperCase() + newWeaponName.slice(1);
                var newWeapon = new window[newName](systemJson.weapons[i], ship);
                
                if(newWeapon.duoWeapon || newWeapon.dualWeapon){
                    for(var index in newWeapon.weapons){
                        var newSubWeaponName = newWeapon.weapons[index].name;
                        var newSubName = newSubWeaponName.charAt(0).toUpperCase() + newSubWeaponName.slice(1);
                        var newSubWeapon = new window[newSubName](newWeapon.weapons[index], ship);
                
                        newWeapon.weapons[index] = newSubWeapon;
                    }
                }
                
                system.weapons[i] = newWeapon;
            }
        }
        
        //console.log(name);
        return system;
    }
    
}