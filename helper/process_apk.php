<?php
	use Symfony\Component\Process\Process;
	
	$data = array();
	$apk_file_path = null;
	
	function process_apk($file_path, $aapt) {
		if(!is_dir($file_path)) {
			die("invalid file path");
		}
		
		//using pathinfo() to detect extension file name
		$apk_dirs = scandir($file_path);
		
		$index = 2;
		
		$len = count($apk_dirs);
		
		match_apk($apk_dirs, $file_path);
		
		/*
		for(;$index<$len;$index++) {
			$extension_name = pathinfo($apk_dirs[$index]);
			
			if($extension_name["extension"] == "apk") {
				//execute_command($aapt . "  dump badging " . $file_path . "\\" . $extension_name["basename"], $extension_name["basename"], $file_path);
			}
		}
		*/
	}
	
	function match_apk($apk_dirs, $file_path) {
		
		$file_paths = explode("\\", $file_path);
		
		$len = count($file_paths);
		
		if(file_exists("./db.txt")) {
			$handle = fopen("./db.txt", "r");
			$user = fgets($handle, 4096);
			$pass = fgets($handle, 4096);
			fclose($handle);
		}
		else {
			exit("db.txt must have been setted.\n");
		}
		
		$link_db = new PDO('mysql:host=localhost;dbname=apks;charset=utf8', trim($user), trim($pass));
		$stmt = $link_db -> prepare("select * from `apk_info` where apk_source = :apk_source");
		$result = $stmt -> execute(array(
			":apk_source" => $file_paths[$len-1]
		));
		
		$res = array();
		$index = 0;
		
		while($row = $stmt -> fetch()) {
			$res[$index]["apk_id"] = $row["apk_id"];
			$index++;
		}
		
		$check = false;
		
		$len = count($apk_dirs);
		
		for($index=2;$index<$len;$index++) {
			for($j=0;$j<count($res);$j++) {
				if(stristr($apk_dirs[$index], $res[$j]["apk_id"]) == false) {
					$check = true;
				}
				else {
					$check = false;
					break;
				}
			}
			
			if($check == false) {
				echo $apk_dirs[$index] . "\n";
			}
		}
	}
	
	function execute_command($command, $file_name, $file_path) {
		$file_paths = explode("\\", $file_path);
		
		global $data;
		$data = array();
		
		$len = count($file_paths);
		$data[":apk_source"] = $file_paths[$len-1];
		$data[":size"] = filesize($file_path . "\\" . $file_name);
		$data[":hash"] = md5(file_get_contents($file_path . "\\" . $file_name));
		
		global $apk_file_path;
		$apk_file_path = $file_path . "\\" . $file_name;
		
		//temporarily using default value
		
		$data[":downloads"] = 0;
		$data[":rate"] = 4.5;
		$data[":rate_people"] = 0;
		$data[":category"] = "無";
		$data[":develop_team"] = "無";
		
		$process = new Process($command);
		$process -> run(function ($type, $buffer) {
			if(Process::ERR === $type) {
				$buffer = str_replace(["\r", "\n"], "", $buffer);
				$buffer = trim($buffer);
				if(strlen($buffer) != 0)
					echo $buffer . "\n";
			}
			else {
				//package: name='air.com.vudu.air.DownloaderTablet' versionCode='1148101' versionName='4.1.51.8101' platformBuildVersionName='5.1.1-1819727'
				global $data;
				$buffers = explode(" ", $buffer);
				if(count($buffers) > 1) {
					$len = count($buffers);
					if($buffers[0] == "package:") {
						$buffers[1] = split_str($buffers[1]);
						$buffers[3] = split_str($buffers[3]);
						
						$data[":apk_id"] = $buffers[1];
						$data[":version"] = $buffers[3];
						
						//var_dump($data);
						global $apk_file_path;
						store_data($data, $apk_file_path);
					}
				}
			}
		});
	}
	
	function split_str($str) {
		$str = explode("=", $str);
		$str = $str[1];
		$str = str_replace("'", "", $str);
		return $str;
	}
	
	function store_data($data, $apk_file_path) {
		//using MySQL to store apk information
		if(file_exists("./db.txt")) {
			$handle = fopen("./db.txt", "r");
			$user = fgets($handle, 4096);
			$pass = fgets($handle, 4096);
			fclose($handle);
		}
		else {
			exit("db.txt must have been setted.\n");
		}
		
		$link_db = new PDO('mysql:host=localhost;dbname=apks;charset=utf8', trim($user), trim($pass));
		
		if(!$link_db) {
			die("cannot link database");
		}
		
		else {
			try {
				$stmt = $link_db -> prepare("INSERT INTO apk_info(apk_id, version, downloads, rate, rate_people, category, apk_source, 
				develop_team, size, hash) VALUES(:apk_id, :version, :downloads, :rate, :rate_people, :category, :apk_source, :develop_team, :size, :hash)");
			
				$stmt -> execute($data);
				
				if($stmt -> rowCount()) {
					echo "store success\n";
				}
				else {
					if(!check_duplicate($link_db, $data))
						echo $apk_file_path . " is no inserted...\n";
				}
			}
			catch(PDOException $e) {
				echo "store failed\n";
				
				if($e -> getCode() == 1062) {
					// Take some action if there is a key constraint violation, i.e. duplicate name
					echo "duplicate record.\n";
				}
				else {
					throw $e;
					exit("The program is terminated...");
				}
			}
			
			$link_db = null;
		}
	}
	
	function check_duplicate($link_db, $data) {
		$stmt = $link_db -> prepare("SELECT * FROM apk_info WHERE apk_id = :apk_id");
		$stmt -> execute(array(
			":apk_id" => $data[":apk_id"]
		));
		
		if($stmt -> rowCount() >= 1) {
			return true;
		}
		else {
			return false;
		}
		
		$link_db = null;
	}
?>