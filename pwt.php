<?php
/**
 * Welcome to PWT...
 *
 * @author  David Eddy <me@davidjeddy.com>
 */
class PWT
{
	/**
	 * Container for CLI options
	 * 
	 * @var [type]
	 */
	private $args;

	/**
	 * PHP long options for getopt()
	 * @var [type]
	 */
	private $lopts;

	/**
	 * 
	 */
	public function __construct()
	{
		global $argv;

		$this->lopts  = [
			"exclude::",
			"output::",
			"progress::",
			"source::",
			"verbose::",
		];

		if ($this->mapCLItoProperties()) {
			return $this->doIt();
		}

		return false;
	}

	/**
	 * Map our CLI options to the CLi options of each tool
	 * @return boolean
	 */
	private function mapCLItoProperties()
	{

		$this->args = getopt(null, $this->lopts);

		if (!isset($this->args["output"])) {
			$this->args["output"] = "./_output/";
		}

		return true;
	}

	/**
	 * Execute CLI commands
	 * @return boolean
	 */
	private function doIt() {

		system('mkdir '.$this->args['output']."pdepend");

		// sebastian utilities
		// --progress=true --exclude="./vendor" --source="./"
		system('./vendor/fabpot/php-cs-fixer/php-cs-fixer fix '.$this->args['source'].' exclude="'.$this->args['exclude'].'"');
		/*
		system('php ./vendor/sebastian/phpcpd/phpcpd --exclude="'.$this->args['exclude'].'" '.$this->args['source'].' \
			--progress');
		system('php ./vendor/sebastian/phpdcd/phpdcd --exclude="'.$this->args['exclude'].'" '.$this->args['source'].' \
			--recursive');
		system("./vendor/pdepend/pdepend/src/bin/pdepend \
			--summary-xml=".$this->args['output']."pdepend/summary.xml \
			--jdepend-chart=".$this->args['output']."pdepend/jdepend.svg \
			--overview-pyramid=".$this->args['output']."pdepend/pyramid.svg \
			--ignore=".$this->args['exclude']." \
			".$this->args['source']);
		*/

		return true;
	}
}

$pwt = new PWT();
