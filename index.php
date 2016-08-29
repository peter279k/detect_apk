<?php
	require "vendor/autoload.php";
	require "helper/greet.php";
	require "helper/process_apk.php";
	
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Application;
	use Symfony\Component\Console\Question\ChoiceQuestion;
	
	class detect_apk extends Command {
		
		protected function configure() {
			$this
				-> setName('detect')
				-> setDescription('what kinds of file you want to detect (default: apk)?')
				-> addArgument(
					'action',
					InputArgument::OPTIONAL);
		}
		
		protected function execute(InputInterface $input, OutputInterface $output) {
			$action = $input->getArgument('action');
			
			if($action == "apk" || $action == "") {
				//set the absolute file path in file_path.json
				
				$paths = file_get_contents("./file_path.json");
				$paths = json_decode($paths, true);
				
				foreach($paths as $key => $value) {
					if($key == "file_path") {
						foreach($value as $file_path)
							process_apk($file_path, $paths["aapt_execution"]);
					}
				}
			}
		}
	}
	
	$command = new greet();
	$application = new Application('detect_apk', 'beta-1.0');
	$application -> add($command);
	$application -> add(new detect_apk());
	$application -> setDefaultCommand($command -> getName());
	$application -> run();
?>