<?php

// ロック開始
function lock () {
	$locked = 'locked';
	$sleep_cnt = 0;
	while (1) {
	  if (file_exists("lock.txt")) {
		sleep(1);
		$sleep_cnt++ ;
		if ($sleep_cnt == 15){
			unlock();
		}  
	  } else {
		file_put_contents("lock.txt",$locked);		
		break;
	  }
	}
  } // lock
  
  // ロック終了
  function unlock (){
	unlink("lock.txt");
  } // unlock

?>