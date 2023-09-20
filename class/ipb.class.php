<?php

/**
 * Communications with the forum
 *
 * @version 1.0
 * @author Werner
 */
class ipb
{
    private $isLogged = NULL;
    private $sql;
    public function __construct(mysql $sql)
    {
        $this->sql = $sql;
    }
    public function isLogged($sessionid, $userid)
    {
        if($this->isLogged !== NULL)
        {
            return $this->isLogged;
        }
        $pass_hash = $this->sql->query("SELECT `member_id`, `ip_address` FROM `ipb_core_sessions` WHERE `id` LIKE ?", 's', $sessionid);
        if($pass_hash === FALSE)
        {
            trigger_error("Empty resultset", E_USER_ERROR);
            return FALSE;
        }
        if($pass_hash[0]['member_id'] != $userid)
        {
            $this->isLogged = FALSE;
            return FALSE;
        }
        if($pass_hash[0]['ip_address'] !== $_SERVER['REMOTE_ADDR'])
        {
            $this->isLogged = FALSE;
            return FALSE;
        }
        $this->isLogged = TRUE;
        return TRUE;
    }
    public function validCredentials($mid, $pass) {
        $data = $this->sql->query("SELECT `members_pass_hash`, `members_pass_salt` FROM `ipb_core_members` WHERE `member_id` = ? LIMIT 1", "i", $mid);
        if (!is_array($data)) return FALSE;
        $data = $data[0];
        
        return crypt($pass, '$2a$13$' . $data['members_pass_salt']);
        //if (hash("md5", hash("md5", $data['members_pass_salt']) . hash("md5", $pass)) == $data['members_pass_hash']) {
            //return TRUE;
        //}
        //else return FALSE;
    }
    public function getMemberData($userid, $ign = false)
    {
        // if(!$this->isLogged)
        // {
            // trigger_error("Member is not logged in", E_USER_ERROR);
        // }
        $fields = [ "member_id", "name", "member_group_id", "email", "joined", "ip_address", "last_activity", "mgroup_others", "members_seo_name", "ign"];
        $qf = "";
        foreach($fields as $field)
        {
            $qf .= "`$field`,";
        }
        $qf = rtrim($qf, ',');
		$idfield = $ign ? 'ign' : 'member_id';
        $data = $this->sql->query("SELECT $qf FROM `ipb_core_members` WHERE `$idfield` = ?", 'i', $userid);
        if($data === FALSE)
        {
            return FALSE;
        }
        else
        {
            $data[0]["members_display_name"] = $data[0]["name"];
            return $data[0];
        }
    }
    public function linkAccount($userid, $id)
    {
        $return = $this->sql->query("UPDATE `ipb_core_members` SET `ign` = ? WHERE `member_id` = ?", 'ii', $id, $userid);
        if($this->sql->num_rows != 1)
        {
            printDebugInfo($this->sql->error);
        }
        return $return == TRUE;
    }
	public function setDonator($ign) {
		$result = $this->sql->query("SELECT * FROM ipb_core_members WHERE ign = ?", "i", $ign);
		if ($result) {
			$result = $result[0];
            
            $mgroup_others = explode(',', $result["mgroup_others"]);
			if ($result["member_group_id"] == 9 || in_array('9', $mgroup_others)) {
				return true;
			}
			else {
				if (in_array($result["member_group_id"], $mgroup_others)) { //strpos($result["mgroup_others"], ',' . $result["member_group_id"] . ',') !== false) {
					return $this->sql->query("UPDATE ipb_core_members SET member_group_id = 9 WHERE ign = ?", "i", $ign);
				}
				else {
					if (in_array($result["member_group_id"], [4,6,8])) {
						$id = $result["member_group_id"];
						$mgroup_others[] = 9;
					}
					else {
						$id = 9;
						$mgroup_others[] = $result["member_group_id"];
					}
					$mgroup_others = implode(',', $mgroup_others);
					return $this->sql->query("UPDATE ipb_core_members SET member_group_id = ?, mgroup_others = ? WHERE ign = ?", "isi", $id, $mgroup_others, $ign);
				}
			}
		}
		return false;
	}
	public function getAdmins()
	{
		return $this->sql->query("SELECT * FROM ipb_core_members WHERE member_group_id = 4 OR member_group_id = 8 OR mgroup_others REGEXP '.*(^|,)4(,|$).*' OR mgroup_others REGEXP '.*(^|,)8(,|$).*'");
	}
}
