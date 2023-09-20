<?php

/**
 * game short summary.
 *
 * game description.
 *
 * @version 1.0
 * @author Werner
 */
class game
{
    public $sql;
    public function __construct(mysql $sql)
    {
        $this->sql = $sql;
    }
    public function linkAccounts($username, $password)
    {
        $result = $this->sql->query("SELECT `ID`, `user` FROM `PlayerInfo` WHERE `user` LIKE ? AND `password` = ?", 'ss', $username, hash('sha512', $password));
        if($result === FALSE || $this->sql->num_rows != 1)
        {
            return FALSE;
        }
        else
        {
            return $result[0][0];
        }
    }
    public function getMemberData($id)
    {
        $result = $this->sql->query("SELECT * FROM `PlayerInfo` WHERE `ID` = ?", 'i', $id);
        if ($this->sql->num_rows < 1) {
            $result = $this->sql->query("SELECT * FROM `PlayerInfoOld` WHERE `ID` = ?", 'i', $id);
            if ($this->sql->num_rows < 1) {
                return FALSE;
            }
            else {
                $result[0]['Deleted'] = 1;
                return $result[0];
            }
        }
        else {
            $result[0]['Deleted'] = 0;
            return $result[0];
        }
    }
    public function getFriendlyColumnName($column)
    {
        $columns = ['user' => 'In-Game Name', 'C1' => 'Color 1', 'C2' => 'Color 2', 'IRC' => 'In-Game Group Channel (IRC)', 'color' => 'Name Color', 'Warn' => 'Warnings', 'LastLogged' => 'Last Online', 'CreatedAt' => 'Registered'];
        return array_key_exists($column, $columns) ? $columns[$column] : $column;
    }
    public function getFriendlyColumnValue($column, $value)
    {
        if ($column == 'Donor') {
            return ['0' => 'None', '1' => 'Bronze', '2' => 'Silver', '3' => 'Gold', '4' => 'Platinum', '5' => 'VIP', '6' => 'Legacy'][$value];
        }
        else if ($column == 'Locked') {
            return ['0' => 'No', '1' => 'Yes'][$value];
        }
        else if ($column == 'Muted') {
            return $value == "0" ? 'No' : "Yes, $value minutes remaining";
        }
        else if ($column == 'color') {
            if($value == "0") return "Custom color not set";
            // Parse color
        }
        else if ($column == 'Cash') {
            return "\$$value";
        }
        else if ($column == 'LastLogged') {
            if(preg_match('#(\d{2})/(\d{2})/(\d{4})#', $value, $matches))
            {
                return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
            }
        }
        else if ($column == 'CreatedAt') {
            return $value == "2014-09-18" ? "before " . $value : $value;
        }
        return $value;
    }
    public function relatedAccounts($ip, $serial)
    {
        $ipParts = explode('.', $ip);
        $result1 = $this->sql->query("SELECT *, 0 AS Deleted FROM `PlayerInfo` WHERE `IP` LIKE ? OR (`IP` LIKE ? AND `Serial` LIKE ?)", 'sss', $ip, ($ipParts[0] . '.' . $ipParts[1] . '.%'), $serial);
        $result2 = $this->sql->query("SELECT *, 1 AS Deleted FROM `PlayerInfoOld` WHERE `IP` LIKE ? OR (`IP` LIKE ? AND `Serial` LIKE ?)", 'sss', $ip, ($ipParts[0] . '.' . $ipParts[1] . '.%'), $serial);
        if (!is_array($result1)) $result1 = [];
        if (!is_array($result2)) $result2 = [];
        return array_merge($result1, $result2);
    }
    public function modSearch($args)
    {
        $queryargs = [0 => "SELECT *, 0 AS Deleted FROM `PlayerInfo` WHERE ", 1 => ''];
        
        $andlimit = 3;
        // Construct query
        for($i = 3; !empty($args[$i]); $i++)
        {
            if($i > $andlimit) $queryargs[0] .= " AND ";
            $matches = [];
            // score filter
            if(preg_match('#^score:\[(\d+),(\d+)\]$#', $args[$i], $matches))
            {
                $queryargs[0] .= "`Score` >= ? AND `Score` <= ?";
                $queryargs[1] .= 'ii';
                $queryargs[] = $matches[1];
                $queryargs[] = $matches[2];
            }
            // cash filter
            else if(preg_match('#^cash:\[(\d+),(\d+)\]$#', $args[$i], $matches))
            {
                $queryargs[0] .= "`Cash` >= ? AND `Cash` <= ?";
                $queryargs[1] .= 'ii';
                $queryargs[] = $matches[1];
                $queryargs[] = $matches[2];
            }
            // banned filter
            else if(preg_match('#^banned:(yes|no)$#', $args[$i], $matches))
            {
                $GLOBALS['modcpBanSearch'] = $matches[1];
                ++$andlimit;
            }
            // no filter
            else
            {
                $queryargs[0] .= "(`user` LIKE ? OR `IP` LIKE ? OR `oldname` LIKE ? OR `Serial` LIKE ? OR `CarString` LIKE ?)";
                $queryargs[1] .= 'sssss';
                for($j = 0; $j < 5; $j++) $queryargs[] = $this->addWildCards($args[$i]);
            }
        }
        
        $queryargs[0] .= " LIMIT 1000";
        
        $queryargs[0] = str_replace("AND  LIMIT", "LIMIT", $queryargs[0]);
        
        /*/ Debug
        echo htmlspecialchars($queryargs[0]);
        echo "<pre><code>" . print_r($queryargs, TRUE) . "</code></pre>";
        //*/
        
        // Call query
        $result1 = call_user_func_array([$this->sql, "query"], $queryargs);
        $queryargs[0] = str_replace("0 AS Deleted FROM `PlayerInfo`", "1 AS Deleted FROM `PlayerInfoOld`", $queryargs[0]);
        $result2 = call_user_func_array([$this->sql, "query"], $queryargs);

        
        
        if (!is_array($result1)) $result1 = [];
        if (!is_array($result2)) $result2 = [];
        return array_merge($result1, $result2);
    }
    public function getUsersByIpAndSerial($ip, $serial)
    {
        $bip = explode('.', $ip);
		$bip = $bip[0] . '.' . $bip[1] . '%';
        $result1 = $this->sql->query("SELECT *, 0 AS Deleted FROM PlayerInfo WHERE IP LIKE ? AND Serial = ?", "ss", $bip, $serial);
        $result2 = $this->sql->query("SELECT *, 1 AS Deleted FROM PlayerInfoOld WHERE IP LIKE ? AND Serial = ?", "ss", $bip, $serial);
        if (!is_array($result1)) $result1 = [];
        if (!is_array($result2)) $result2 = [];
        return array_merge($result1, $result2);
    }
    public function banDetails($id)
    {
        $result = $this->sql->query("SELECT *, 0 AS Expired FROM Banlist WHERE ID = ? LIMIT 1", "i", $id);
        if (!is_array($result)) {
            return $this->sql->query("SELECT *, 1 AS Expired FROM BanlistOld WHERE ID = ? LIMIT 1", "i", $id);
        }
        else return $result;
    }
    public function searchBannedUsers($args)
    {
        $queryargs = [0 => "SELECT *, 0 AS Expired FROM `Banlist` WHERE (", 1 => ''];
        
        for ($i = 3; !empty($args[$i]); $i++) {
            if ($i > 3) $queryargs[0] .= " OR ";
            $queryargs[0] .= "Username LIKE ? OR Date LIKE ? OR Admin LIKE ? OR Reason LIKE ? OR IP LIKE ? OR Serial LIKE ?";
            $queryargs[1] .= "ssssss";
            for ($j = 0; $j < 6; $j++) {
                $queryargs[] = $this->addWildCards($args[$i]);
            }
        }
        $queryargs[0] .= ") AND Admin != 'System Process'";
        
        // Call query
        $result1 = call_user_func_array([$this->sql, "query"], $queryargs);
        $queryargs[0] = str_replace("0 AS Expired FROM `Banlist`", "1 AS Expired FROM `BanlistOld`", $queryargs[0]);
        $result2 = call_user_func_array([$this->sql, "query"], $queryargs);
        
        if (!is_array($result1)) $result1 = [];
        if (!is_array($result2)) $result2 = [];
        return array_merge($result1, $result2);
    }
    public function bannedUsers($all = true)
    {
        return $this->sql->query("SELECT " . ($all ? "*" : "LOWER(`Username`) AS Username") . " FROM `Banlist`");
    }
	public function getBanState($array, $old = false)
	{
		$bip = explode('.', $array["IP"]);
		if (count($bip) != 4) {
			return false;
		}
		$bip = $bip[0] . '.' . $bip[1] . '.*.*';
		$table = $old ? "BanlistOld" : "Banlist";
		$res = $this->sql->query("SELECT * FROM $table WHERE Serial LIKE ? AND BIP LIKE ?", "ss", $array["Serial"], $bip);
		if (!is_array($res)) {
			return false;
		}
		else return $res;
	}
	public function userBan($id, $ban, $admin = "UCP", $reason = "None given")
	{
		$res = $this->sql->query("SELECT `user`, `IP`, `Serial` FROM `PlayerInfo` WHERE `ID` = ? LIMIT 1", "i", $id);
		if (is_array($res)) {
			$res = $res[0];
			$bip = explode('.', $res['IP']);
			$bip = $bip[0] . '.' . $bip[1] . '.*.*';
			if ($ban) {
				$this->sql->query("INSERT INTO Banlist (`Type`, `Username`, `Date`, `Admin`, `Reason`, `IP`, `Serial`, `BIP`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
								"isssssss",
								2,
								$res['user'],
								date('d/m/Y'),
								$admin,
								$reason,
								$res['IP'],
								$res['Serial'],
								$bip);
				$this->sql->query("UPDATE `PlayerInfo` SET `Locked` = 1 WHERE `ID` = ?", "i", $id);
			}
			else {
				$this->sql->query("INSERT INTO BanlistOld SELECT * FROM Banlist WHERE `Serial` LIKE ? AND `BIP` LIKE ?", "ss", $res['Serial'], $bip);
				$this->sql->query("DELETE FROM Banlist WHERE `Serial` LIKE ? AND `BIP` LIKE ?", "ss", $res['Serial'], $bip);
				$this->sql->query("UPDATE `PlayerInfo` SET `Locked` = 0 WHERE `ID` = ?", "i", $id);
			}
			return true;
		}
		return false;
	}
	public function userLock($id, $lock)
	{
		if ($lock === false) {
			return $this->sql->query("UPDATE `PlayerInfo` SET `Locked` = 0 WHERE `ID` = ?", "i", $id);
		}
	}
	public function userDelete($id, $delete) {
		if ($delete) {
			$return = $this->sql->query("INSERT INTO `PlayerInfoOld` (SELECT * FROM `PlayerInfo` WHERE `ID` = ?)", "i", $id);
			if ($return) {
				return $this->sql->query("DELETE FROM `PlayerInfo` WHERE `ID` = ?", "i", $id);
			}
			return false;
		}
	}
    public function inactiveAdmin($id) {
        $date = date("Y-m-d");
        return $this->sql->query("UPDATE PlayerInfo SET AdminInactive = Admin, AdminInactiveDate = ? WHERE ID = ?", "si", $date, $id);
    }
    public function activeAdmin($id) {
        return $this->sql->query("UPDATE PlayerInfo SET AdminInactive = 0 WHERE ID = ?", "i", $id);
    }
    public function highlightSearchArgs($args, $str)
    {
        $originalStr = $str;
        for($i = 3; !empty($args[$i]) && $str == $originalStr; $i++)
        {
            $args[$i] = preg_replace('#^[^:]+(?<!\\\):#i', '', $args[$i]);
            $str = str_ireplace($args[$i], '<span class="hl">' . htmlspecialchars($args[$i]) . '</span>', $str);
        }
        return $str;
    }
    private function addWildCards($arg)
    {
        return '%' . str_replace('%', '%%', $arg) . '%';
    }
	public function setDonator($id, $expiry, $level = 4)
	{
		$this->sql->query("UPDATE PlayerInfo SET Donor=?, HasCarText=1 WHERE ID=?", "ii", $level, $id);
		$this->sql->query("UPDATE PlayerInfo SET VIPDate=? WHERE ID=?", "si", date('Y-m-d', $expiry), $id);
	}
    public function addUploadedMap($userid, $filename)
    {
        $date = date('Y-m-d H:i:s');
        return $this->sql->query("INSERT INTO Uploader (userid, filename, date) VALUES (?, ?, ?)", "iss", $userid, $filename, $date);
    }
    public function listUploadedMaps($userid, $page)
    {
        if ($userid > -1)
        {
            $res1 = $this->sql->query("SELECT * FROM Uploader WHERE userid = ? AND `status` = 0 ORDER BY id DESC LIMIT ?,?", "iii", $userid, $page * 30, $page * 30 + 30);
            $res2 = $this->sql->query("SELECT * FROM Uploader WHERE userid = ? AND `status` != 0 ORDER BY id DESC LIMIT ?,?", "iii", $userid, $page * 30, $page * 30 + 30);
            if (!is_array($res1)) $res1 = [];
            if (!is_array($res2)) $res2 = [];
            return array_merge($res1, $res2);
        }
        else
        {
            $res1 = $this->sql->query("SELECT * FROM Uploader WHERE `status` = 0 ORDER BY id DESC LIMIT ?,?", "ii", $page * 30, $page * 30 + 30);
            $res2 = $this->sql->query("SELECT * FROM Uploader WHERE `status` != 0 ORDER BY id DESC LIMIT ?,?", "ii", $page * 30, $page * 30 + 30);
            if (!is_array($res1)) $res1 = [];
            if (!is_array($res2)) $res2 = [];
            return array_merge($res1, $res2);
        }
    }
    public function modifyUploadedStatus($id, $status, $reason) 
    {
        return $this->sql->query("UPDATE Uploader SET status = ?, reason = ? WHERE id = ?", "isi", $status, $reason, $id);
    }
    public function updatePassword($id, $pass)
    {
        $passhash = hash("sha512", $pass);
        return $this->sql->query("UPDATE PlayerInfo SET password = ? WHERE ID = ?", "si", $passhash, $id);
    }
    public function mapList()
    {
        return $this->sql->query("SELECT * FROM MapInfo ORDER BY Map ASC");
    }
    public function getNameChanges($id)
    {
        return $this->sql->query("SELECT * FROM nameLogs WHERE playerid = ? ORDER BY id DESC", "i", $id);
    }
    
    
}
