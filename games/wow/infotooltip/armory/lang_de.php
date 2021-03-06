<?php
/*	Project:	EQdkp-Plus
 *	Package:	World of Warcraft game package
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$lang = array(
	'itemBind' => array(
		1 => 'Wird beim Aufheben gebunden',
		2 => 'Wird beim Anlegen gebunden',
		3 => 'Wird beim Benutzen gebunden',
		4 => 'Battle.net-Accountgebunden'
	),
	'itemClass'	=> array(
		0	=> array('Verbrauchbar', 'Trank', 'Elixier', 'Fläschchen', 'Rolle', 'Essen & Trinken', 'Gegenstandsverzauberung', 'Verband', 'Anderes'),
		1	=> array('Tasche', 'Seelentasche', 'Kräutertasche', 'Verzauberertasche', 'Ingenieuerstasche', 'Edelsteintasche', 'Bergbautasche', 'Lederertasche', 'Inschriftentasche', 'Tackle Box'),
		2	=> array('Axt', 'Axt', 'Bogen', 'Schusswaffe', 'Streitkolben', 'Streitkolben', 'Stangenwaffe', 'Schwert', 'Schwert', 10 => 'Stab', 13 => 'Faustwaffe', 14 => 'Verschiedenes', 15 => 'Dolch', 16 => 'Wurfwaffe', 18 => 'Armbrust', 19 => 'Zauberstab', 20 => 'Angel'),
		3	=> array('Roter Edelstein', 'Blauer Edelstein', 'Gelber Edelstein', 'Violetter Edelstein ', 'Grüner Edelstein', 'Oranger Edelstein ', 'Meta Edelstein', 'Einfacher Edelstein', 'Prismatischer Edelstein', 'Hydraulischer Edelstein', 'Zahnrad Edelstein'),
		4	=> array('Verschiedenes', 'Stoff', 'Leder', 'Kette', 'Platte', '', 'Schild'),
		7	=> array('Handelswaren', 'Teile', 'Explosives', 'Geräte', 'Juwelen schleifen', 'Stoff', 'Leder', 'Metall & Stein', 'Fleisch', 'Kräuter', 'Elementar', 'Anderes', 'Verzauberkunst', 'Materialien', 'Gegenstandsverbesserung'),
		9	=> array('Buch', 'Lederverarbeitung', 'Schneiderei', 'Ingenieurskunst', 'Schmiedekunst', 'Kochkunst', 'Alchemie', 'Erste Hilfe', 'Verzauberkunst', 'Angeln', 'Juwelenschleifen'),
		12	=> array('Questgegenstand'),
		13	=> array('Schlüssel'),
		15	=> array('Müll', 'Zutat', 'Haustier', 'Feiertag', 'Anderes', 'Reittier'),
		16	=> array('Glyphe', 'Krieger', 'Paladin', 'Jäger', 'Schurke', 'Priester', 'Todesritter', 'Shamane', 'Magier', 'Hexenmeister', 'Mönch', 'Druide')
	),
	'inventoryType'	=> array(
		1	=> 'Kopf',
		2	=> 'Hals',
		3	=> 'Schulter',
		4	=> 'Hemd',
		5	=> 'Brust',
		6	=> 'Gürtel',
		7	=> 'Beine',
		8	=> 'Füße',
		9	=> 'Handgelenke',
		10	=> 'Hände',
		11	=> 'Finger',
		12	=> 'Schmuck',
		13	=> 'Einhändig',
		14	=> 'Schild',
		15	=> 'Distanz',
		16	=> 'Rücken',
		17	=> 'Zweihändig',
		18	=> 'Tasche',
		20	=> 'Brust',
		21	=> 'Waffenhand',
		22	=> 'Schildhand',
		23	=> 'In Schildhand geführt',
		25	=> 'Distanz',
		26	=> 'Distanz'
	),
	'damage'		=> 'Schaden',
	'weaponSpeed'	=> 'Geschwindigkeit',
	'dps'			=> 'Schaden pro Sekunde',
	'armor'			=> 'Rüstung',
	'bonusStats'	=> array(
		3	=> 'Beweglichkeit',
		4	=> 'Stärke',
		5	=> 'Intelligenz',
		6	=> 'Willenskraft',
		7	=> 'Ausdauer',
		13	=> 'Ausweichwertung',
		14	=> 'Parierwertung',
		31	=> 'Trefferchance',
		32	=> 'kritische Trefferchance',
		35	=> 'PvP-Abhärtung',
		36	=> 'Tempowertung',
		37	=> 'Waffenkundewertung',
		38	=> 'Angriffskraft',
		45	=> 'Zaubermacht',
		46	=> 'Leben Regeneration',
		47	=> 'Zauberdurchschlagskraft',
		49	=> 'Meisterschaftswertung',
		57	=> 'PvP-Macht',
	),
	'secondary_stats'	=> '+%d %s',
	'socket'	=> array(
		'red'		=> 'Roter Sockel',
		'blue'		=> 'Blauer Sockel',
		'yellow'	=> 'Gelber Sockel',
		'meta'		=> 'Meta Sockel',
		'prismatic'	=> 'Prismatischer Sockel',
		'hydraulic'	=> 'Hydraulischer Sockel',
	'cogwheel'		=> 'Zahnradsockel'      
	),
	'socketBonus'	=> 'Sockelbonus',
	'maxDurability'	=> 'Haltbarkeit',
	'allowableClasses'	=> 'Klassen',
	'requiredLevel'	=> 'Erfordert Stufe',
	'requiredSkill'	=> 'Erfordert',
	'itemLevel'		=> 'Gegenstandsstufe',
	'trigger'		=> array(
		'ON_EQUIP'	=> 'Anlegen:',
		'ON_USE'	=> 'Benutzen:',
		'ON_PROC'	=> 'Trefferchance:',
		'UNKNOWN'	=> 'Unbekannt:'
	),
	'reforged'   => 'Umgeschmiedet',
	'reforgedFrom' => 'Umgeschmiedet aus',
	'enchanted' => 'Verzaubert',
	'upgraded' => "Aufwertungsgrad"
);
?>