<?php	
	
// Определение существует ли класс и метод в данном классе.
// Если есть то выполняется вызов метода и возврат разультата в JS или PHP скрипт.
	interface IFactory{
		public static function ckeckMethod(array $post);
	}
	
	class Factory implements IFactory{
		public static function ckeckMethod(array $post){
			$className = ucfirst($post['phpClass']);
			$methodName = $post['method'];
			if(class_exists($className, false)){
				$rc = new ReflectionClass(new $className);
				if($rc->hasMethod($methodName)){
					$rm = $rc->getMethod($methodName);
					if($rm->isStatic()){
						$result = $rm->invoke(null, $post);
					}else{
						$obj = $rc->newInstance($className);
						$result = $rm->invoke($obj, $post);
					}
				}else{
					$result = 'Wrong methodName';
				}
			}else{
				$result = 'Wrong className';
			}
			if($post['noJSON']){
				return $result; 
			}else{
				echo json_encode($result); // возврат в js
			}
		}
	}
?>