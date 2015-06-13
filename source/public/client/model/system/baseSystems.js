var Reactor = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Reactor.prototype = Object.create( ShipSystem.prototype );
Reactor.prototype.constructor = Reactor;


var Scanner = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Scanner.prototype = Object.create( ShipSystem.prototype );
Scanner.prototype.constructor = Scanner;


var ElintScanner = function(json, ship)
{
    Scanner.call( this, json, ship);
}
ElintScanner.prototype = Object.create( Scanner.prototype );
ElintScanner.prototype.constructor = ElintScanner;


var Engine = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Engine.prototype = Object.create( ShipSystem.prototype );
Engine.prototype.constructor = Engine;


var CnC = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
CnC.prototype = Object.create( ShipSystem.prototype );
CnC.prototype.constructor = CnC;


var Thruster = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Thruster.prototype = Object.create( ShipSystem.prototype );
Thruster.prototype.constructor = Thruster;


var GraviticThruster = function(json, ship)
{
    Thruster.call( this, json, ship);
}
GraviticThruster.prototype = Object.create( Thruster.prototype );
GraviticThruster.prototype.constructor = GraviticThruster;


var Hangar = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Hangar.prototype = Object.create( ShipSystem.prototype );
Hangar.prototype.constructor = Hangar;

var Catapult = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Catapult.prototype = Object.create( ShipSystem.prototype );
Catapult.prototype.constructor = Catapult;

var CargoBay = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
CargoBay.prototype = Object.create( ShipSystem.prototype );
CargoBay.prototype.constructor = CargoBay;

var JumpEngine = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
JumpEngine.prototype = Object.create( ShipSystem.prototype );
JumpEngine.prototype.constructor = JumpEngine;


var Structure = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Structure.prototype = Object.create( ShipSystem.prototype );
Structure.prototype.constructor = Structure;


var ElintArray = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
ElintArray.prototype = Object.create( ShipSystem.prototype );
ElintArray.prototype.constructor = ElintArray;


var Jammer = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Jammer.prototype = Object.create( ShipSystem.prototype );
Jammer.prototype.constructor = Jammer;


var Stealth = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Stealth.prototype = Object.create( ShipSystem.prototype );
Stealth.prototype.constructor = Stealth;


var Stealth = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Stealth.prototype = Object.create( ShipSystem.prototype );
Stealth.prototype.constructor = Stealth;

var HkControlNode = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
HkControlNode.prototype = Object.create( ShipSystem.prototype );
HkControlNode.prototype.constructor = HkControlNode;
