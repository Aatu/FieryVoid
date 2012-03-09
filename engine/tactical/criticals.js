window.shipManager.criticals = {

    hasCritical: function(system, name){
        var amount = 0;
        for (var i in system.criticals){
            var crit = system.criticals[i];
            if (crit.phpclass == name)
                amount++;
        }
        return amount;
    },
    
    hasCriticals: function(system){
    
        return (system.criticals.length > 0);

    },
    
    hasCriticalInAnySystem: function(ship, name){
        var amount = 0;
        for (var a in ship.systems){
            var system = ship.systems[a];
            for (var i in system.criticals){
                var crit = system.criticals[i];
                if (crit.phpclass == name)
                    amount++;
            }
        }
        return amount;
    },
    

}
