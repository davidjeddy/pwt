<?php
/**
 * `PHP Workflow Tools` is a class that invokes a set of PHP QA tools using a single CLI command
 *
 * @package davidjeddy
 * @subpackage  PWT
 * @author  David J Eddy <me@davidjeddy.com>
 * @version  0.2
 */
require_once('./vendor/autoload.php');

/**
 * Welcome to PWT...
 *
 * @author  David Eddy <me@davidjeddy.com>
 */
class pwt
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
            "source::",
        ];

        if ($this->mapCLItoProperties()) {
            return $this->doIt();
        }

        return false;
    }

    /**
     * Map our CLI options to the CLI options of each tool
     * 
     * @return boolean
     */
    private function mapCLItoProperties()
    {
        // get provided CLI options
        $this->args = getopt(null, $this->lopts);


        // set the default output dir
        if (isset($this->args["output"])) {
        	if (file_exists($this->args["output"])) {
        		system("mkdir ".$this->args['source']."_archives/");
        		system ("tar -zcf ".$this->args['source']."_archives/".time().".tar.gz ".$this->args['output']." && rm -rf ".$this->args['output']);
        	}
            
            system('mkdir '.$this->args["output"]);

            return true;
        }

        return false;
    }

    /**
     * Execute CLI commands
     * @return boolean
     */
    private function doIt()
    {
        system("mkdir ".$this->args['output']."pdepend/ && ./vendor/pdepend/pdepend/src/bin/pdepend \
            --summary-xml=".$this->args['output']."pdepend/summary.xml \
            --jdepend-chart=".$this->args['output']."pdepend/jdepend.svg \
            --overview-pyramid=".$this->args['output']."pdepend/pyramid.svg \
            --ignore=".$this->args['exclude']." \
            ".$this->args['source']);
        system("mkdir ".$this->args['output']."phpdocs/ && php ./vendor/phpdocumentor/phpdocumentor/bin/phpdoc \
        	--directory=".$this->args['source']." \
        	--target=./".$this->args['output']."phpdocs/ \
        	--ignore=".$this->args['exclude']."/ \
        	--log=".$this->args['output']."/phpdocs/logs \
        	--progressbar");
        system("mkdir ".$this->args['output']."phploc/ && php ./vendor/phploc/phploc/phploc \
        	--log-csv ".$this->args['output']."/phploc/phploc.csv \
        	--git-repository ".$this->args['source']." \
        	src --progress");
        system("mkdir ".$this->args['output']."phpcpd/ && php ./vendor/sebastian/phpcpd/phpcpd \
        	--exclude=".$this->args['exclude']." ".$this->args['source']." \
            --progress > ".$this->args['output']."/phpcpd/phpcpd.log");
        system("mkdir ".$this->args['output']."phpdcd/ && php ./vendor/sebastian/phpdcd/phpdcd \
        	--exclude=".$this->args['exclude']." ".$this->args['source']." \
            --recursive > ".$this->args['output']."/phpdcd/phpdcd.log");

        return true;
    }
}

new PWT();
