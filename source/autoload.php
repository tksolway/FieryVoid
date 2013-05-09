<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'acceleratorprimus' => '/server/model/ships/centauri/AcceleratorPrimus.php',
                'advparticlebeam' => '/server/model/weapons/particle.php',
                'altarian' => '/server/model/ships/centauri/altarian.php',
                'altarianmagnus' => '/server/model/ships/centauri/altarianmagnus.php',
                'amar' => '/server/model/ships/centauri/amar.php',
                'ammo' => '/server/model/weapons/ammo.php',
                'antimatterconverter' => '/server/model/weapons/antimatter.php',
                'aoe' => '/server/model/weapons/AoE.php',
                'apollo' => '/server/model/ships/EA/apollo.php',
                'armorreduced' => '/server/model/cricialClasses.php',
                'artemisescort' => '/server/model/ships/EA/artemisEscort.php',
                'artemisbeta' => '/server/model/ships/EA/artemisBeta.php',
                'ashinta' => '/server/model/ships/minbari/ashinta.php',
                'assaultlaser' => '/server/model/weapons/laser.php',
                'advancedassaultlaser' => '/server/model/weapons/laser.php',
                'athasa' => '/server/model/ships/balosian/athasa.php',
                'aurorastarfury' => '/server/model/ships/EA/auroraStarfury.php',
                'ballistic' => '/server/model/BaseClasses.php',
                'ballistictorpedo' => '/server/model/weapons/torpedo.php',
                'balvarin' => '/server/model/ships/centauri/balvarin.php',
                'balvarix' => '/server/model/ships/centauri/balvarix.php',
                'baseship' => '/server/model/ships/ShipClasses.php',
                'basicmissile' => '/server/model/weapons/ammo.php',
                'battlelaser' => '/server/model/weapons/lasers.php',
                'bintak' => '/server/model/ships/narn/bintak.php',
                'brahassa' => '/server/model/ships/balosian/brahassa.php',
                'burstbeam' => '/server/model/weapons/specialWeapons.php',
                'centurion' => '/server/model/ships/centauri/centurion.php',
                'chatmanager' => '/server/controller/ChatManager.php',
                'chatmessage' => '/server/model/ChatMessage.php',
                'cnc' => '/server/model/systems/baseSystems.php',
                'communicationsdisrupted' => '/server/model/cricialClasses.php',
                'covran' => '/server/model/ships/centauri/Covran.php',
                'critical' => '/server/model/cricialClasses.php',
                'criticals' => '/server/handlers/criticals.php',
                'cronos' => '/server/model/ships/EA/cronos.php',
                'dagkar' => '/server/model/ships/narn/dagkar.php',
                'damageentry' => '/server/model/BaseClasses.php',
                'damagereductionremoved' => '/server/model/cricialClasses.php',
                'dargan' => '/server/model/ships/centauri/dargan.php',
                'darkner' => '/server/model/ships/centauri/darkner.php',
                'darmoti' => '/server/model/ships/centauri/darmoti.php',
                'dbmanager' => '/server/controller/DBManager.php',
                'debug' => '/server/lib/Debug.php',
                'defensivesystem' => '/server/model/systems/baseSystems.php',
                'delphi' => '/server/model/ships/EA/delphi.php',
                'demos' => '/server/model/ships/centauri/demos.php',
                'deployment' => '/server/handlers/Deployment.php',
                'dice' => '/server/lib/dice.php',
                'disengagedfighter' => '/server/model/cricialClasses.php',
                'dualweapon' => '/server/model/weapons/dualWeapon.php',
                'electropulsegun' => '/server/model/weapons/specialWeapons.php',
                'elintarray' => '/server/model/systems/baseSystems.php',
                'elintscanner' => '/server/model/systems/baseSystems.php',
                'elutarian' => '/server/model/ships/centauri/elutarian.php',
                'energymine' => '/server/model/weapons/AoE.php',
                'engine' => '/server/model/systems/baseSystems.php',
                'erlassan' => '/server/model/ships/balosian/erlassan.php',
                'esharan' => '/server/model/ships/minbari/esharan.php',
                'essusu' => '/server/model/ships/balosian/essusu.php',
                'esthasa' => '/server/model/ships/balosian/esthasa.php',
                'estnassa' => '/server/model/ships/balosian/estnassa.php',
                'ew' => '/server/handlers/EW.php',
                'ewentry' => '/server/model/BaseClasses.php',
                'fighter' => '/server/model/systems/fighter.php',
                'fighterflight' => '/server/model/ships/FighterFlight.php',
                'fireorder' => '/server/model/BaseClasses.php',
                'firing' => '/server/handlers/firing.php',
                'firstthrustignored' => '/server/model/cricialClasses.php',
                'forcedofflineoneturn' => '/server/model/cricialClasses.php',
                'frazi' => '/server/model/ships/narn/frazi.php',
                'fusioncannon' => '/server/model/weapons/molecular.php',
                'gatlingpulsecannon' => '/server/model/weapons/pulse.php',
                'glan' => '/server/model/ships/narn/glan.php',
                'gorith' => '/server/model/ships/narn/gorith.php',
                'gquan' => '/server/model/ships/narn/gquan.php',
                'gquonth' => '/server/model/ships/narn/gquonth.php',
                'graviticthruster' => '/server/model/systems/baseSystems.php',
                'greysharlin' => '/server/model/ships/minbari/greySharlin.php',
                'gsten' => '/server/model/ships/narn/gsten.php',
                'gstor' => '/server/model/ships/narn/gstor.php',
                'guardianarray' => '/server/model/weapons/defensive.php',
                'halfefficiency' => '/server/model/cricialClasses.php',
                'hangar' => '/server/model/systems/baseSystems.php',
                'haven' => '/server/model/ships/centauri/haven.php',
                'heavyarray' => '/server/model/weapons/particle.php',
                'heavycombatvessel' => '/server/model/ships/ShipClasses.php',
                'heavylaser' => '/server/model/weapons/lasers.php',
                'heavyplasma' => '/server/model/weapons/plasma.php',
                'heavypulse' => '/server/model/weapons/pulse.php',
                'heavyrailgun' => '/server/model/weapons/matter.php',
                'heavythentus' => '/server/model/ships/narn/HeavyThentus.php',
                'hyperion' => '/server/model/ships/EA/hyperion.php',
                'hyperionassault' => '/server/model/ships/EA/hyperionAssault.php',
                'hyperioncommand' => '/server/model/ships/EA/hyperionCommand.php',
                'hyperionmissile' => '/server/model/ships/EA/hyperionMissile.php',
                'hyperionpatrol' => '/server/model/ships/EA/hyperionPatrol.php',
                'hyperionpulse' => '/server/model/ships/EA/hyperionPulse.php',
                'hyperionrail' => '/server/model/ships/EA/hyperionRail.php',
                'hvyparticlecannon' => '/server/model/weapons/particle.php',
                'improvedneutronlaser' => '/server/model/weapons/lasers.php',
                'improvedioncannon' => '/server/model/weapons/ion.php',
                'intercept' => '/server/handlers/firing.php',
                'interceptcandidate' => '/server/handlers/firing.php',
                'interceptormki' => '/server/model/weapons/defensive.php',
                'interceptormkii' => '/server/model/weapons/defensive.php',
                'ionbolt' => '/server/model/weapons/ion.php',
                'ioncannon' => '/server/model/weapons/ion.php',
                'iontorpedo' => '/server/model/weapons/torpedo.php',
                'jammer' => '/server/model/systems/baseSystems.php',
                'jumpengine' => '/server/model/systems/baseSystems.php',
                'katoc' => '/server/model/ships/narn/Katoc.php',
                'katanpulsedestroyer' => '/server/model/ships/narn/KatanPulseDestroyer.php',
                'kraasus' => '/server/model/ships/balosian/kraasus.php',
                'kutai' => '/server/model/ships/centauri/kutai.php',
                'lahas' => '/server/model/ships/balosian/lahas.php',
                'laser' => '/server/model/weapons/lasers.php',
                'laserpulsearray' => '/server/model/weapons/dualWeapon.php',
                'leshath' => '/server/model/ships/minbari/leshath.php',
                'letann' => '/server/model/ships/minbari/letann.php',
                'lightfusioncannon' => '/server/model/weapons/molecular.php',
                'lightpulse' => '/server/model/weapons/pulse.php',
                'lightrailgun' => '/server/model/weapons/matter.php',
                'linkedweapon' => '/server/model/weapons/linkedWeapon.php',
                'lmissilerack' => '/server/model/weapons/missile.php',
                'lhmissilerack' => '/server/model/weapons/missile.php',
                'maggun' => '/server/model/weapons/plasma.php',
                'manager' => '/server/controller/Manager.php',
                'mathlib' => '/server/lib/mathlib.php',
                'matter' => '/server/model/weapons/matter.php',
                'mattercannon' => '/server/model/weapons/matter.php',
                'maximus' => '/server/model/ships/centauri/maximus.php',
                'mediumlaser' => '/server/model/weapons/lasers.php',
                'mediumplasma' => '/server/model/weapons/plasma.php',
                'mediumpulse' => '/server/model/weapons/pulse.php',
                'mediumship' => '/server/model/ships/ShipClasses.php',
                'missilelauncher' => '/server/model/weapons/missile.php',
                'mograth' => '/server/model/ships/centauri/mograth.php',
                'mogratti' => '/server/model/ships/centauri/mogratti.php',
                'molecular' => '/server/model/weapons/molecular.php',
                'moleculardisruptor' => '/server/model/weapons/molecular.php',
                'molecularpulsar' => '/server/model/weapons/pulse.php',
                'morshin' => '/server/model/ships/minbari/morshin.php',
                'movement' => '/server/handlers/movement.php',
                'movementorder' => '/server/model/BaseClasses.php',
                'neshatan' => '/server/model/ships/minbari/neshatan.php',
                'neutronlaser' => '/server/model/weapons/lasers.php',
                'nial' => '/server/model/ships/minbari/nial.php',
                'nova' => '/server/model/ships/EA/nova.php',
                'novaalpha' => '/server/model/ships/EA/novaAlpha.php',
                'octurion' => '/server/model/ships/centauri/octurion.php',
                'olympus' => '/server/model/ships/EA/olympus.php',
                'olympusbeta' => '/server/model/ships/EA/olympusBeta.php',
                'omega' => '/server/model/ships/EA/omega.php',
                'omegabeta' => '/server/model/ships/EA/omegaBeta.php',
                'oracle' => '/server/model/ships/EA/oracle.php',
                'oraclescout' => '/server/model/ships/EA/oracleScout.php',
                'outputreduced' => '/server/model/cricialClasses.php',
                'outputreduced1' => '/server/model/cricialClasses.php',
                'outputreduced10' => '/server/model/cricialClasses.php',
                'outputreduced2' => '/server/model/cricialClasses.php',
                'outputreduced3' => '/server/model/cricialClasses.php',
                'outputreduced4' => '/server/model/cricialClasses.php',
                'outputreduced6' => '/server/model/cricialClasses.php',
                'outputreduced8' => '/server/model/cricialClasses.php',
                'pairedparticlegun' => '/server/model/weapons/particle.php',
                'partialburnout' => '/server/model/cricialClasses.php',
                'particle' => '/server/model/weapons/particle.php',
                'particlecannon' => '/server/model/weapons/particle.php',
                'penaltytohit' => '/server/model/cricialClasses.php',
                'plasma' => '/server/model/weapons/plasma.php',
                'plasmaaccelerator' => '/server/model/weapons/plasma.php',
                'plasmastream' => '/server/model/weapons/specialWeapons.php',
                'playerslot' => '/server/model/BaseClasses.php',
                'playerslotfromjson' => '/server/model/BaseClasses.php',
                'powermanagemententry' => '/server/model/BaseClasses.php',
                'primus' => '/server/model/ships/centauri/primus.php',
                'primusmaximus' => '/server/model/ships/centauri/primusMaximus.php',
                'pulse' => '/server/model/weapons/pulse.php',
                'railgun' => '/server/model/weapons/matter.php',
                'raking' => '/server/model/weapons/lasers.php',
                'razik' => '/server/model/ships/centauri/razik.php',
                'reactor' => '/server/model/systems/baseSystems.php',
                'reduceddamage' => '/server/model/cricialClasses.php',
                'reducediniative' => '/server/model/cricialClasses.php',
                'reducediniativeoneturn' => '/server/model/cricialClasses.php',
                'reducedrange' => '/server/model/cricialClasses.php',
                'reloadrack' => '/server/model/weapons/missile.php',
                'resha' => '/server/model/ships/balosian/resha.php',
                'restrictedew' => '/server/model/cricialClasses.php',
                'rolentha' => '/server/model/ships/minbari/rolentha.php',
                'rongoth' => '/server/model/ships/narn/rongoth.php',
                'rutarian' => '/server/model/ships/centauri/rutarian.php',
                'sagittarius' => '/server/model/ships/EA/sagittarius.php',
                'scanner' => '/server/model/systems/baseSystems.php',
                'secundus' => '/server/model/ships/centauri/secundus.php',
                'seffensa' => '/server/model/ships/balosian/seffensa.php',
                'sensorsdisrupted' => '/server/model/cricialClasses.php',
                'sentri' => '/server/model/ships/centauri/sentri.php',
                'severeburnout' => '/server/model/cricialClasses.php',
                'shadras' => '/server/model/ships/balosian/shadras.php',
                'shantavi' => '/server/model/ships/minbari/shantavi.php',
                'sharaal' => '/server/model/ships/minbari/sharaal.php',
                'sharlin' => '/server/model/ships/minbari/sharlin.php',
                'shasi' => '/server/model/ships/balosian/shasi.php',
                'shaveenpatrolcutter' => '/server/model/ships/minbari/shaveenPatrolCutter.php',
                'shaveenpoliceleader' => '/server/model/ships/minbari/shaveenPoliceLeader.php',
                'shield' => '/server/model/systems/baseSystems.php',
                'shipdisabledoneturn' => '/server/model/cricialClasses.php',
                'shiploader' => '/server/controller/shipLoader.php',
                'shipsystem' => '/server/model/systems/ShipSystem.php',
                'shockcannon' => '/server/model/weapons/specialWeapons.php',
                'shokar' => '/server/model/ships/narn/shokar.php',
                'shokos' => '/server/model/ships/narn/shokos.php',
                'shokov' => '/server/model/ships/narn/shokov.php',
                'sitara' => '/server/model/ships/centauri/sitara.php',
                'smissilerack' => '/server/model/weapons/missile.php',
                'specialability' => '/server/model/systems/baseSystems.php',
                'stdparticlebeam' => '/server/model/weapons/particle.php',
                'stealth' => '/server/model/systems/baseSystems.php',
                'structure' => '/server/model/systems/baseSystems.php',
                'sulust' => '/server/model/ships/centauri/sulust.php',
                'sussha' => '/server/model/ships/balosian/sussha.php',
                'systemdata' => '/server/model/BaseClasses.php',
                'tacgamedata' => '/server/model/TacGamedata.php',
                'tethys' => '/server/model/ships/EA/tethys.php',
                'tethyslaser' => '/server/model/ships/EA/tethysLaser.php',
                'tethysmissile' => '/server/model/ships/EA/tethysMissile.php',
                'tethyspolice' => '/server/model/ships/EA/tethysPolice.php',
                'thentus' => '/server/model/ships/narn/thentus.php',
                'thosalsi' => '/server/model/ships/balosian/thosalsi.php',
                'thruster' => '/server/model/systems/baseSystems.php',
                'thunderboltstarfury' => '/server/model/ships/EA/thunderboltStarfury.php',
                'tigara' => '/server/model/ships/minbari/tigara.php',
                'tigarin' => '/server/model/ships/minbari/tigarin.php',
                'tinashi' => '/server/model/ships/minbari/tinashi.php',
                'tishat' => '/server/model/ships/minbari/tishat.php',
                'tloth' => '/server/model/ships/narn/tloth.php',
                'tnorr' => '/server/model/ships/narn/Tnorr.php',
                'torotha' => '/server/model/ships/minbari/torotha.php',
                'torpedo' => '/server/model/weapons/torpedo.php',
                'tractorbeam' => '/server/model/weapons/specialWeapons.php',
                'tradana' => '/server/model/ships/minbari/tradana.php',
                'trakk' => '/server/model/ships/narn/trakk.php',
                'trann' => '/server/model/ships/narn/Trann.php',
                'trolata' => '/server/model/ships/minbari/trolata.php',
                'twinarray' => '/server/model/weapons/particle.php',
                'vakar' => '/server/model/ships/narn/vakar.php',
                'varnic' => '/server/model/ships/narn/varnic.php',
                'vasachi' => '/server/model/ships/centauri/vasachi.php',
                'vorchan' => '/server/model/ships/centauri/vorchan.php',
                'vorchar' => '/server/model/ships/centauri/vorchar.php',
                'warlock' => '/server/model/ships/EA/warlock.php',
                'weapon' => '/server/model/weapons/weapon.php',
                'weaponloading' => '/server/model/BaseClasses.php',
                'whitestar' => '/server/model/ships/minbari/whitestar.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require __DIR__ . $classes[$cn];
        }
    }
);
// @codeCoverageIgnoreEnd