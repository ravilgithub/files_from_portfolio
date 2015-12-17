<?php
/**
*
* Часть класса для работы с базой данных
*
*/	
	class DB{
		private $_db;
		protected static $_instance;
		private function __construct(){
			$db_conn = 'mysql:dbname=portfolio;host=127.0.0.1';
			$db_user = 'root';
			$db_pass = '';
			$this->_db = new PDO($db_conn, $db_user, $db_pass);
			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_db->exec("set names utf8");
		}
		
		public function __destruct(){
			unset($this->_db);
		}
		
		public static function _getInstance(){
			if(!self::$_instance instanceof self){
				self::$_instance = new self;
			}
			return self::$_instance;
		}
		
		private function __sleep(){}
		private function __wakeup(){}
		private function __clone(){}
		
		public function db2Array($stmt){
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	
	// Проверяем существует ли пользователь с подобным "nik_name" или "email" в базе данных	
		public function checkUser(array $params){
			$colums = array_keys($params);
			try{
				$this->_db->beginTransaction();
				$sql = 'SELECT ' . $colums[0] . ', ' . $colums[1] . ' FROM users WHERE ' . $colums[0] . ' = ? OR ' . $colums[1] . ' = ?';
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(1, $params[$colums[0]], PDO::PARAM_STR, 45);
				$stmt->bindParam(2, $params[$colums[1]], PDO::PARAM_STR, 45);
				$stmt->execute();
				$this->_db->commit();
				return $this->db2Array($stmt);
			}catch(PDOException $e){
				$this->_db->rollback();
				return array('error'=>$e->getMessage());
			}
		}
		
	// Регистрируем пользователя
		public function addUser($nik_name, $email, $passwordHash, $salt, $date){
			try{
				$this->_db->beginTransaction();
				$sql = 'INSERT INTO users(nik_name, email, password, salt, add_date) VALUES(?,?,?,?,?)';
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(1, $nik_name, PDO::PARAM_STR, 45);
				$stmt->bindParam(2, $email, PDO::PARAM_STR, 45);
				$stmt->bindParam(3, $passwordHash, PDO::PARAM_STR, 255);
				$stmt->bindParam(4, $salt, PDO::PARAM_STR, 255);
				$stmt->bindParam(5, $date, PDO::PARAM_INT);
				$stmt->execute();
				$this->_db->commit();
				return true;
			}catch(PDOException $e){
				$this->_db->rollback();
				return array('error'=>$e->getMessage());
			}
		}
		
	// Добавляем коментарий	
		function addComment($post, $date, $blogId, $userId){
			try{
				$this->_db->beginTransaction();
				$sql = 'INSERT INTO comments(content, add_date, parent_id, blogs_id, users_id) VALUES(?,?,?,?,?)';
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(1, $post['comment'], PDO::PARAM_STR);
				$stmt->bindParam(2, $date, PDO::PARAM_INT);
				$stmt->bindParam(3, $post['comment_parent'], PDO::PARAM_INT);
				$stmt->bindParam(4, $blogId, PDO::PARAM_INT);
				$stmt->bindParam(5, $userId, PDO::PARAM_INT);
				$stmt->execute();
				$this->_db->commit();
				return true;
			}catch(PDOException $e){
				$this->_db->rollback();
				return array('error'=>$e->getMessage());
			}
		}
		
	// Получаем информацию о пользователе
		public function getUser($email){
			try{
				$this->_db->beginTransaction();
				$sql = 'SELECT id, nik_name, email, password, salt, add_date FROM users WHERE email = ?';
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(1, $email, PDO::PARAM_STR, 45);
				$stmt->execute();
				$this->_db->commit();
				return $this->db2Array($stmt);
			}catch(PDOException $e){
				$this->_db->rollback();
				return array('error'=>$e->getMessage());
			}
		}	
		
	/*............................................*/	

	}