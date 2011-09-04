<?php
/*
    Copyright (C) 2004-2010 Kestas J. Kuliukas

	This file is part of webDiplomacy.

    webDiplomacy is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    webDiplomacy is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with webDiplomacy.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('IN_CODE') or die('This script can not be run by itself.');

/**
 * This class gives the static information for moderator tasks, and the code
 * which runs each task. The adminActionsForms class can use the static data
 * to present forms for each task, and correctly pass the submitted form data
 * to the right task code here.
 *
 * @package Admin
 */
class adminActions extends adminActionsForms
{
	public static $actions = array(
			'drawGame' => array(
				'name' => 'Draw game',
				'description' => 'Splits points among all the surviving players in a game equally, and ends the game.',
				'params' => array('gameID'=>'Game ID'),
			),
			'cancelGame' => array(
				'name' => 'Cancel game',
				'description' => 'Splits points among all players in a game equally, and deletes the game.',
				'params' => array('gameID'=>'Game ID'),
			),
			'togglePause' => array(
				'name' => 'Toggle-pause game',
				'description' => 'Flips a game\'s paused status; if it\'s paused it\'s unpaused, otherwise it\'s paused.',
				'params' => array('gameID'=>'Game ID'),
			),
			'makePublic' => array(
				'name' => 'Make public a private game',
				'description' => 'Removes a private game\'s password.',
				'params' => array('gameID'=>'Game ID'),
			),
			'makePrivate' => array(
				'name' => 'Make a public game private',
				'description' => 'Add a password to a private game.',
				'params' => array('gameID'=>'Game ID','password'=>'Password'),
			),
			'cdUser' => array(
				'name' => 'Force a user into CD',
				'description' => 'Force a user into CD in all his games, or in one game specifically if non-zero gameID given.',
				'params' => array('userID'=>'User ID','gameID'=>'Game ID'),
			),
			'banIP' => array(
				'name' => 'Ban an IP',
				'description' => 'Bans a certain IP address',
				'params' => array('IP'=>'IP address (xxx.xxx.xxx.xxx)'),
			),
			'banUser' => array(
				'name' => 'Ban a user',
				'description' => 'Bans a user, setting his games to civil disorder, and removing his points.',
				'params' => array('userID'=>'User ID','reason'=>'Reason'),
			),
			'unbanUser' => array(
				'name' => 'Unban a user',
				'description' => 'Unbans a user; does not return the player from civil disorder or return the points taken.',
				'params' => array('userID'=>'Banned User ID'),
			),
			'givePoints' => array(
				'name' => 'Give or take points',
				'description' => 'Enter a positive number of points to give, or a negative number of points to take.',
				'params' => array('userID'=>'User ID', 'points'=>'Points')
			),
			'resetPass' => array(
				'name' => 'Reset password',
				'description' => 'Resets a users password',
				'params' => array('userID'=>'User ID'),
			),
			'makeDonator' => array(
				'name' => 'Give donator benefits',
				'description' => 'Give donator benefits (in practical terms this just means opt-out of the distributed processing)',
				'params' => array('userID'=>'User ID'),
			),
			'makeDonatorPlatinum' => array(
				'name' => 'Donator: platinum',
				'description' => 'Give platinum donator marker',
				'params' => array('userID'=>'User ID'),
			),
			'makeDonatorGold' => array(
				'name' => 'Donator: gold',
				'description' => 'Give gold donator marker',
				'params' => array('userID'=>'User ID'),
			),
			'makeDonatorSilver' => array(
				'name' => 'Donator: silver',
				'description' => 'Give silver donator marker',
				'params' => array('userID'=>'User ID'),
			),
			'makeDonatorBronze' => array(
				'name' => 'Donator: bronze',
				'description' => 'Give bronze donator marker',
				'params' => array('userID'=>'User ID'),
			),
			'setProcessTimeToPhase' => array(
				'name' => 'Reset process time',
				'description' => 'Set a game process time to now + the phase length, resetting the turn length',
				'params' => array('gameID'=>'Game ID'),
			),
			'setProcessTimeToNow' => array(
				'name' => 'Process game now',
				'description' => 'Set a game process time to now, resulting in it being processed now',
				'params' => array('gameID'=>'Game ID'),
			),
			'panic' => array(
				'name' => 'Toggle panic button',
				'description' => 'Toggle the panic button; turning it on prevents games from being processed, users joining games,
						users registering. It is intended to limit the damage a problem can do',
				'params' => array(),
			),
			'resetLastProcessTime' => array(
				'name' => 'Reset last process time',
				'description' => 'Once the reason for the period of no processing is known, and it\'s safe to reenable
					game processing, the last process time can be reset here',
				'params' => array(),
			),
			'changePhaseLength' => array(
				'name' => 'Change phase length',
				'description' => 'Change the maximum number of minutes that a phase lasts.
					The time must be given in minutes (5 minutes to 10 days = 5-14400).<br />
					Also the next process time is reset to the new phase length.',
				'params' => array('gameID'=>'Game ID','phaseMinutes'=>'Minutes per phase'),
			),
			'countryReallocate' => array(
				'name' => 'Reallocate countries',
				'description' => 'Alter which player has which country. Enter a list like so:
					"<em>R,T,A,G,U,F,E</em>".<br />
					The result will be that England will be set to Russia, France to Turkey, etc.<br /><br />
					If you aren\'t sure about the order of each country just enter the gameID without anything else and the list of
					countries in the order will be output.<br /><br />
					To prevent people sharing invalid info before the countries have been reallocated only no-message games
					can have their countries reallocated; messages should be enabled only after the countries have been reallocated.<br /><br />
					(The substitution string to reverse the reallocation will be generated, in case you need to reverse the reallocation.)<br />
					(If changing the countries of a variant for which the first letter of the countries are not distinct countryID numbers must be used instead.)<br />
					(Alternatively you can enter [userID1]=[countryLetter1],[userID2]=[countryLetter2],etc)',
				'params' => array(
					'gameID'=>'Game ID',
					'reallocations'=>'Reallocations list (e.g "<em>R,T,A,G,U,F,E</em>")'
					)
			),
			'alterMessaging' => array(
				'name' => 'Alter game messaging',
				'description' => 'Change a game\'s messaging settings, e.g. to convert from gunboat to public-only or all messages allowed.',
				'params' => array(
					'gameID'=>'Game ID',
					'newSetting'=>'Enter a number for the desired setting: 1=Regular, 2=PublicPressOnly, 3=NoPress'
					),
			),
			'unCrashGames' => array(
				'name' => 'Uncrash games',
				'description' => 'Uncrashes all crashed games except the games specified (if any).',
				'params' => array('excludeGameIDs'=>'Except Game ID list'),
			),
			'reportMuteToggle' => array(
				'name' => 'Toggle mod-report mute',
				'description' => 'Toggles whether the given userID can submit reports, to prevent annoying users from abusing the report feature.',
				'params' => array('userID'=>'User ID'),
			)
		);

	public function __construct()
	{
		global $Misc;
	}

	public function unCrashGames(array $params)
	{
		global $DB;

		require_once('gamemaster/game.php');

		$excludeGameIDs = explode(',', $params['excludeGameIDs']);

		foreach($excludeGameIDs as $index=>$gameID)
		{
			$gameID = (int)$gameID;
			$excludeGameIDs[$index] = $gameID;
		}
		$excludeGameIDs = implode(',', $excludeGameIDs);

		$tabl = $DB->sql_tabl(
			"SELECT * FROM wD_Games WHERE processStatus = 'Crashed' ".( $excludeGameIDs ? "AND id NOT IN (".$excludeGameIDs.")" : "" )." FOR UPDATE"
		);
		$count=0;
		while($row=$DB->tabl_hash($tabl))
		{
			$count++;

			$Variant=libVariant::loadFromVariantID($row['variantID']);
			$Game = $Variant->processGame($row);

			if( $Game->phase == 'Finished' )
			{
				$DB->sql_put("UPDATE wD_Games SET processStatus = 'Not-processing', pauseTimeRemaining=NULL, processTime = ".time()." WHERE id = ".$Game->id);
				continue;
			}

			if ( $Game->phaseMinutes < 12*60 )
			{
				if( $Game->phase == 'Pre-game' )
				{
					$newTimeDetails = "";
					$DB->sql_put("UPDATE wD_Games SET processStatus = IF(pauseTimeRemaining IS NULL,'Not-processing','Paused') WHERE id = ".$Game->id);
				}
				else
				{
					$newTimeDetails = ", and the game has been paused, since it's a fast game, to give players a chance to regroup";
					$Game->processStatus = 'Not-processing';
					$Game->togglePause();
				}
			}
			else
			{
				$newTimeDetails = ", and the game's next process time has been reset";
				$DB->sql_put("UPDATE wD_Games SET processStatus = 'Not-processing', processTime = ".time()." + 60*phaseMinutes, pauseTimeRemaining=NULL WHERE id = ".$Game->id);
			}

			$Game->Members->send('No',"This game has been uncrashed".$newTimeDetails.". Thanks for your patience.");
		}

		/* Simpler, but doesn't accomodate live games well
		$DB->sql_put(
			"UPDATE wD_Games SET processStatus = 'Not-processing', processTime = ".time()." + 60*phaseMinutes
			WHERE AND processStatus = 'Crashed' ".( $excludeGameIDs ? "AND id NOT IN (".$excludeGameIDs.")" : "" )
		);*/

		$details = 'All crashed games were un-crashed';
		if ( $excludeGameIDs )
			$details .= ', except: '.$excludeGameIDs;
		$details .= '. '.$count.' games in total.';

		return $details;
	}

	public function countryReallocate(array $params)
	{
		global $DB;

		$gameID=(int)$params['gameID'];

		$Variant=libVariant::loadFromGameID($gameID);
		$Game = $Variant->Game($gameID);

		if( strlen($params['reallocations'])==0 )
		{
			$c=array();
			foreach($Variant->countries as $index=>$country)
			{
				$index++;
				$countryLetter=strtoupper(substr($country,0,1));
				$c[$countryLetter] = '#'.$index.": ".$country;
			}
			$ids=array_keys($c);

			return implode('<br />',$c)."<br />e.g. \"".implode(',',$ids)."\" would change nothing";
		}

		$reallocations=explode(',',$params['reallocations']);

		if ( $Game->pressType != 'NoPress' )
			throw new Exception("Only games with no messages allowed can have their countries reordered,
				otherwise information may already have been communicated while believing countries were already allocated.");

		if ( $Game->phase == 'Pre-game' )
			throw new Exception("This game hasn't yet started; countries can only be reallocated after they have been allocated already.");

		if ( $Game->phase == 'Finished' )
			throw new Exception("This game has finished, countries can't be reallocated.");

		if( count($reallocations) != count($Variant->countries) )
			throw new Exception("The number of inputted reallocations (".count($reallocations).") aren't equal to the number of countries (".count($Variant->countries).").");

		if( !is_numeric(implode('', $reallocations)) )
		{
			$countryIDsByLetter=array();
			foreach($Variant->countries as $countryID=>$countryName)
			{
				$countryID++;
				$countryLetter=strtoupper(substr($countryName,0,1));
				if( isset($countryIDsByLetter[$countryLetter]) )
					throw new Exception("For the given variant two countries have the same start letter: '".$countryLetter." (one is '".$countryName."'), you must give countryIDs instead of letters.");

				$countryIDsByLetter[$countryLetter]=$countryID;
			}

			if( count(explode('=',$reallocations[0]))==2 )
			{
				$newCountryIDsByOldCountryID=array();
				foreach($reallocations as $r)
				{
					list($userID,$countryLetter)=explode('=', $r);
					$countryID=$countryIDsByLetter[$countryLetter];

					$oldCountryID=false;
					list($oldCountryID)=$DB->sql_row("SELECT countryID FROM wD_Members WHERE userID=".$userID." AND gameID = ".$Game->id);
					if( !$oldCountryID )
						throw new Exception("User ".$userID." not found in this game.");

					$newCountryIDsByOldCountryID[$oldCountryID]=$countryID;
				}
			}
			else
			{
				$newCountryIDsByOldCountryID=array();
				for($oldCountryID=1; $oldCountryID<=count($reallocations); $oldCountryID++)
				{
					$countryLetter=$reallocations[$oldCountryID-1];

					if( !isset($countryIDsByLetter[$countryLetter]) )
						throw new Exception("No country name starts with letter '".$countryLetter."'");

					$newCountryIDsByOldCountryID[$oldCountryID]=$countryIDsByLetter[$countryLetter];
				}
			}
		}
		else
		{
			$newCountryIDsByOldCountryID=array();
			for($oldCountryID=1; $oldCountryID<=count($reallocations); $oldCountryID++)
			{
				$newCountryID=$reallocations[$oldCountryID-1];
				$newCountryIDsByOldCountryID[$oldCountryID]=(int)$newCountryID;
			}
		}

		$changes=array();
		$newUserIDByNewCountryID=array();
		$changeBack=array();
		foreach($newCountryIDsByOldCountryID as $oldCountryID=>$newCountryID)
		{
			list($userID)=$DB->sql_row("SELECT userID FROM wD_Members WHERE gameID=".$Game->id." AND countryID=".$oldCountryID." FOR UPDATE");
			$newUserIDByNewCountryID[$newCountryID]=$userID;

			$changes[] = "Changed ".$Variant->countries[$oldCountryID-1]." (#".$oldCountryID.") ".
				"to ".$Variant->countries[$newCountryID-1]." (#".$newCountryID.").";
			$changeBack[$newCountryID]=$oldCountryID;
		}

		$changeBackStr=array();
		for($i=1; $i<=count($Variant->countries); $i++)
			$changeBackStr[] = $changeBack[$i];
		$changeBackStr=implode(',', $changeBackStr);

		// Foreach member set the new owners' userID
		// The member isn't given a new countryID, instead the user in control of the countryID is moved into the other countryID:
		// userID is what gets changed, not countryID (if it's not done this way all sorts of problems e.g. supplyCenterNo crop up)
		$DB->sql_put("BEGIN");
		foreach($newUserIDByNewCountryID as $newCountryID=>$userID)
			$DB->sql_put("UPDATE wD_Members SET userID=".$userID." WHERE gameID=".$Game->id." AND countryID=".$newCountryID);
		$DB->sql_put("COMMIT");

		return 'In this game these countries were successfully swapped:<br />'.implode(',<br />', $changes).'.<br />
			These changes can be reversed with "'.$changeBackStr.'"';
	}

	public function reportMuteToggle(array $params) {
		global $DB;

		$userID=(int)$params['userID'];
		$DB->sql_put("UPDATE wD_Users SET muteReports=IF(muteReports='Yes','No','Yes') WHERE id=".$userID);
		return "User's reporting ability has been toggled.";
	}
	public function alterMessaging(array $params)
	{
		global $DB;

		$gameID=(int)$params['gameID'];
		$newSetting=(int)$params['newSetting'];

		switch($newSetting)
		{
			case 1: $newSettingName='Regular'; break;
			case 2: $newSettingName='PublicPressOnly'; break;
			case 3: $newSettingName='NoPress'; break;
			default: throw new Exception("Invalid messaging setting; enter 1, 2, or 3.");
		}

		$DB->sql_put("UPDATE wD_Games SET pressType = '".$newSettingName."' WHERE id = ".$gameID);

		return 'Game changed to pressType='.$newSettingName.'.';
	}

	public function changePhaseLength(array $params)
	{
		global $DB;

		require_once('objects/game.php');

		$newPhaseMinutes = (int)$params['phaseMinutes'];

		if ( $newPhaseMinutes < 5 || $newPhaseMinutes > 10*24*60 )
			throw new Exception("Given phase minutes out of bounds (5 minutes to 10 days)");

		$Variant=libVariant::loadFromGameID($params['gameID']);
		$Game = $Variant->Game($params['gameID']);

		if( $Game->processStatus != 'Not-processing' || $Game->phase == 'Finished' )
			throw new Exception("Game is either crashed/paused/finished/processing, and so the next-process time cannot be altered.");

		$oldPhaseMinutes = $Game->phaseMinutes;

		$Game->phaseMinutes = $newPhaseMinutes;
		$Game->processTime = time()+($newPhaseMinutes*60);

		$DB->sql_put("UPDATE wD_Games SET
			phaseMinutes = ".$Game->phaseMinutes.",
			processTime = ".$Game->processTime."
			WHERE id = ".$Game->id);

		return 'Process time changed from '.libTime::timeLengthText($oldPhaseMinutes*60).
			' to '.libTime::timeLengthText($Game->phaseMinutes*60).'.
			Next process time is '.libTime::text($Game->processTime).'.';
	}

	public function resetLastProcessTime(array $params)
	{
		global $Misc;

		$Misc->LastProcessTime = time();
		$Misc->write();

		return 'Last process time reset';
	}

	public function setProcessTimeToPhaseConfirm(array $params)
	{
		global $DB;

		require_once('objects/game.php');

		$gameID = intval($params['gameID']);

		$Variant=libVariant::loadFromGameID($gameID);
		$Game = $Variant->Game($gameID);

		return 'Are you sure you want to reset the phase process time of this game
			 to process in '.($Game->phaseMinutes/60).' hours?';
	}
	public function setProcessTimeToPhase(array $params)
	{
		global $DB;

		$gameID = intval($params['gameID']);

		$Variant=libVariant::loadFromGameID($gameID);
		$Game = $Variant->Game($gameID);

		if( $Game->processStatus != 'Not-processing' || $Game->phase == 'Finished' )
			return 'This game is paused/crashed/finished.';

		$DB->sql_put(
			"UPDATE wD_Games
			SET processTime = ".time()." + phaseMinutes * 60
			WHERE id = ".$Game->id
		);

		return 'Process time reset successfully';
	}

	public function setProcessTimeToNowConfirm(array $params)
	{
		global $DB;

		require_once('objects/game.php');

		$Variant=libVariant::loadFromGameID($params['gameID']);
		$Game = $Variant->Game($params['gameID']);

		return 'Are you sure you want to start processing this game now?';
	}
	public function makePublic(array $params)
	{
		global $DB;

		$gameID = intval($params['gameID']);

		$DB->sql_put(
			"UPDATE wD_Games
			SET password = NULL
			WHERE id = ".$gameID
		);

		return 'Password removed';
	}
	public function makePrivate(array $params)
	{
		global $DB;

		$gameID = intval($params['gameID']);
		$password=$params['password'];

		$DB->sql_put(
			"UPDATE wD_Games
			SET password = UNHEX('".md5($password)."')
			WHERE id = ".$gameID
		);

		return 'Password set to "'.$password.'"';
	}
	public function setProcessTimeToNow(array $params)
	{
		global $DB;

		$gameID = intval($params['gameID']);

		$Variant=libVariant::loadFromGameID($gameID);
		$Game = $Variant->Game($gameID);

		if( $Game->processStatus != 'Not-processing' || $Game->phase == 'Finished' )
			return 'This game is paused/crashed/finished.';

		$DB->sql_put(
			"UPDATE wD_Games
			SET processTime = ".time()."
			WHERE id = ".$Game->id
		);

		return 'Process time set to now successfully';
	}

	public function panicConfirm(array $params)
	{
		return 'Are you sure? This should only be used if something is damaging the server';
	}
	public function panic(array $params)
	{
		global $Misc;

		$Misc->Panic = 1-$Misc->Panic;
		$Misc->write();

		return 'Panic button '.($Misc->Panic?'turned on':'turned off');
	}

	private function makeDonatorType(array $params, $type='') {
		global $DB;

		$userID = (int)$params['userID'];

		$DB->sql_put("UPDATE wD_Users SET type = CONCAT_WS(',',type,'Donator".$type."') WHERE id = ".$userID);

		return 'User ID '.$userID.' given donator status.';
	}

	public function makeDonator(array $params)
	{
		return $this->makeDonatorType($params);
	}
	public function makeDonatorPlatinum(array $params)
	{
		return $this->makeDonatorType($params,'Platinum');
	}
	public function makeDonatorGold(array $params)
	{
		return $this->makeDonatorType($params,'Gold');
	}
	public function makeDonatorSilver(array $params)
	{
		return $this->makeDonatorType($params,'Silver');
	}
	public function makeDonatorBronze(array $params)
	{
		return $this->makeDonatorType($params,'Bronze');
	}

	public function resetPassConfirm(array $params)
	{
		global $DB;

		$User= new User($params['userID']);

		return 'Are you sure you want to reset the password of this user?';
	}
	public function resetPass(array $params)
	{
		global $DB,$User;

		$ChangeUser= new User($params['userID']);
		if( $ChangeUser->type['Admin'] || ( $ChangeUser->type['Moderator'] && !$User->type['Admin'] ) )
		{
			throw new Exception("Cannot reset an admin/moderator's password if you aren't admin.");
		}

		$password = base64_encode(rand(1000000,2000000));

		$DB->sql_put(
			"UPDATE wD_Users
			SET password = UNHEX('".libAuth::pass_Hash($password)."')
			WHERE id = ".$ChangeUser->id
		);

		return 'Users password reset to '.$password;
	}


	public function drawGameConfirm(array $params)
	{
		global $DB;

		require_once('objects/game.php');

		$Variant=libVariant::loadFromGameID($params['gameID']);
		$Game = $Variant->Game($params['gameID']);

		return 'Are you sure you want to draw this game?
			This is really hard to undo, so be sure this is correct!';
	}
	public function drawGame(array $params)
	{
		global $DB, $Game;

		$gameID = (int)$params['gameID'];

		$DB->sql_put("BEGIN");

		require_once('gamemaster/game.php');
		$Variant=libVariant::loadFromGameID($gameID);
		$Game = $Variant->processGame($gameID);

		if( $Game->phase != 'Diplomacy' and $Game->phase != 'Retreats' and $Game->phase != 'Builds' )
		{
			throw new Exception('This game is in phase '.$Game->phase.", so it can't be drawn.", 987);
		}

		$Game->setDrawn();

		$DB->sql_put("COMMIT");

		return 'The game was drawn.';
	}

	public function cancelGameConfirm(array $params)
	{
		global $DB;

		require_once('objects/game.php');

		$Variant=libVariant::loadFromGameID($params['gameID']);
		$Game = $Variant->Game($params['gameID']);

		return 'Are you sure you want to cancel this game?
			This is really hard to undo, so be sure this is correct!';
	}
	public function cancelGame(array $params)
	{
		global $DB, $Game;

		$gameID = (int)$params['gameID'];

		$DB->sql_put("BEGIN");

		require_once('gamemaster/game.php');
		$Variant=libVariant::loadFromGameID($gameID);
		$Game = $Variant->processGame($gameID);

		if( $Game->phase != 'Diplomacy' and $Game->phase != 'Retreats' and $Game->phase != 'Builds' )
		{
			throw new Exception('This game is in phase '.$Game->phase.", so it can't be cancelled.", 987);
		}

		$Game->setCancelled();

		$DB->sql_put("COMMIT");

		return 'This game was cancelled.';
	}
	public function togglePause(array $params)
	{
		global $DB, $Game;

		$gameID = (int)$params['gameID'];

		$DB->sql_put("BEGIN");

		require_once('gamemaster/game.php');
		$Variant=libVariant::loadFromGameID($gameID);
		$Game = $Variant->processGame($gameID);

		$Game->togglePause();

		$DB->sql_put("COMMIT");

		return 'This game is now '.( $Game->processStatus == 'Paused' ? 'paused':'unpaused').'.';
	}

	public function cdUserConfirm(array $params)
	{
		global $DB;

		require_once('objects/game.php');

		$User = new User($params['userID']);
		if ( isset($params['gameID']) && $params['gameID'] )
		{
			$Variant=libVariant::loadFromGameID($params['gameID']);
			$Game = $Variant->Game($params['gameID']);
		}

		return 'Are you sure you want to put this user into civil-disorder'.
			(isset($Game)?', in this game':', in all his games').'?';
	}
	public function cdUser(array $params)
	{
		global $DB;

		require_once('gamemaster/game.php');

		$User = new User($params['userID']);
		if ( isset($params['gameID']) && $params['gameID'] )
		{
			$Variant=libVariant::loadFromGameID($params['gameID']);
			$Game = $Variant->processGame($params['gameID']);

			if( $Game->phase == 'Pre-game' || $Game->phase == 'Finished' )
				throw new Exception("Invalid phase to set CD");

			$Game->Members->ByUserID[$User->id]->setLeft();
		}
		else
		{
			$tabl = $DB->sql_tabl("SELECT gameID, status FROM wD_Members
						WHERE userID = ".$userID);
			while( list($gameID, $status) = $DB->tabl_row($tabl) )
			{
				if ( $status != 'Playing' ) continue;

				$Variant=libVariant::loadFromGameID($gameID);
				$Game = $Variant->processGame($gameID);
				$Game->Members->ByUserID[$User->id]->setLeft();
			}
		}

		return 'This user put into civil-disorder'.
			((isset($params['gameID']) && $params['gameID'])?', in this game':', in all his games').'?';
	}

	public function banUserConfirm(array $params)
	{
		global $DB;

		$User = new User($params['userID']);

		if( !isset($params['reason']) || strlen($params['reason'])==0 )
			return 'Couldn\'t ban user; no reason was given.';

		return 'Are you sure you want to ban '.$User->username.' (Reason: "'.$DB->msg_escape($params['reason']).'")?
			Restoring mistakenly removed points takes a long time, so be sure this is correct!';
	}
	public function banIP(array $params)
	{
		User::banIP(ip2long($ip));
	}
	public function banUser(array $params)
	{
		global $User, $DB, $Game;

		$userID = (int)$params['userID'];

		if( !isset($params['reason']) || strlen($params['reason'])==0 )
			return 'Couldn\'t ban user; no reason was given.';

		$banReason = $DB->msg_escape($params['reason']);

		$banUser = new User($userID);

		if( $banUser->type['Banned'] )
			throw new Exception("The user is already banned");

		if( $banUser->type['Admin'] )
			throw new Exception("Admins can't be banned");

		if( $banUser->type['Moderator'] and ! $User->type['Admin'] )
			throw new Exception("Moderators can't be banned by non-admins");

		User::banUser($userID, "Banned by a moderator: ".$params['reason']);

		require_once('gamemaster/game.php');

		/*
		 * Explain what has happened to the games the banned user was in, and extend the
		 * turn
		 */
		$tabl = $DB->sql_tabl("SELECT gameID, status FROM wD_Members
					WHERE userID = ".$userID);
		while( list($gameID, $status) = $DB->tabl_row($tabl) )
		{
			if ( $status != 'Playing' ) continue;

			$Variant=libVariant::loadFromGameID($gameID);
			$Game = $Variant->Game($gameID);

			$banMessage = $banUser->username.' was banned: '.$banReason.'. ';

			if( $Game->phase == 'Pre-game' )
			{
				if(count($Game->Members->ByID)==1)
					processGame::eraseGame($Game->id);
				else
					$DB->sql_put("DELETE FROM wD_Members WHERE gameID = ".$Game->id." AND userID = ".$userID);
			}
			elseif( $Game->processStatus != 'Paused' and $Game->phase != 'Finished' )
			{
				// The game may need a time extension to allow for a new player to be added

				// Would the time extension would give a difference of more than ten minutes? If not don't bother
				if ( (time() + $Game->phaseMinutes*60) - $Game->processTime > 10*60 ) {

					// It is worth adding an extension
					$DB->sql_put(
						"UPDATE wD_Games
						SET processTime = ".time()." + phaseMinutes*60
						WHERE id = ".$Game->id
					);
					$Game->processTime = time() + $Game->phaseMinutes*60;

					$banMessage .= 'The time until the next phase has been extended by one phase length '.
						'to give an opportunity to replace the player.'."\n".
						'Remember to finalize your orders if you don\'t want '.
						'to wait, so the game isn\'t held up unnecessarily!';
				}
			}

			libGameMessage::send('Global','GameMaster', $banMessage);

			$Game->Members->sendToPlaying('No', $banUser->username.' was banned, see in-game for details.');
		}

		$DB->sql_put("UPDATE wD_Members SET status = 'Left', orderStatus=CONCAT(orderStatus,',Ready')
					WHERE userID = ".$userID." AND status = 'Playing'");
		$DB->sql_put("UPDATE wD_Orders o INNER JOIN wD_Members m ON ( m.gameID = o.gameID AND m.countryID = o.countryID )
					SET o.toTerrID = NULL, o.fromTerrID = NULL
					WHERE m.userID = ".$userID);

		unset($Game);

		return 'This user was banned, and had their '.
			$banUser->points.' points removed and their games set to civil disorder.';
	}
	public function givePoints(array $params)
	{
		global $DB;

		$userID = (int)$params['userID'];
		$points = (int)$params['points'];

		$giveUser = new User($userID);

		if( $points > 0 )
			User::pointsTransfer($giveUser->id, 'Supplement', $points);
		else
		{
			$points = (-1)*$points;
			$DB->sql_put("UPDATE wD_Users SET points = points - ".$points." WHERE id=".$userID);
		}

		return 'This user was transferred '.$points.' '.libHTML::points().'.';
	}
	public function unbanUser(array $params)
	{
		global $DB;

		$userID = (int)$params['userID'];

		$unbanUser = new User($userID);

		if( ! $unbanUser->type['Banned'] )
			throw new Exception("Can't unban a user which isn't banned");

		$DB->sql_put(
			"UPDATE wD_Users SET type = 'User' WHERE id = ".$userID
		);

		return 'This user was unbanned.';
	}

	public function setCivilDisorderConfirm(array $params)
	{
		global $DB;

		$User = new User($params['userID']);

		require_once('objects/game.php');
		$Variant=libVariant::loadFromGameID($params['gameID']);
		$Game = $Variant->Game($params['gameID']);

		return 'Are you sure you want to set this user to civil disorder in this game?';
	}
	public function setCivilDisorder(array $params)
	{
		global $DB;

		$User = new User($params['userID']);

		require_once('gamemaster/game.php');
		$Variant=libVariant::loadFromGameID($params['gameID']);
		$Game = $Variant->processGame($params['gameID']);

		foreach($Game->Members->ByID as $Member)
		{
			if ( $User->id != $Member->userID ) continue;

			if ( $Member->status == 'Playing' )
			{
				$DB->sql_put("UPDATE wD_Members SET status = 'Left' WHERE id=".$Member->id);
				$DB->sql_put("INSERT INTO wD_CivilDisorders ( gameID, userID, countryID, turn, bet, SCCount )
					VALUES ( ".$Game->id.", ".$User->id.", ".$Member->countryID.", ".$Game->turn.", ".$Member->bet.", ".$Member->SCCount.")");

				return 'User set to civil disorder in game';
			}
		}

		throw new Exception("User not in specified game");
	}
}

?>