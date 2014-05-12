<?php
/* zKillboard
 * Copyright (C) 2012-2013 EVE-KILL Team and EVSCO.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!User::isLoggedIn()) {
	$app->render("login.html");
	die();
}

$userID = User::getUserID();
$key = "me";
$error = "";

$bannerUpdates = array();

if(isset($req))
	$key = $req;

if($_POST)
{
	// Check for adfree purchase
	$purchase = Util::getPost("purchase");
	if ($purchase)
	{
		global $twig;
		if ($purchase == "donate")
		{
			$amount = User::getBalance($userID);
			if ($amount > 0) {
				Db::execute("insert into zz_account_history (userID, purchase, amount) values (:userID, :purchase, :amount)",
					array(":userID" => $userID, ":purchase" => "donation", ":amount" => $amount));
				Db::execute("update zz_account_balance set balance = 0 where userID = :userID", array(":userID" => $userID));
				$twig->addGlobal("accountBalance", User::getBalance($userID));
				$error = "Thank you VERY much for your donation!";
			} else $error = "Gee, thanks for nothing...";
		}
		else
		{
			global $adFreeMonthCost;

			$months = str_replace("buy", "", $purchase);
			if ($months > 12 || $months < 0) $months = 1;
			$balance = User::getBalance($userID);
			$amount = $adFreeMonthCost * $months;
			$bonus = floor($months / 6);
			$months += $bonus;
			if ($balance >= $amount)
			{
				$dttm = UserConfig::get("adFreeUntil", null);
				$now = $dttm == null ? " now() " : "'$dttm'";
				$newDTTM = Db::queryField("select date_add($now, interval $months month) as dttm", "dttm", array(), 0);
				Db::execute("update zz_account_balance set balance = balance - :amount where userID = :userID",
						array(":userID" => $userID, ":amount" => $amount));
				Db::execute("insert into zz_account_history (userID, purchase, amount) values (:userID, :purchase, :amount)",
						array(":userID" => $userID, ":purchase" => $purchase, ":amount" => $amount));
				UserConfig::set("adFreeUntil", $newDTTM);

				$twig->addGlobal("accountBalance", User::getBalance($userID));
				$error = "Funds have been applied for $months month" . ($months == 1 ? "" : "s") . ", you are now ad free until $newDTTM";
				Log::log("Ad free time purchased by user $userID for $months months with " . number_format($amount) . " ISK");
			} else $error = "Insufficient Funds... Nice try though....";
		}
	}


	$keyid = Util::getPost("keyid");
	$vcode = Util::getPost("vcode");
	$label = Util::getPost("label");
	// Apikey stuff
	if(isset($keyid) || isset($vcode))
	{
		$check = Api::checkAPI($keyid, $vcode);
		if($check == "success")
		{
			$error = Api::addKey($keyid, $vcode, $label);
		}
		else
		{
			$error = $check;
		}
	}

	$deletesessionid = Util::getPost("deletesessionid");
	// delete a session
	if(isset($deletesessionid))
		User::deleteSession($userID, $deletesessionid);

	$deletekeyid = Util::getPost("deletekeyid");
	$deleteentity = Util::getPost("deleteentity");
	// Delete an apikey
	if(isset($deletekeyid) && !isset($deleteentity))
		$error = Api::deleteKey($deletekeyid);

	$viewtheme = Util::getPost("viewtheme");
	// Theme stuff
	if(isset($viewtheme))
	{
		UserConfig::set("viewtheme", $viewtheme);
		$app->redirect($_SERVER["REQUEST_URI"]);
	}

	$theme = Util::getPost("theme");
	if(isset($theme))
		UserConfig::set("theme", $theme);

	$orgpw = Util::getPost("orgpw");
	$password = Util::getPost("password");
	$password2 = Util::getPost("password2");
	// Password
	if(isset($orgpw) && isset($password) && isset($password2))
	{
		if($password != $password2)
			$error = "Passwords don't match, try again";
		elseif(Password::checkPassword($orgpw) == true)
		{
			Password::updatePassword($password);
			$error = "Password updated";
		}
		else
			$error = "Original password is wrong, please try again";
	}

	$timeago = Util::getPost("timeago");
	if(isset($timeago))
		UserConfig::set("timeago", $timeago);

	$deleteentityid = Util::getPost("deleteentityid");
	$deleteentitytype = Util::getPost("deleteentitytype");
	// Tracker
	if(isset($deleteentityid) && isset($deleteentitytype))
	{
		$q = UserConfig::get("tracker_" . $deleteentitytype);
		foreach($q as $k => $ent)
		{
			if($ent["id"] == $deleteentityid)
			{
				unset($q[$k]);
				$error = $ent["name"]." has been removed";
			}
		}
		UserConfig::set("tracker_" . $deleteentitytype, $q);
	}

	$entity = Util::getPost("entity");
	$entitymetadata = Util::getPost("entitymetadata");
	// Tracker
	if((isset($entity) && $entity != null) && (isset($entitymetadata) && $entitymetadata != null))
	{
		$entitymetadata = json_decode("$entitymetadata", true);
		$entities = UserConfig::get("tracker_" . $entitymetadata['type']);
		$entity = array('id' => $entitymetadata['id'], 'name' => $entitymetadata['name']);

		if(empty($entities) || !in_array($entity, $entities))
		{
			$entities[] = $entity;
			UserConfig::set("tracker_" . $entitymetadata['type'], $entities);
			$error = "{$entitymetadata['name']} has been added to your tracking list";
		}
		else
			$error = "{$entitymetadata['name']} is already being tracked";
	}

	$ddcombine = Util::getPost("ddcombine");
	if(isset($ddcombine))
		UserConfig::set("ddcombine", $ddcombine);

	$ddmonthyear = Util::getPost("ddmonthyear");
	if(isset($ddmonthyear))
		UserConfig::set("ddmonthyear",$ddmonthyear);

	$subdomain = Util::getPost("subdomain");
	if ($subdomain) {
		$banner = Util::getPost("banner");
		$bannerUpdates = array("$subdomain" => $banner);
		// table is updated if user is ceo/executor in code thta loads this information below
	}
}

$data["entities"] = Account::getUserTrackerData();
$data["themes"] = Util::bootstrapThemes();
$data["viewthemes"] = Util::themesAvailable();
$data["apiKeys"] = Api::getKeys($userID);
$data["apiChars"] = Api::getCharacters($userID);
$charKeys = Api::getCharacterKeys($userID);
$charKeys = Info::addInfo($charKeys);
$data["apiCharKeys"] = $charKeys;
$data["userInfo"] = User::getUserInfo();
$data["currentTheme"] = UserConfig::get("theme", "cyborg");
$data["sessionviewtheme"] = UserConfig::get("viewtheme", "bootstrap");
$data["timeago"] = UserConfig::get("timeago");
$data["ddcombine"] = UserConfig::get("ddcombine");
$data["ddmonthyear"] = UserConfig::get("ddmonthyear");
$data["useSummaryAccordion"] = UserConfig::get("useSummaryAccordion", true);
$data["sessions"] = User::getSessions($userID);
$data["history"] = User::getPaymentHistory($userID);

$apiChars = Api::getCharacters($userID);
$domainChars = array();
if ($apiChars != null) foreach($apiChars as $apiChar) {
	$char = Info::getPilotDetails($apiChar["characterID"], null);
	$char["corpTicker"] = modifyTicker(Db::queryField("select ticker from zz_corporations where corporationID = :corpID", "ticker", array(":corpID" => $char["corporationID"])));
	$char["alliTicker"] = modifyTicker(Db::queryField("select ticker from zz_alliances where allianceID = :alliID", "ticker", array(":alliID" => $char["allianceID"])));

	$domainChars[] = $char;
}

$corps = array();
$allis = array();
foreach ($domainChars as $domainChar) {
	if (@$domainChar["isCEO"]) {
		$subdomain = modifyTicker($domainChar["corpTicker"]) . ".zkillboard.com";
		if (isset($bannerUpdates[$subdomain])) {
			$banner = $bannerUpdates[$subdomain];
			Db::execute("insert into zz_subdomains (subdomain, banner) values (:subdomain, :banner) on duplicate key update banner = :banner", array(":subdomain" => $subdomain, ":banner" => $banner));
			$error = "Banner updated for $subdomain, please wait 2 minutes for the change to take effect.";
		}
		$corpStatus = Db::queryRow("select adfreeUntil, banner from zz_subdomains where subdomain = :subdomain", array(":subdomain" => $subdomain), 0);
		$domainChar["adfreeUntil"] = @$corpStatus["adfreeUntil"];
		$domainChar["banner"] = @$corpStatus["banner"];
		$corps[] = $domainChar;
	}
	if (@$domainChar["isExecutorCEO"]) {
		$subdomain = modifyTicker($domainChar["alliTicker"]) . ".zkillboard.com";
		if (isset($bannerUpdates[$subdomain])) {
			$banner = $bannerUpdates[$subdomain];
			Db::execute("insert into zz_subdomains (subdomain, banner) values (:subdomain, :banner) on duplicate key update banner = :banner", array(":subdomain" => $subdomain, ":banner" => $banner));
			$error = "Banner updated for $subdomain, please wait 2 minutes for the change to take effect.";
		}
		$status = Db::queryRow("select adfreeUntil, banner from zz_subdomains where subdomain = :subdomain", array(":subdomain" => $subdomain), 0);
		$domainChar["adfreeUntil"] = @$status["adfreeUntil"];
		$domainChar["banner"] = @$status["banner"];
		$alli[] = $domainChar;
	}
}
$data["domainCorps"] = $corps;
$data["domainAllis"] = $allis;
$data["domainChars"] = $domainChars;

$app->render("account.html", array("data" => $data, "message" => $error, "key" => $key, "reqid" => $reqid));

function modifyTicker($ticker) {
	$ticker = str_replace(" ", "_", $ticker);
	$ticker = preg_replace('/^\./', "dot.", $ticker);
	$ticker = preg_replace('/\.$/', ".dot", $ticker);
	return strtolower($ticker);
}
