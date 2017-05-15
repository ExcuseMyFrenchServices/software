<?php 
namespace App\Services;

use Illuminate\Support\Facades\DB;

class SaveDatabase
{
	public function SaveDatabase()
	{
		// dump the database to backup/test.sql
		$shellProcessor = new McCool\DatabaseBackup\Processors\ShellProcessor(new Symfony\Component\Process\Process(''));
		$dumper = new McCool\DatabaseBackup\Dumpers\MysqlDumper($shellProcessor, env('DB_HOST'), 3306, env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'), 'backup/'.date('d-m-Y').'EMFSoftware.sql');

		$backup = new McCool\DatabaseBackup\BackupProcedure($dumper);
		$backup->backup();
	}
}