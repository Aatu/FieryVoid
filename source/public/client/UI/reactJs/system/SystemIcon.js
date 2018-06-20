import * as React from "react";
import styled from "styled-components"


const System = styled.div`
    position: relative;
    width: 30px;
    height: 30px;
    margin: 2px;
    border: 1px solid #496791;
    background-color: black;
    background-image: ${props => `url(${props.background})`};
    background-size: cover;
    filter: ${props => props.destroyed ? 'blur(1px)' : 'none'};
    
    :before {
        content: "";
        position:absolute;
        width: 100%;
        height: 100%;
        opacity: ${props => props.overlay ? '0.5' : '0'};
        background-color: ${props => props.overlay || 'transparent'};
    }
`

const HealthBar = styled.div`
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    border-top: 1px solid #496791;
    background-color: black;

    :before {
        content: "";
        position:absolute;
        width:  ${props => `${props.health}%`};
        height: 100%;
        left: 0;
        bottom: 0;
        background-color: ${props => props.criticals ? '#ed6738' : '#8dd872'};
    }
`;

class SystemIcon extends React.Component{

    constructor(props) {
        super(props);
    }

    render(){
        const {system, ship} = this.props;
        
        const intializedSystem = shipManager.systems.initializeSystem(system);
        const destroyed = getDestroyed(ship, intializedSystem);

        if (getDestroyed(ship, intializedSystem)){
            return (
                <System background={getBackgroundImage(intializedSystem)} overlay="black" destroyed><HealthBar health="0"/></System>
            )
        }

        return (
            <System 
                background={getBackgroundImage(intializedSystem)}
                
            >
            <HealthBar health={getStructureLeft(ship, intializedSystem)} criticals={hasCriticals(intializedSystem)}/>
            </System>
        )
    }
}

const getStructureLeft = (ship, system) => (system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth * 100;

const getDestroyed = (ship, system) => shipManager.systems.isDestroyed(ship, system)

const getBackgroundImage = (system) => {
    if (system.name == "thruster") {
        return './img/systemicons/thruster';
    } else if (system.iconPath) {
        return `./img/systemicons/${system.iconPath}`;
    } else {
        return `./img/systemicons/${system.name}.png`;
    }
}

const hasCriticals = (system) => shipManager.criticals.hasCriticals(system)



const setSystemData = (ship, system) => {
    var parentWeapon = null;
    var parentWindow = null;

    if (system.parentId > 0) {
        parentWeapon = system;

        while (parentWeapon.parentId > 0) {
            parentWeapon = shipManager.systems.getSystem(ship, parentWeapon.parentId);
        }

        system.damage = parentWeapon.damage;

        //parentWindow = shipwindow.find(".parentsystem_" + parentWeapon.id);
    }

    shipManager.systems.initializeSystem(system);

    if (system.dualWeapon && system.weapons != null) {
        var weapon = system.weapons[system.firingMode];
        shipManager.systems.initializeSystem(weapon);
    }

    //var systemwindow = shipwindow.find(".system_" + system.id);

    if (systemwindow.length == 0 && system.parentId > -1) {
        //systemwindow = shipwindow.find(".parentsystem_" + system.parentId);
    }

    var output = shipManager.systems.getOutput(ship, system);
    var field = systemwindow.find(".efficiency.value");

    /*
    if (system.name == "structure") {
        systemwindow.find(".healthvalue ").html(system.maxhealth - damageManager.getDamage(ship, system) + "/" + system.maxhealth + " A" + shipManager.systems.getArmour(ship, system));
    }

    if (system.parentId > 0) {
        parentWindow.find(".healthbar").css("width", (system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth * 100 + "%");
    } else {
        systemwindow.find(".healthbar").css("width", (system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth * 100 + "%");
    }

    if (system.name == "thruster") {
        systemwindow.data("direction", system.direction);
        systemwindow.find(".icon").css("background-image", "url(./img/systemicons/thruster" + system.direction + ".png)");
    }
    */

    //shipWindowManager.removeSystemClasses(systemwindow);

    /*
    if (shipManager.systems.isDestroyed(ship, system)) {
        if (system.parentId > 0) {
            if (shipManager.systems.getSystem(ship, system.parentId).duoWeapon) {
                // create an iconMask at the top of the DOM for the system.
                var iconmask_element = document.createElement('div');
                iconmask_element.className = "iconmask";
                parentWindow.find(".iconmask").remove();
                parentWindow.find(".icon").append(iconmask_element);
            }

            parentWindow.addClass("destroyed");
        } else {
            systemwindow.addClass("destroyed");
        }
        return;
    }

    if (shipManager.criticals.hasCriticals(system)) {
        if (system.parentId > 0) {
            parentWindow.addClass("critical");
        } else {
            systemwindow.addClass("critical");
        }
    }

    */

    if (shipManager.power.setPowerClasses(ship, system, systemwindow)) return;

    if (system.weapon) {
        var firing = weaponManager.hasFiringOrder(ship, system);

        // To avoid double overlay of loading icon mask in case of a
        // duoWeapon in a dualWeapon
        if (!weaponManager.isLoaded(system) && !(system.duoWeapon && system.parentId > 0)) {
            systemwindow.addClass("loading");
        } else {
            systemwindow.removeClass("loading");
        }

        if (weaponManager.isSelectedWeapon(system)) {
            systemwindow.addClass("selected");
        } else {
            systemwindow.removeClass("selected");
        }

        if (firing && firing != "self" && !system.duoWeapon && !systemwindow.hasClass("loading")) {
            systemwindow.addClass("firing");

            if (system.parentId > -1) {
                var parentSystem = shipManager.systems.getSystem(ship, system.parentId);

                if (parentSystem.duoWeapon) {
                    $(".system_" + system.parentId).addClass("duofiring");
                }
            }
        } else if (firing == "self") {
            systemwindow.addClass("firing");
            systemwindow.addClass("selfIntercept");
        } else {
            firing = false;
            systemwindow.removeClass("firing");
            systemwindow.removeClass("selfIntercept");
        }

        if (system.ballistic) {
            systemwindow.addClass("ballistic");
        } else {
            systemwindow.removeClass("ballistic");
        }

        if (!firing && (Object.keys(system.firingModes).length > 1 || system.dualWeapon)) {
            if (system.parentId >= 0) {
                var parentSystem = shipManager.systems.getSystem(ship, system.parentId);

                if (parentSystem.parentId >= 0) {
                    parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
                    $(".parentsystem_" + parentSystem.id).addClass("modes");
                    var modebutton = $(".mode", $(".parentsystem_" + parentSystem.id));
                } else {
                    $(".parentsystem_" + parentSystem.id).addClass("modes");
                    var modebutton = $(".mode", systemwindow);
                }

                modebutton.html("<span>" + parentSystem.firingModes[parentSystem.firingMode].substring(0, 1) + "</span>");
            } else {
                systemwindow.addClass("modes");

                var modebutton = $(".mode", systemwindow);
                modebutton.html("<span>" + system.firingModes[system.firingMode].substring(0, 1) + "</span>");
            }
        }

        if (firing && system.canChangeShots) {
            var fire = weaponManager.getFiringOrder(ship, system);

            if (fire.shots < system.shots) {
                systemwindow.addClass("canAddShots");
            } else {
                systemwindow.removeClass("canAddShots");
            }

            if (fire.shots > 1) {
                systemwindow.addClass("canReduceShots");
            } else {
                systemwindow.removeClass("canReduceShots");
            }

            field.html(fire.shots + "/" + system.shots);
        } else if (!firing) {
            if (system.duoWeapon) {
                var UI_active = systemwindow.find(".UI").hasClass("active");

                shipWindowManager.addDuoSystem(ship, system, systemwindow);

                if (UI_active) {
                    systemwindow.find(".UI").addClass("active");
                }
            } else {
                if (system.dualWeapon && system.weapons) {
                    system = system.weapons[system.firingMode];
                }

                var load = weaponManager.getWeaponCurrentLoading(system);
                var loadingtime = system.loadingtime;

                if (system.normalload > 0) {
                    loadingtime = system.normalload;
                }

                if (load > loadingtime) {
                    load = loadingtime;
                }

                var overloadturns = "";

                if (system.overloadturns > 0 && shipManager.power.isOverloading(ship, system)) {
                    overloadturns = "(" + system.overloadturns + ")";
                }

                if (system.overloadshots > 0) {
                    field.html("S" + system.overloadshots);
                } else {
                    field.html(load + overloadturns + "/" + loadingtime);
                }
            }
        }
    } else if (system.name == "thruster") {
        systemwindow.data("direction", system.direction);
        systemwindow.find(".icon").css("background-image", "url(./img/systemicons/thruster" + system.direction + ".png)");

        var channeled = shipManager.movement.getAmountChanneled(ship, system);
        if (channeled > output) {
            field.addClass("darkred");
        } else {
            field.removeClass("darkred");
        }

        if (channeled < 0) {
            channeled = 0;
        }

        field.html(channeled + "/" + output);
    } else if (system.name == "engine") {
        var rem = shipManager.movement.getRemainingEngineThrust(ship);

        field.html(rem + "/" + output);
    } else if (system.name == "reactor") {
        field.html(shipManager.power.getReactorPower(ship, system));
    } else if (system.output > 0) {
        field.html(output);
    }
}

export default SystemIcon;
