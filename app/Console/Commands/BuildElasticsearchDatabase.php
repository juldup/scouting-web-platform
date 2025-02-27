<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BuildElasticsearchDatabase extends Command {
  
	protected $name = 'scouts:build-elasticsearch';
	protected $description = 'Fills the elasticsearch database with pages and documents from the website';
  
	public function fire() {
    ElasticsearchHelper::fillElasticsearchDatabase();
	}
  
}
