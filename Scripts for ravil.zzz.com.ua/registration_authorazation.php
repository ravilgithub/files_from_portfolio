<?php
	// Регистрация и авторизация для ravil.zzz.com.ua
	
	// Генерируем хэш пароля
		public function getHash($password, $salt, $i){
			if($i > 0){
				return $this->getHash(sha1(md5($password . $salt)), $salt, --$i);
			}else{
				return $password;
			}
		}
	
	// Регистрация пользователя
		public function registrationUser($post){
			$returnArr = array();
			$data = array();
			$str = '';
			$salt = str_replace('=', '', base64_encode(md5(microtime() . '1FD37EAA5ED9425683326F3SG5EA68DCD0E59')));
			$nik_name = $post['nik_name'];
			$email = $post['email'];
			$password = $post['password'];
			$matcher = self::$_db->checkUser(array('nik_name'=>$nik_name, 'email'=>$email));
			if(!$matcher['error']){
				if(!empty($matcher[0])){
					if($matcher[0]['nik_name'] == $nik_name){
						$str .= "Ник: $nik_name уже занят.";
					}elseif($matcher[0]['email'] == $email){
						$str .= "Email: $email уже занят.";
					}
					$returnArr['msg'] = 'Ошибка!|' . $str;
				}else{
					$date = time(); 
					$passwordHash = $this->getHash($password, $salt, self::ITERATIONCOUNT);
					$insertRes = self::$_db->addUser($nik_name, $email, $passwordHash, $salt, $date);
					if(!$insertRes['error']){
					// Массив для сессии
						$data['nik_name'] = $nik_name;
						$data['email'] = $email;
						$data['add_date'] = $date;
						$this->rememberUser($data, true);
					// Массив для возврата в JS
						$returnArr['status'] = 'ok';
						$returnArr['msg'] = "Поздравляем!|Вы зарегистрированы под ником: $nik_name";	
					}else{
						$returnArr['error'] = $insertRes['error'];
					}
				}
			}else{
				$returnArr['error'] = $matcher['error'];
			}
			return $returnArr;
		}
		
	// Авторизация пользователя	
		public function authorizeUser($post){
			$data = array();
			$returnArr = array();
			$email = $post['email'];
			$password = $post['password'];
			$userData = self::$_db->getUser($email);
			if(!$userData['error']){
				if(!empty($userData[0])){
					$newPasswordHash = $this->getHash($password, $userData[0]['salt'], self::ITERATIONCOUNT);
					if($newPasswordHash == $userData[0]['password']){
					// Массив для куки и сессии
						$data['nik_name'] = $userData[0]['nik_name'];
						$data['email'] = $post['email'];
						$data['add_date'] = $userData[0]['add_date'];
						$this->rememberUser($data, $post['checkbox']);
					// Массив для возврата в JS
						$returnArr['status'] = 'ok';
						$returnArr['msg'] = 'Приветствуем вас!|Вы вошли под ником: ' . $userData[0]['nik_name'];
					}else{
						$returnArr['msg'] = 'Ошибка!|Не правильный логин или пароль.';
					}
				}else{
					$returnArr['msg'] = 'Ошибка!|Не правильный логин или пароль.'; // такого email'a в бд. нет
				}
			}else{
				$returnArr['error'] = $userData['error'];
			}
			return $returnArr;
		}
?>