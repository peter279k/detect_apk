<?php
	use Symfony\Component\Process\Process;
	
	function process_apk($file_path, $aapt) {
		if(!is_dir($file_path)) {
			die("invalid file path");
		}
		
		//using pathinfo() to detect extension file name
		$apk_dirs = scandir($file_path);
		$len = count($apk_dirs);
		
		for($index=2;$index<$len;$index++) {
			$extension_name = pathinfo($apk_dirs[$index]);
			
			if($extension_name["extension"] == "apk") {
				execute_command($aapt . "  dump badging " . $file_path . "\\" . $extension_name["basename"], $extension_name["basename"], $file_path);
			}
		}
	}
	
	function execute_command($command, $file_name, $file_path) {
		$file_paths = explode("\\", $file_path);
		$data = array();
		$len = count($file_paths);
		$data[":apk_source"] = $file_paths[$len-1];
		$data[":size"] = filesize($file_path . "\\" . $file_name);
		$data[":hash"] = md5(file_get_contents($file_path . "\\" . $file_name));
		
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
				
				$buffers = explode(" ", $buffer);
				if(count($buffers) > 1) {
					$len = count($buffers);
					if($buffers[0] == "package:") {
						$buffers[1] = split_str($buffers[1]);
						$buffers[3] = split_str($buffers[3]);
						
						$data[":apk_id"] = $buffers[1];
						$data[":version"] = $buffers[3];
					}
				}
			}
		});
		
		store_data($data);
	}
	
	function split_str($str) {
		$str = explode("=", $str);
		$str = $str[1];
		$str = str_replace("'", "", $str);
		return $str;
	}
	
	function store_data($data) {
		//using MySQL to store apk information
		if(file_exists("./db.txt")) {
			$handle = fopen("./db.txt", "r");
			echo $user = fgets($handle, 4096) . "\n";
			echo $pass = fgets($handle, 4096) . "\n";
			fclose($handle);
		}
		else {
			exit("db.txt must have been setted.\n");
		}
		
		$link_db = new PDO('mysql:host=localhost;dbname=apks', $user, $pass);
		
		if(!$link_db) {
			die("cannot link database");
		}
		
		else {
			$stmt = $link_db -> prepare("INSERT INTO apk_info(apk_id, version, downloads, rate, rate_people, category, apk_source, 
			meta_source, develop_team, size, hash) VALUES(:apk_id, :version, :downloads, :rate, :rate_people, :category, :apk_source,  
			:meta_source, :develop_team, :size, :hash)");
			
			$stmt -> execute($data);
			
			if($stmt) {
				echo "store success\n";
			}
			else {
				echo "store failed\n";
				exit("The program is terminated...");
			}
		}
	}
?>