<?php

// Функция поиска для сайта "ravil.zzz.com.ua"
	public function search($post){
		$queryRes = '';
		$partQuery = '';
		$i = 0;
		$str = $post['search'];
		$str = substr($str, 0, 64);
		$str = preg_replace('/[^\w\x7F-\xFF\s]/', ' ', $str);
		$rows = array('blogs.title', 'blogs.content', 'blogs.category');
		$where = 'WHERE ';
		$like = ' LIKE \'%';
		$or = '%\' OR ';
		
		if(!empty($str)){
			$words = explode(' ', $str);
			if(count($words) > 0){
				foreach($words as $word){
					$word = self::clearData($word);
					if($word != '' and $word != ' ' and strlen($word) > 1){
						if($i == 0){
							foreach($rows as $row){
								$partQuery .= $row . $like . $word . $or;
							}
							$queryRes .= $where . $partQuery;
						}else{
							foreach($rows as $row){
								$queryRes .= $row . $like . $word . $or;
							}
						}
					}
					$i++;
				}
				$queryRes = substr($queryRes, 0, strrpos($queryRes, '%') +2);
			}
		}
		$_SESSION['search'] = json_encode($queryRes);
		header("location: index.php?page=blog");
	}