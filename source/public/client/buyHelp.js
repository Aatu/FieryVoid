'use strict';

/* The buy dialog's "?" texts (confirm.addBuyHelp): one short line on what each Ammo & Ordnance,
   Enhancements and Options row does, keyed by its enhancement id. Written from the Ship Options
   page (docs/ammo-options.html), which stays the full account - edit both when a rule changes.
   An officer row is not here: its text is his registry summary (officers.js), the line his System
   Info tooltip prints in game. An id with no text simply gets no "?". */
window.buyHelp = {

    TEXT: {
        /* Ship missiles - the missile's own range and damage; the launcher can change them (an
           L-Rack adds 10 to the range). */
        AMMO_B: 'The standard missile: range 20, 20 damage.',
        AMMO_A: 'Range 15, 15 damage, and +6 fire control against fighters (other missiles +3).',
        AMMO_C: 'Range 20, no damage. A unit it hits is -15% to hit with its non-ballistic weapons.',
        AMMO_D: 'Range 15, 12 damage.',
        AMMO_F: 'Range 20, 20 damage, dealt as Flash damage.',
        AMMO_H: 'Range 10, 30 damage, with no fire control bonus against fighters.',
        AMMO_HM: 'Range 20, 20 damage. If it misses without being intercepted it attacks again, from its target\'s previous hex, until its fuel runs out.',
        AMMO_I: 'Fires defensively: a -30 intercept rating against ballistic weapons. Deals no damage.',
        AMMO_J: 'Hex targeted, range 15, no damage. Every ship within 5 hexes of the target hex gets 2 points of Blanket DEW.',
        AMMO_K: 'Range 15. Damage in Pulse mode: D3+3 pulses of 10.',
        AMMO_KK: 'Range 60, 18 damage, dealt as Matter damage. A range penalty of -5% per hex beyond 15 hexes.',
        AMMO_L: 'Range 30, 15 damage.',
        AMMO_M: 'Range 20. Splits into 6 submunitions of 10 damage each. Can only target fighters.',
        AMMO_P: 'Range 20, 30 damage, dealt as Piercing damage. Cannot target fighters.',
        AMMO_S: 'Range 20, 20 damage. Its target is not revealed to the enemy player.',
        AMMO_X: 'Range 20, no damage. +5% to hit per point of OEW and CCEW its target is using; a hit costs the target\'s scanner 1d6 output next turn.',
        AMMO_Z: 'Hex targeted, range 20, 15 damage. Attacks any mines within 3 hexes; +15% to hit a mine in the target hex.',

        //Fighter missiles. AMMO_LBT / AMMO_LIT are no enhancement: the old fighter launchers' rows
        //(confirm.addBuyMissileRows) are looked up as AMMO_ + their firing mode.
        AMMO_FB: 'Range 10, 10 damage.',
        AMMO_FD: 'Range 10, 6 damage. A fighter it hits is more likely to drop out.',
        AMMO_FH: 'Range 5, 15 damage. Limited to 1 per fighter (2 per Super Heavy Fighter).',
        AMMO_FL: 'Range 15, 8 damage.',
        AMMO_FY: 'Range 8, 6 damage.',
        AMMO_DUM: 'Does not fire, but is always the first missile lost to damage, preserving the real ones.',
        AMMO_LBT: 'Range 25, 2d6 damage.',
        AMMO_LIT: 'Range 20, 10 damage.',

        //Mine launcher rounds: the mine attacks a unit that comes within its range.
        MINE_BLB: 'A captor mine: attacks within 3 hexes, 17-26 damage.',
        MINE_BLH: 'A captor mine: attacks within 2 hexes, 25-34 damage.',
        MINE_BLW: 'A captor mine: attacks within 5 hexes, 13-22 damage.',
        MINE_MLB: 'A captor mine: attacks within 4 hexes, 12 damage.',
        MINE_MLW: 'A captor mine: attacks within 7 hexes, 12 damage.',
        MINE_AML: 'A captor mine: attacks within 4 hexes, 12 damage.',
        MINE_BML: 'A captor mine: attacks within 5 hexes, 15 damage.',
        MINE_CML: 'A captor mine: attacks within 6 hexes, 18 damage.',

        //Grome railgun shells, one text for every size (textOf drops SHELL_H / M / L's size letter).
        SHELL_BSC: 'The railgun\'s standard shell. Its damage depends on the railgun\'s size.',
        SHELL_FLH: 'Deals Plasma damage in Flash mode.',
        SHELL_SCT: 'Pulse mode: one shot, plus one more hit for every 25% the roll is under the hit chance.',
        SHELL_HVY: 'Deals extra damage.',
        SHELL_LR: 'A smaller range penalty, but lower damage. Medium and Heavy Railguns only.',
        SHELL_ULR: 'Range improved further, with even lower damage. Heavy Railguns only.',

        //Extra shots for a fighter's gun (Ammo & Ordnance - confirm.BUY_AMMO_IDS).
        EXT_AMMO: 'One more shot per level for each of the flight\'s limited-ammunition guns.',
        EXT_HAMMO: 'One more shot per level for each of the flight\'s limited-ammunition heavy guns.',

        //Ship enhancements and options.
        ADV_ENG: 'The strongest Engine boosts for 1 less power per point of thrust (minimum 1). Lost for good the first time any Engine takes a critical.',
        CHAM_DISG: 'The vessel this ship disguises itself as. The enemy sees that class until they see through the deception.',
        EDF_RANGE: '+1 hex radius per level on every Energy Draining Field (a Variable field\'s normal-power radius only).',
        ELITE_CREW: '+1 to hit and +1 damage per die with all weapons, +5 initiative, +2 Engine, +1 Sensors, +2 power, +1 every Thruster, -5% to be hit, -1 on criticals, -1 turn delay, -20% jump delay, a default shuttle armed free. Per level.',
        ELT_MRN: 'The marines of this ship\'s Grappling Claws and Combat Transporters are Elite.',
        EXT_MRN: 'One more marine unit per level for each Grappling Claw and Combat Transporter.',
        HANG_AS: 'Converts a fighter hangar slot into an assault shuttle slot.',
        HANG_BP: 'Converts a default shuttle slot into a slot for a Breaching Pod.',
        HANG_F: 'Converts an assault shuttle hangar slot into a fighter slot.',
        HANG_MSW: 'Converts a default shuttle into a minesweeping shuttle.',
        HANG_ORD: 'A pool of points spent restocking the missiles of fighters docked on this ship.',
        HBLAST_THR: 'Boosting the Hypergraviton Blaster\'s damage costs only 4 thrust.',
        IFF_SYS: 'Stops this unit\'s mines attacking friendly units.',
        IMPR_ENG: '+1 Engine thrust per level.',
        IMPR_PSY: '+1 range on the Psychic Field.',
        IMPR_REA: '+1 to +4 power, by the unit\'s size (MCV to Enormous).',
        IMPR_SENS: '+1 Scanner rating.',
        IMPR_SR: '+1 rating per level on every Self Repair system.',
        IMPR_THSD: '+1 rating per level on every shield, and +1 Shield Generator output.',
        IMPR_TS: '+1 rating per level on every Thought Shield, and +1 Thought Shield Generator output.',
        IPSH_EETH: 'Eethan Barony variant: +2 thrust, +25% power, +0.1 turn delay, -5 initiative, +4 on Reactor and Engine criticals.',
        IPSH_ESSAN: 'Essan Barony variant: Engine +1 thrust and +2 boxes, Scanner -1 output and -2 boxes, +1 armour on all Structure (to 5).',
        JUMP_ACC: 'Jump delay -33% and jump point range +2 hexes. The drive draws double power and drains the reactor even when off; opening a jump point on a damaged drive is twice as likely to destroy the ship.',
        MAR_CONT: 'A pool of marines restocking the Marines of Breaching Pods docked on this ship, 1 per pod per turn.',
        MARK_FERV: '+1 to hit with all weapons and +10 initiative, but +10% to be hit. Take it when Desperate rules apply to the Markab player.',
        POOR_CREW: '-1 to hit with all weapons, -5 initiative, -2 Engine, -1 Sensors, -2 power, -1 every Thruster, +5% to be hit, +2 on criticals, +1 turn delay, half hangar rate, +20% jump delay, no armed shuttles. Per level.',
        'RE-EVAL': 'Applies the custom re-pricing of this official design. Agree it with your opponent first.',
        SHAD_DIFF: '+1 rating per level on every Energy Diffuser.',
        SHAD_FTRL: 'An integrated Shadow Medium Fighter for this ship\'s bay.',
        SHAD_TEND: 'A matched pair of new Diffuser Tendrils, port and starboard, on the strongest Energy Diffuser.',
        SLUGGISH: '-5 initiative per level.',
        SPARK_CURT: 'Ballistic hit chance against this ship reduced by 2, plus the Spark Field\'s boost level. Unofficial.',
        STEALTH_CT: '-5% to be hit on the covered facings by weapons that use OEW, from a unit holding a lock-on. No effect against Advanced Sensors.',
        VOR_AMETHS: '+1 Adaptive Armor point per level; every even total also raises the per-weapon allowance and the pre-assigned amount.',
        VOR_AZURS: '+1 rating per level on all EM Shields.',
        VOR_CRIMS: 'The Power Capacitor gains +2 storage and +1 recharge per level.',
        VULN_CRIT: '+1 to critical rolls per level.',

        //Mine enhancements.
        MINE_ACC: '+1 Accuracy per level.',
        MINE_ARM: '+1 Armour per level.',
        MINE_CTRL: 'Choose the mine\'s target by hand instead of its automatic targeting. Includes IFF.',
        MINE_DMG: '+1 damage per level.',
        MINE_MULTI: 'Set a different range for each of the mine\'s weapons during Deployment.',
        MINE_RANG: '+1 Range per level.',
        MINE_SIGN: '+1 Signature per level.',

        //Fighter enhancements and options.
        ELITE_SW: 'Pivot cost reduced by one thrust, +5 initiative, +1 Offensive Bonus, -5% to be hit.',
        ELT_MAR: 'The marines of the flight\'s Breaching Pods are Elite.',
        EXT_MAR: 'One more marine unit per level in each Breaching Pod.',
        FTR_FERV: '+1 Offensive Bonus, +10 initiative and -3 on dropout rolls, but +10% to be hit. Take it when Desperate rules apply.',
        IMPR_OB: '+1 Offensive Bonus.',
        IMPR_THR: '+1 thrust.',
        MAKE_MINE: 'Offensive Bonus 4, and the shuttle uses its full Offensive Bonus for mine detection.',
        NAVIGATOR: '360-degree missile guidance and +5 initiative.',
        POOR_TRAIN: '-5 initiative, -1 thrust, -1 Offensive Bonus, +5% to be hit, +2 on dropout rolls.',
        RAM_PROW: 'One per craft, from the first craft on. +2 front armour; weapons fire straight ahead only, at -10%, and ballistic weapons cannot be used (missiles are bought only for the craft without one); ramming factor +50% when the craft rams or is rammed head-on. Only where Desperate rules apply.',
        SHAD_CTRL: 'Deployed without a carrier\'s control: -2 Offensive Bonus, -15 initiative.',
        VOR_AZURF: '+1 rating per level on every fighter shield.'
    },

    //The row's "?" text, or '' for none.
    textOf: function textOf(enhID) {
        var officer = window.officers ? officers.get(enhID) : null;
        if (officer) return officer.summary;
        var id = String(enhID).replace(/^SHELL_[HML](?=BSC|FLH|SCT|HVY|LR|ULR)/, 'SHELL_');
        return this.TEXT.hasOwnProperty(id) ? this.TEXT[id] : '';
    }
};
