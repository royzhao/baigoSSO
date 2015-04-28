<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if(!defined("IN_BAIGO")) {
	exit("Access Denied");
}

/*-------------用户模型-------------*/
class MODEL_USER {
	private $obj_db;
    private $is_create_view;

	function __construct() { //构造函数
		$this->obj_db = $GLOBALS["obj_db"]; //设置数据库对象
        $this->is_create_view = false;
	}


	/** 创建表
	 * mdl_create function.
	 *
	 * @access public
	 * @return void
	 */
	function mdl_create_table() {
		$_arr_userCreate = array(
			"user_id"            => "int NOT NULL AUTO_INCREMENT COMMENT 'ID'",
			"user_name"          => "varchar(30) NOT NULL COMMENT '用户名'",
			"user_mail"          => "varchar(300) NOT NULL COMMENT 'E-mail'",
			"user_pass"          => "char(32) NOT NULL COMMENT '密码'",
			"user_rand"          => "char(6) NOT NULL COMMENT '随机串'",
			"user_nick"          => "varchar(30) NOT NULL COMMENT '昵称'",
			"user_status"        => "enum('wait','enable','disable') NOT NULL COMMENT '状态'",
			"user_note"          => "varchar(30) NOT NULL COMMENT '备注'",
			"user_time"          => "int NOT NULL COMMENT '创建时间'",
			"user_time_login"    => "int NOT NULL COMMENT '登录时间'",
			"user_ip"            => "varchar(15) NOT NULL COMMENT '最后IP地址'",
		);

		$_num_mysql = $this->obj_db->create_table(BG_DB_TABLE . "user", $_arr_userCreate, "user_id", "用户");

		if ($_num_mysql > 0) {
			$_str_alert = "y010105"; //更新成功
		} else {
			$_str_alert = "x010105"; //更新成功
		}

		return array(
			"str_alert" => $_str_alert, //更新成功
		);
	}


	/** 创建视图
	 * mdl_create_view function.
	 *
	 * @access public
	 * @return void
	 */
	function mdl_create_view() {
		$_arr_userCreat = array(
			"user_id"            => BG_DB_TABLE . "user",
			"user_name"          => BG_DB_TABLE . "user",
			"user_mail"          => BG_DB_TABLE . "user",
			"user_nick"          => BG_DB_TABLE . "user",
			"user_note"          => BG_DB_TABLE . "user",
			"user_status"        => BG_DB_TABLE . "user",
			"user_time"          => BG_DB_TABLE . "user",
			"user_time_login"    => BG_DB_TABLE . "user",
			"user_ip"            => BG_DB_TABLE . "user",
			"belong_app_id"      => BG_DB_TABLE . "app_belong",
		);

		$_str_sqlJoin = "LEFT JOIN `" . BG_DB_TABLE . "app_belong` ON (`" . BG_DB_TABLE . "user`.`user_id`=`" . BG_DB_TABLE . "app_belong`.`belong_user_id`)";

		$_num_mysql = $this->obj_db->create_view(BG_DB_TABLE . "user_view", $_arr_userCreat, BG_DB_TABLE . "user", $_str_sqlJoin);

		if ($_num_mysql > 0) {
			$_str_alert = "y010108"; //更新成功
		} else {
			$_str_alert = "x010108"; //更新成功
		}

		return array(
			"str_alert" => $_str_alert, //更新成功
		);
	}


	/** 检查字段
	 * mdl_column function.
	 *
	 * @access public
	 * @return void
	 */
	function mdl_column() {
		$_arr_colRows = $this->obj_db->show_columns(BG_DB_TABLE . "user");

		foreach ($_arr_colRows as $_key=>$_value) {
			$_arr_col[] = $_value["Field"];
		}

		return $_arr_col;
	}


	/** 登录
	 * mdl_login function.
	 *
	 * @access public
	 * @param mixed $num_userId
	 * @return void
	 */
	function mdl_login($num_userId) {
		$_arr_userData = array(
			"user_pass"         => $this->apiLogin["user_pass_do"],
			"user_rand"         => $this->apiLogin["user_rand"],
			"user_time_login"   => time(),
			"user_ip"           => fn_getIp(true),
		);

		$_num_mysql = $this->obj_db->update(BG_DB_TABLE . "user", $_arr_userData, "user_id=" . $num_userId); //更新数据
		if ($_num_mysql > 0) {
			$_str_alert = "y010103"; //更新成功
		} else {
			return array(
				"str_alert" => "x010103", //更新失败
			);
			exit;

		}

		return array(
			"user_id"    => $num_userId,
			"str_alert"  => $_str_alert, //成功
		);
	}


	/** 编辑
	 * mdl_edit function.
	 *
	 * @access public
	 * @param mixed $num_userId
	 * @return void
	 */
	function mdl_edit($num_userId) {
		$_arr_userData = array();

		if (isset($this->apiEdit["user_mail"])) {
			$_arr_userData["user_mail"] = $this->apiEdit["user_mail"]; //如果密码为空，则不修改
		}

		if (isset($this->apiEdit["user_pass_do"])) {
			$_arr_userData["user_pass"] = $this->apiEdit["user_pass_do"]; //如果密码为空，则不修改
			$_arr_userData["user_rand"] = $this->apiEdit["user_rand"]; //如果密码为空，则不修改
		}

		if ($this->apiEdit["user_nick"]) {
			$_arr_userData["user_nick"] = $this->apiEdit["user_nick"]; //如果密码为空，则不修改
		}

		if ($_arr_userData) {
			$_num_userId  = $num_userId;
			$_num_mysql   = $this->obj_db->update(BG_DB_TABLE . "user", $_arr_userData, "user_id=" . $_num_userId); //更新数据
		}

		if ($_num_mysql > 0) {
			$_str_alert = "y010103"; //更新成功
		} else {
			return array(
				"str_alert" => "x010103", //更新失败
			);
			exit;
		}

		return array(
			"user_id"    => $_num_userId,
			"str_alert"  => $_str_alert, //成功
		);
	}


	/** 提交
	 * mdl_submit function.
	 *
	 * @access public
	 * @param string $str_userPass (default: "")
	 * @param string $str_userRand (default: "")
	 * @return void
	 */
	function mdl_submit($str_userPass = "", $str_userRand = "") {
		$_arr_userData = array(
			"user_name"     => $this->userSubmit["user_name"],
			"user_mail"     => $this->userSubmit["user_mail"],
			"user_nick"     => $this->userSubmit["user_nick"],
			"user_status"   => $this->userSubmit["user_status"],
		);

		if (isset($this->userSubmit["user_id"])) {
			$_num_userId = $this->userSubmit["user_id"];
		} else {
			$_num_userId = 0;
		}

		if (isset($this->userSubmit["user_note"])) {
			$_arr_userData["user_note"] = $this->userSubmit["user_note"];
		}

		if ($_num_userId == 0) {
			$_arr_insert = array(
				"user_pass"         => $str_userPass,
				"user_rand"         => $str_userRand,
				"user_time"         => time(),
				"user_time_login"   => time(),
				"user_ip"           => fn_getIp(),
			);
			$_arr_data   = array_merge($_arr_userData, $_arr_insert);
			$_num_userId = $this->obj_db->insert(BG_DB_TABLE . "user", $_arr_data); //更新数据
			if ($_num_userId > 0) {
				$_str_alert = "y010101"; //更新成功
			} else {
				return array(
					"str_alert" => "x010101", //更新失败
				);
				exit;

			}
		} else {
			if ($str_userPass) {
				$_arr_userData["user_pass"] = $str_userPass; //如果密码为空，则不修改
			}
			if ($str_userRand) {
				$_arr_userData["user_rand"] = $str_userRand; //如果密码为空，则不修改
			}
			$_num_userId = $this->userSubmit["user_id"];
			$_num_mysql  = $this->obj_db->update(BG_DB_TABLE . "user", $_arr_userData, "user_id=" . $_num_userId); //更新数据
			if ($_num_mysql > 0) {
				$_str_alert = "y010103"; //更新成功
			} else {
				return array(
					"str_alert" => "x010103", //更新失败
				);
				exit;

			}
		}

		return array(
			"user_id"    => $_num_userId,
			"str_alert"  => $_str_alert, //成功
		);
	}


	/** 更新状态
	 * mdl_status function.
	 *
	 * @access public
	 * @param mixed $str_status
	 * @return void
	 */
	function mdl_status($str_status) {
		$_str_userId = implode(",", $this->userIds["user_ids"]);

		$_arr_userUpdate = array(
			"user_status" => $str_status,
		);

		$_num_mysql = $this->obj_db->update(BG_DB_TABLE . "user", $_arr_userUpdate, "user_id IN (" . $_str_userId . ")"); //删除数据

		//如影响行数大于0则返回成功
		if ($_num_mysql > 0) {
			$_str_alert = "y010103"; //成功
		} else {
			$_str_alert = "x010103"; //失败
		}

		return array(
			"str_alert" => $_str_alert,
		);
	}


	/** 读取
	 * mdl_read function.
	 *
	 * @access public
	 * @param mixed $str_user
	 * @param string $str_by (default: "user_id")
	 * @param int $num_notId (default: 0)
	 * @return void
	 */
	function mdl_read($str_user, $str_by = "user_id", $num_notId = 0) {
		$_arr_userSelect = array(
			"user_id",
			"user_name",
			"user_pass",
			"user_mail",
			"user_nick",
			"user_note",
			"user_rand",
			"user_status",
			"user_time",
			"user_time_login",
			"user_ip",
		);

		switch ($str_by) {
			case "user_id":
				$_str_sqlWhere = "user_id=" . $str_user;
			break;
			default:
				$_str_sqlWhere = $str_by . "='" . $str_user . "'";
			break;
		}

		if ($num_notId > 0) {
			$_str_sqlWhere .= " AND user_id<>" . $num_notId;
		}

		$_arr_userRows    = $this->obj_db->select_array(BG_DB_TABLE . "user", $_arr_userSelect, $_str_sqlWhere, 1, 0); //检查本地表是否存在记录

		if (isset($_arr_userRows[0])) { //用户名不存在则返回错误
			$_arr_userRow = $_arr_userRows[0];
		} else {
			return array(
				"str_alert" => "x010102", //不存在记录
			);
			exit;
		}

		$_arr_userRow["str_alert"]    = "y010102";

		return $_arr_userRow;

	}


	/** 从视图里出
	 * mdl_view function.
	 *
	 * @access public
	 * @param string $str_key (default: "")
	 * @param int $num_appId (default: 0)
	 * @return void
	 */
	function mdl_view($str_key = "", $num_appId = 0) {
		$_arr_userSelect = array(
			"user_id",
			"user_name",
			"user_mail",
			"user_nick",
			"user_note",
			"user_status",
			"user_time",
			"user_time_login",
			"user_ip",
		);

		$_str_sqlWhere = "1=1";

		if ($str_key) {
			$_str_sqlWhere .= " AND (user_name LIKE '%" . $str_key . "%' OR user_nick LIKE '%" . $str_key . "%' OR user_note LIKE '%" . $str_key . "%')";
		}

		if ($num_appId > 0) {
			$_str_sqlWhere .= " AND belong_app_id=" . $num_appId;
		}
        if($this->is_create_view == false){
            $this->mdl_create_view();
        }
		$_arr_userRows = $this->obj_db->select_array(BG_DB_TABLE . "user_view", $_arr_userSelect, $_str_sqlWhere . " ORDER BY user_id DESC"); //查询数据

		return $_arr_userRows;
	}


	/** 列出
	 * mdl_list function.
	 *
	 * @access public
	 * @param mixed $num_userNo
	 * @param int $num_userExcept (default: 0)
	 * @param string $str_key (default: "")
	 * @param string $str_status (default: "")
	 * @param bool $arr_notIn (default: false)
	 * @return void
	 */
	function mdl_list($num_userNo, $num_userExcept = 0, $str_key = "", $str_status = "", $arr_notIn = false) {
		$_arr_userSelect = array(
			"user_id",
			"user_name",
			"user_mail",
			"user_nick",
			"user_note",
			"user_status",
			"user_time",
			"user_time_login",
			"user_ip",
		);

		$_str_sqlWhere = "1=1";

		if ($str_key) {
			$_str_sqlWhere .= " AND (user_name LIKE '%" . $str_key . "%' OR user_nick LIKE '%" . $str_key . "%' OR user_note LIKE '%" . $str_key . "%')";
		}

		if ($str_status) {
			$_str_sqlWhere .= " AND user_status='" . $str_status . "'";
		}

		if ($arr_notIn) {
			$_str_notIn = implode(",", $arr_notIn);
			$_str_sqlWhere .= " AND user_id NOT IN (" . $_str_notIn . ")";
		}

		$_arr_userRows = $this->obj_db->select_array(BG_DB_TABLE . "user", $_arr_userSelect, $_str_sqlWhere . " ORDER BY user_id DESC", $num_userNo, $num_userExcept); //查询数据

		return $_arr_userRows;
	}


	/** 删除
	 * mdl_del function.
	 *
	 * @access public
	 * @param mixed $_arr_userIds
	 * @return void
	 */
	function mdl_del($_arr_userIds) {
		$_str_userId  = implode(",", $_arr_userIds);
		$_num_mysql   = $this->obj_db->delete(BG_DB_TABLE . "user", "user_id IN (" . $_str_userId . ")"); //删除数据

		//如车影响行数小于0则返回错误
		if ($_num_mysql > 0) {
			$_str_alert = "y010104"; //成功
			$this->obj_db->delete(BG_DB_TABLE . "app_belong", "belong_user_id IN (" . $_str_userId . ")"); //删除数据
		} else {
			$_str_alert = "x010104"; //失败
		}

		return array(
			"str_alert" => $_str_alert,
		);
	}


	/** 计数
	 * mdl_count function.
	 *
	 * @access public
	 * @param string $str_key (default: "")
	 * @param string $str_status (default: "")
	 * @param bool $arr_notIn (default: false)
	 * @return void
	 */
	function mdl_count($str_key = "", $str_status = "", $arr_notIn = false) {
		$_str_sqlWhere = "1=1";

		if ($str_key) {
			$_str_sqlWhere .= " AND (user_name LIKE '%" . $str_key . "%' OR user_nick LIKE '%" . $str_key . "%' OR user_note LIKE '%" . $str_key . "%')";
		}

		if ($str_status) {
			$_str_sqlWhere .= " AND user_status='" . $str_status . "'";
		}

		if ($arr_notIn) {
			$_str_notIn = implode(",", $arr_notIn);
			$_str_sqlWhere .= " AND user_id NOT IN (" . $_str_notIn . ")";
		}

		$_num_userCount = $this->obj_db->count(BG_DB_TABLE . "user", $_str_sqlWhere); //查询数据

		return $_num_userCount;
	}


	/**
	 * input_get_by function.
	 *
	 * @access public
	 * @param string $str_method (default: "get")
	 * @return void
	 */
	function input_get_by($str_method = "get") {
		if ($str_method == "post") {
			$_str_getBy = fn_getSafe(fn_post("user_by"), "txt", "");
			if ($_str_getBy == "user_id") {
				$_arr_userGet["user_by"]     = "user_id";
				$_arr_userChk                = $this->input_id_chk(fn_post("user_id"));
				$_arr_userGet["user_str"]    = $_arr_userChk["user_id"];
			} else {
				$_arr_userGet["user_by"]     = "user_name";
				$_arr_userChk                = $this->input_name_chk(fn_post("user_name"));
				$_arr_userGet["user_str"]    = $_arr_userChk["user_name"];
			}
		} else {
			$_str_getBy = fn_getSafe(fn_get("user_by"), "txt", "");
			if ($_str_getBy == "user_id") {
				$_arr_userGet["user_by"]     = "user_id";
				$_arr_userChk                = $this->input_id_chk(fn_get("user_id"));
				$_arr_userGet["user_str"]    = $_arr_userChk["user_id"];
			} else {
				$_arr_userGet["user_by"]     = "user_name";
				$_arr_userChk                = $this->input_name_chk(fn_get("user_name"));
				$_arr_userGet["user_str"]    = $_arr_userChk["user_name"];
			}
		}

		if ($_arr_userChk["str_alert"] != "ok") {
			return $_arr_userChk;
			exit;
		}

		$_arr_userGet["str_alert"] = "ok";

		return $_arr_userGet;
	}


	/**
	 * input_id_chk function.
	 *
	 * @access private
	 * @param mixed $num_id
	 * @return void
	 */
	private function input_id_chk($num_id) {
		$_arr_userId = validateStr($num_id, 1, 0, "str", "int");

		switch ($_arr_userId["status"]) {
			case "too_short":
				return array(
					"str_alert" => "x010217",
				);
				exit;
			break;

			case "format_err":
				return array(
					"str_alert" => "x010218",
				);
				exit;
			break;

			case "ok":
				$_num_userId = $_arr_userId["str"];
			break;
		}

		return array(
			"user_id"     => $_num_userId,
			"str_alert"   => "ok",
		);
	}


	/**
	 * input_name_chk function.
	 *
	 * @access public
	 * @param mixed $str_user
	 * @return void
	 */
	private function input_name_chk($str_name) {
		$_arr_userName = validateStr($str_name, 1, 30, "str", "strDigit");

		switch ($_arr_userName["status"]) {
			case "too_short":
				return array(
					"str_alert" => "x010201",
				);
				exit;
			break;

			case "too_long":
				return array(
					"str_alert" => "x010202",
				);
				exit;
			break;

			case "format_err":
				return array(
					"str_alert" => "x010203",
				);
				exit;
			break;

			case "ok":
				$_str_userName = $_arr_userName["str"];
			break;
		}

		return array(
			"user_name"   => $_str_userName,
			"str_alert"   => "ok",
		);
	}


	/**
	 * input_mail_chk function.
	 *
	 * @access public
	 * @param mixed $str_mail
	 * @param mixed $num_mailMin
	 * @return void
	 */
	private function input_mail_chk($str_mail) {

		if (BG_REG_NEEDMAIL == "on") {
			$_num_mailMin = 1;
		} else {
			$_num_mailMin = 0;
		}

		$_arr_userMail = validateStr($str_mail, $_num_mailMin, 300, "str", "email");

		switch ($_arr_userMail["status"]) {
			case "too_short":
				return array(
					"str_alert" => "x010206",
				);
				exit;
			break;

			case "too_long":
				return array(
					"str_alert" => "x010207",
				);
				exit;
			break;

			case "format_err":
				return array(
					"str_alert" => "x010208",
				);
				exit;
			break;

			case "ok":
				$_str_userMail = $_arr_userMail["str"];
			break;
		}

		return array(
			"user_mail"   => $_str_userMail,
			"str_alert"   => "ok",
		);
	}


	/**
	 * input_pass_chk function.
	 *
	 * @access public
	 * @param mixed $str_pass
	 * @return void
	 */
	private function input_pass_chk($str_pass) {
		$_arr_userPass = validateStr($str_pass, 1, 0);
		switch ($_arr_userPass["status"]) {
			case "too_short":
				return array(
					"str_alert" => "x010212",
				);
				exit;
			break;

			case "ok":
				$_str_userPass = $_arr_userPass["str"];
			break;
		}

		return array(
			"user_pass"   => $_str_userPass,
			"str_alert"   => "ok",
		);
	}


	/**
	 * input_nick_chk function.
	 *
	 * @access public
	 * @param mixed $str_nick
	 * @return void
	 */
	private function input_nick_chk($str_nick) {
		$_arr_userNick = validateStr($str_nick, 0, 30);
		switch ($_arr_userNick["status"]) {
			case "too_long":
				return array(
					"str_alert" => "x010214",
				);
				exit;
			break;

			case "ok":
				$_str_userNick = $_arr_userNick["str"];
			break;

		}

		return array(
			"user_nick"   => $_str_userNick,
			"str_alert"   => "ok",
		);
	}


	/**
	 * $this->input_note_chk function.
	 *
	 * @access public
	 * @param mixed $str_note
	 * @return void
	 */
	private function input_note_chk($str_note) {
		$_arr_userNote = validateStr($str_note, 0, 30);
		switch ($_arr_userNote["status"]) {
			case "too_long":
				return array(
					"str_alert" => "x010215",
				);
				exit;
			break;

			case "ok":
				$_str_userNote = $_arr_userNote["str"];
			break;

		}

		return array(
			"user_note"   => $_str_userNote,
			"str_alert"   => "ok",
		);
	}


	/**
	 * input_user_name function.
	 *
	 * @access public
	 * @return void
	 */
	function input_user_name() {
		$_num_notId = fn_getSafe(fn_get("not_id"), "int", 0);

		$_arr_userName = $this->input_name_chk(fn_get("user_name"));
		if ($_arr_userName["str_alert"] != "ok") {
			return $_arr_userName;
			exit;
		}
		if (defined("BG_BAD_NAME") && strlen(BG_BAD_NAME)) {
			if (fn_regChk($_arr_userName["user_name"], BG_BAD_NAME, true)) {
				return array(
					"str_alert" => "x010204",
				);
				exit;
			}
		}

		return array(
			"not_id"      => $_num_notId,
			"user_name"   => $_arr_userName["user_name"],
			"str_alert"   => "ok",
		);
	}


	/**
	 * input_user_mail function.
	 *
	 * @access public
	 * @return void
	 */
	function input_user_mail() {
		$_num_notId   = fn_getSafe(fn_get("not_id"), "int", 0);

		$_arr_userMail = $this->input_mail_chk(fn_get("user_mail"));
		if ($_arr_userMail["str_alert"] != "ok") {
			return $_arr_userMail;
			exit;
		}

		if (defined("BG_ACC_MAIL") && strlen(BG_ACC_MAIL)) {
			if (!fn_regChk($_arr_userMail["user_mail"], BG_ACC_MAIL)) {
				return array(
					"str_alert" => "x010209",
				);
				exit;
			}
		} else if (defined("BG_BAD_MAIL") && strlen(BG_BAD_MAIL)) {
			if (fn_regChk($_arr_userMail["user_mail"], BG_BAD_MAIL)) {
				return array(
					"str_alert" => "x010210",
				);
				exit;
			}
		}

		return array(
			"not_id"      => $_num_notId,
			"user_mail"   => $_arr_userMail["user_mail"],
			"str_alert"   => "ok",
		);
	}


	/** api 注册
	 * api_reg function.
	 *
	 * @access public
	 * @return void
	 */
	function api_reg() {
		$_arr_userName = $this->input_name_chk(fn_post("user_name"));
		if ($_arr_userName["str_alert"] != "ok") {
			return $_arr_userName;
			exit;
		}
		$this->userSubmit["user_name"] = $_arr_userName["user_name"];

		if (defined("BG_BAD_NAME") && strlen(BG_BAD_NAME)) {
			if (fn_regChk($this->userSubmit["user_name"], BG_BAD_NAME, true)) {
				return array(
					"str_alert" => "x010204",
				);
				exit;
			}
		}

		$_arr_userRow = $this->mdl_read($this->userSubmit["user_name"], "user_name");
		if ($_arr_userRow["str_alert"] == "y010102") {
			return array(
				"str_alert" => "x010205",
			);
			exit;
		}

		$_arr_userMail = $this->input_mail_chk(fn_post("user_mail"));
		if ($_arr_userMail["str_alert"] != "ok") {
			return $_arr_userMail;
			exit;
		}
		$this->userSubmit["user_mail"] = $_arr_userMail["user_mail"];

		if (BG_REG_ONEMAIL == "false" && BG_REG_NEEDMAIL == "on") {
			$_arr_userRow = $this->mdl_read($this->userSubmit["user_mail"], "user_mail"); //检查Email
			if ($_arr_userRow["str_alert"] == "y010102") {
				return array(
					"str_alert" => "x010211",
				);
				exit;
			}
		}

		if (defined("BG_ACC_MAIL") && strlen(BG_ACC_MAIL)) {
			if (!fn_regChk($this->userSubmit["user_mail"], BG_ACC_MAIL)) {
				return array(
					"str_alert" => "x010209",
				);
				exit;
			}
		} else if (defined("BG_BAD_MAIL") && strlen(BG_BAD_MAIL)) {
			if (fn_regChk($this->userSubmit["user_mail"], BG_BAD_MAIL)) {
				return array(
					"str_alert" => "x010210",
				);
				exit;
			}
		}

		$_arr_userPass = $this->input_pass_chk(fn_post("user_pass"));
		if ($_arr_userPass["str_alert"] != "ok") {
			return $_arr_userPass;
			exit;
		}
		$this->userSubmit["user_pass"] = $_arr_userPass["user_pass"];

		$_arr_userNick = $this->input_nick_chk(fn_post("user_nick"));
		if ($_arr_userNick["str_alert"] != "ok") {
			return $_arr_userNick;
			exit;
		}
		$this->userSubmit["user_nick"]    = $_arr_userNick["user_nick"];
		$this->userSubmit["user_status"]  = "enable";
		$this->userSubmit["str_alert"]    = "ok";

		return $this->userSubmit;
	}


	/** api 登录
	 * api_login function.
	 *
	 * @access public
	 * @return void
	 */
	function api_login() {
		$_arr_userGet = $this->input_get_by("post");

		if ($_arr_userGet["str_alert"] != "ok") {
			return $_arr_userGet;
			exit;
		}

		$this->apiLogin   = $_arr_userGet;
		$_arr_userPass    = $this->input_pass_chk(fn_post("user_pass"));
		if ($_arr_userPass["str_alert"] != "ok") {
			return $_arr_userPass;
			exit;
		}

		$this->apiLogin["user_pass"]      = $_arr_userPass["user_pass"];
		$this->apiLogin["user_rand"]      = fn_rand(6);
		$this->apiLogin["user_pass_do"]   = fn_baigoEncrypt($this->apiLogin["user_pass"], $this->apiLogin["user_rand"], true);
		$this->apiLogin["str_alert"]      = "ok";

		return $this->apiLogin;
	}


	/** api 编辑
	 * api_edit function.
	 *
	 * @access public
	 * @return void
	 */
	function api_edit() {
		$_arr_userGet = $this->input_get_by("post");

		if ($_arr_userGet["str_alert"] != "ok") {
			return $_arr_userGet;
			exit;
		}

		$this->apiEdit = $_arr_userGet;

		if (fn_post("user_mail")) {
			$_arr_userMail = $this->input_mail_chk(fn_post("user_mail"));
			if ($_arr_userMail["str_alert"] != "ok") {
				return $_arr_userMail;
				exit;
			}
			$this->apiEdit["user_mail"] = $_arr_userMail["user_mail"];

			if (defined("BG_ACC_MAIL") && strlen(BG_ACC_MAIL)) {
				if (!fn_regChk($this->apiEdit["user_mail"], BG_ACC_MAIL)) {
					return array(
						"str_alert" => "x010209",
					);
					exit;
				}
			} else if (defined("BG_BAD_MAIL") && strlen(BG_BAD_MAIL)) {
				if (fn_regChk($this->apiEdit["user_mail"], BG_BAD_MAIL)) {
					return array(
						"str_alert" => "x010210",
					);
					exit;
				}
			}
		}

		$this->apiEdit["user_check_pass"] = fn_getSafe(fn_post("user_check_pass"), "txt", "");

		if ($this->apiEdit["user_check_pass"] == true) {
			$_arr_userPass = $this->input_pass_chk(fn_post("user_pass"));
			if ($_arr_userPass["str_alert"] != "ok") {
				return $_arr_userPass;
				exit;
			}
			$this->apiEdit["user_pass"] = $_arr_userPass["user_pass"];
		}

		if (fn_post("user_pass_new")) {
			$this->apiEdit["user_pass_new"]  = fn_post("user_pass_new");
			$this->apiEdit["user_rand"]      = fn_rand(6);
			$this->apiEdit["user_pass_do"]   = fn_baigoEncrypt($this->apiEdit["user_pass_new"], $this->apiEdit["user_rand"], true);
		}

		$_arr_userNick = $this->input_nick_chk(fn_post("user_nick"));
		if ($_arr_userNick["str_alert"] != "ok") {
			return $_arr_userNick;
			exit;
		}
		$this->apiEdit["user_nick"] = $_arr_userNick["user_nick"];
		$this->apiEdit["str_alert"] = "ok";

		return $this->apiEdit;
	}


	/** 表单验证
	 * input_submit function.
	 *
	 * @access public
	 * @return void
	 */
	function input_submit() {
		if (!fn_token("chk")) { //令牌
			return array(
				"str_alert" => "x030101",
			);
			exit;
		}

		$this->userSubmit["user_id"] = fn_getSafe(fn_post("user_id"), "int", 0);

		if ($this->userSubmit["user_id"] > 0) {
			//检查用户是否存在
			$_arr_userRow = $this->mdl_read($this->userSubmit["user_id"]);
			if ($_arr_userRow["str_alert"] != "y010102") {
				return $_arr_userRow;
			}
		}

		$_arr_userName = $this->input_name_chk(fn_post("user_name"));
		if ($_arr_userName["str_alert"] != "ok") {
			return $_arr_userName;
			exit;
		}
		$this->userSubmit["user_name"] = $_arr_userName["user_name"];

		//检验用户名是否重复
		$_arr_userRow = $this->mdl_read($this->userSubmit["user_name"], "user_name", $this->userSubmit["user_id"]);
		if ($_arr_userRow["str_alert"] == "y010102") {
			return array(
				"str_alert" => "x010205",
			);
			exit;
		}


		$_arr_userMail = $this->input_mail_chk(fn_post("user_mail"));
		if ($_arr_userMail["str_alert"] != "ok") {
			return $_arr_userMail;
			exit;
		}
		$this->userSubmit["user_mail"] = $_arr_userMail["user_mail"];

		$_arr_userNick = $this->input_nick_chk(fn_post("user_nick"));
		if ($_arr_userNick["str_alert"] != "ok") {
			return $_arr_userNick;
			exit;
		}
		$this->userSubmit["user_nick"] = $_arr_userNick["user_nick"];

		$_arr_userNote = $this->input_note_chk(fn_post("user_note"));
		if ($_arr_userNote["str_alert"] != "ok") {
			return $_arr_userNote;
			exit;
		}
		$this->userSubmit["user_note"] = $_arr_userNote["user_note"];

		$_arr_userStatus = validateStr(fn_post("user_status"), 1, 0);
		switch ($_arr_userStatus["status"]) {
			case "too_short":
				return array(
					"str_alert" => "x010216",
				);
				exit;
			break;

			case "ok":
				$this->userSubmit["user_status"] = $_arr_userStatus["str"];
			break;
		}

		$this->userSubmit["str_alert"] = "ok";
		return $this->userSubmit;
	}


	/** 选择
	 * input_ids function.
	 *
	 * @access public
	 * @return void
	 */
	function input_ids() {
		if (!fn_token("chk")) { //令牌
			return array(
				"str_alert" => "x030101",
			);
			exit;
		}

		$_arr_userIds = fn_post("user_id");

		if ($_arr_userIds) {
			foreach ($_arr_userIds as $_key=>$_value) {
				$_arr_userIds[$_key] = fn_getSafe($_value, "int", 0);
			}
			$_str_alert = "ok";
		} else {
			$_str_alert = "none";
		}

		$this->userIds = array(
			"str_alert"   => $_str_alert,
			"user_ids"    => $_arr_userIds
		);

		return $this->userIds;
	}
}
