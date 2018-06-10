import * as React from "react";
import styled from "styled-components"
import {Clickable} from "../styled";
import {Tooltip, TooltipHeader, TooltipEntry} from "../common"

const Text = styled.span`
    color: white;
    font-family:arial;
    font-size:12px;
`;

const Thruster = styled.div`
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-around;

    :before {
        content: "";
        position: absolute;
        width: 40px;
        height: 40px;
        z-index: -1;
        background-image: ${ props => {
            switch(props.crits) {
                case 11: 
                    return 'url(img/systemicons/thruster1-critical12.png);'
                case 10: 
                    return 'url(img/systemicons/thruster1-critical1.png);'
                case 1: 
                    return 'url(img/systemicons/thruster1-critical2.png);'
                default: 
                    return 'url(img/systemicons/thruster1.png);'
            }
        }}
        background-size: cover;
        transform: ${props => {
            switch(props.direction) {
                case 4:
                    return "rotate(180deg)";
                case 1:
                    return "rotate(90deg)"; 
                case 2:
                    return "rotate(270deg)";
                default: 
                    return "none"
            }
        }};
      }

    
    ${Clickable}
`;

const ThrusterContainer = styled.div`
    display: flex;
    position: absolute;
`;

const ForwardThrusterContainer = ThrusterContainer.extend`
    flex-direction: row;
    left: 60px;
    transform: translate(0, -50%);
    flex-wrap: wrap;
    max-width: 40px;
`;

const AftThrusterContainer = ForwardThrusterContainer.extend`
    left: -100px;
`;

const PortThrusterContainer = ThrusterContainer.extend`
    flex-direction: row;
    top: -120px;
    transform: translate(-50%, 0);
`;

const StarBoardThrusterContainer = PortThrusterContainer.extend`
    top: 80px;
`;

const ThrusterSetContainer = styled.div`
    position: relative;
    transform: ${props => `rotate(${props.rotation}deg);`}

    ${Text} {
        transform: ${props => `rotate(${-props.rotation}deg);`}
    }
`;

const ThrustUIContainer = styled.div`
    position: absolute;
    left: ${props => props.left};
    top: ${props => props.top};
    transform: translate(-50%, -50%);
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: blue;
`;


const ThrustTooltip = Tooltip.extend`
    top: 125px;
    min-width: 150px;
`

const ThrustButtons = styled.div`
    margin-top: 14px;
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
`
const IconButton = styled.div`
    width: 40px;
    height: 40px;
    background-size: cover;
    margin: 5px;
    font-size: 30px;
    
    ${Clickable}
`

const TooltipEntryButton = TooltipEntry.extend`
    ${Clickable}
`

class ShipThrust extends React.Component{

    constructor(props) {
        super(props);
    }

    ready() {
        window.shipWindowManager.doneAssignThrust(this.props.ship)
    };

    cancel() {
        window.shipWindowManager.cancelAssignThrustEvent(this.props.ship)
    };

    resetThrust() {
        const ship = this.props.ship;
        window.shipManager.movement.revertAutoThrust(ship);
        window.shipWindowManager.setData(ship)
        window.shipWindowManager.assignThrust(ship);
    }

    autoAssign() {
        const ship = this.props.ship;
        window.shipManager.movement.autoAssignThrust(ship);
        window.shipWindowManager.setData(ship)
        window.shipWindowManager.assignThrust(ship);
    }

    render(){
        const {ship, position, rotation, totalRequired, remainginRequired, movement} = this.props;
        
        return (
            <ThrustUIContainer onMouseOver={(e) => e.preventDefault()} id="thrustUIContainer" left={`${position.x}px`} top={`${position.y}px`}>
                <ThrusterSetContainer rotation={Math.abs(rotation)}>
                    <ForwardThrusterContainer>
                        {
                            getThrusters(ship, 1, totalRequired, remainginRequired, movement)
                        }
                    </ForwardThrusterContainer>

                    <PortThrusterContainer>
                        {
                            getThrusters(ship, 3, totalRequired, remainginRequired, movement)
                        }
                    </PortThrusterContainer>

                    <StarBoardThrusterContainer>
                        {
                            getThrusters(ship, 4, totalRequired, remainginRequired, movement)
                        }
                    </StarBoardThrusterContainer>

                    <AftThrusterContainer>
                        {
                            getThrusters(ship, 2, totalRequired, remainginRequired, movement)
                        }
                    </AftThrusterContainer>
                </ThrusterSetContainer>
                <ThrustTooltip>
                    <TooltipHeader>Assign thrust</TooltipHeader>
                    {getText(totalRequired, remainginRequired, movement)}
                    {getThrustAvailable(ship)}
                    {getTurnDelay(ship, movement)}
                    <TooltipEntryButton space important onClick={this.resetThrust.bind(this)}>RESET THRUST</TooltipEntryButton>
                    <TooltipEntryButton important onClick={this.autoAssign.bind(this)}>AUTO ASSIGN</TooltipEntryButton>
                    <ThrustButtons>
                        <IconButton onClick={this.ready.bind(this)}>✔</IconButton>
                        <IconButton onClick={this.cancel.bind(this)}>🛇</IconButton>
                    </ThrustButtons>
                </ThrustTooltip>
            </ThrustUIContainer>
        )
    }
}

const getThrustAvailable = (ship) => {
    const thrust = shipManager.movement.getRemainingEngineThrust(ship);
    return (<TooltipEntry space important>Thrust available: {thrust}</TooltipEntry>)
}

const getTurnDelay = (ship, movement) => {

    if (! shipManager.movement.isTurn(movement)) {
        return null;
    }

    const turndelay = shipManager.movement.calculateTurndelay(ship, movement, movement.speed);
    return (<TooltipEntry important>Current turn delay: {turndelay}</TooltipEntry>)
}

const getText = (totalRequired, remainginRequired, movement) => {
    const names = Array("either", "front", "aft", "port", "starboard");

    if (movement.type == "roll") {
        names[0] = "any";
    }

    const list = remainginRequired
        .filter(required => required !== null)
        .map((required, index) => {
            if (required <= 0) {
                return null;
            }
            
            return (<TooltipEntry type={(required === 0) ? 'good' : 'bad'} key={`assign-thrust-text-${index}`}>{required} thrust to {names[index]} thrusters</TooltipEntry>)
        })
        .filter(entry => entry !== null)

    if (list.length === 0) {
        return (<TooltipEntry type="good">All done!</TooltipEntry>)
    }

    return list;
        
}

const getThrusters = (ship, direction, totalRequired, movement) => {
    const thrusters = shipManager.systems.getThrusters(ship, direction);

    if (movement.type !== 'roll' && totalRequired[direction] === null) {
        return null;
    }

    return thrusters.map((thruster, index) => {

        const assignThrust = () => {
            shipManager.movement.assignThrust(ship, thruster);
			shipWindowManager.assignThrust(ship);
        }

        const unAssignThrust = (e) => {
            e.preventDefault();
            shipManager.movement.unAssignThrust(ship, thruster);
			shipWindowManager.assignThrust(ship);
        }

        let crits = shipManager.criticals.hasCritical(thruster, "HalfEfficiency") ? 10 : 0;
        

        if (shipManager.criticals.hasCritical(thruster, "FirstThrustIgnored")) {
            crits += 1
        }
       

        const channeled = shipManager.movement.getAmountChanneled(ship, thruster);
        const output = shipManager.systems.getOutput(ship, thruster);
        return (<Thruster crits={crits} onClick={assignThrust} onContextMenu={unAssignThrust} direction={direction} key={`thruster-${direction}-${index}`}><Text>{channeled}/{output}</Text></Thruster>)
    });
}

export default ShipThrust;

/*
setSystemsForAssignThrust: function(ship, requiredThrust, stillReq){
		var loc = "";
		for (var i = 4; i>0;i--){
			if ( i == 1) {loc =".frontcontainer";}
			if ( i == 2) {loc =".aftcontainer";}
			if ( i == 3) {loc =".portcontainer";}
			if ( i == 4) {loc =".starboardcontainer";}
			
			var thrusters = shipManager.systems.getThrusters(ship, i);
			for (var t in thrusters){
				var thruster = thrusters[t];
				var slotnumber = parseInt(t)+1;
				if (shipManager.systems.isDestroyed(ship, thruster))
					continue;
					
				var cont = $(".BPcontainer.thrusters " +loc+" .slot_"+slotnumber);
				cont.addClass("exists");
				cont.data("id", thruster.id);
				cont.data("ship",ship.id);
				cont.data("direction", thruster.direction);
				
				
				if (requiredThrust[i] != null){
				cont.addClass("enableAssignThrust");
				
				}
				if (stillReq[i] == null){
					cont.removeClass("enableAssignThrust");
				}
				
				var field = cont.find(".efficiency.value");
				var channeled = shipManager.movement.getAmountChanneled(ship, thruster);
				var output = shipManager.systems.getOutput(ship, thruster);
			
				if (channeled > output){
					field.addClass("darkred");
				}else{
					field.removeClass("darkred");
				}
				
				if (channeled < 0)
					channeled = 0;
					
				field.html(channeled + "/" + output);
				
			
			}
		}
		
		var field = $(".BPcontainer.thrusters .engine .efficiency.value");
		var rem = shipManager.movement.getRemainingEngineThrust(ship);
		field.html(rem);
	
    },
    */