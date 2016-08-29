<?php
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	
	class greet extends Command {
		protected function configure() {
			$this
				->setName('greet:start')
				->setDescription('Outputs \'Hello detect_apk\'');
		}
		
		protected function execute(InputInterface $input, OutputInterface $output) {
			$output -> writeln('Thank you for using the detect_apk !');
			$output -> writeln('Here are some useful commands:');
			$output -> writeln('');
			$output -> writeln('detect');
			$output -> writeln('	To detect the apk file, you have to use this command and');
			$output -> writeln('	it will use aapt command to dump the apk informations.');
			$output -> writeln('	For example, package name ,versionName and son on.');
		}
	}
?>