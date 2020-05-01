<?php
namespace Newelement\Locations\Commands;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Newelement\Locations\Traits\Seedable;
use Newelement\Locations\LocationsServiceProvider;

class UpdateCommand extends Command
{
    use Seedable;

    protected $seedersPath = __DIR__.'/../../publishable/database/seeds/';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'locations:update';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the Locations package';

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production', null],
            ['with-data', null, InputOption::VALUE_NONE, 'Install with default data', null],
        ];
    }

    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }
        return 'composer';
    }
    public function fire(Filesystem $filesystem)
    {
        return $this->handle($filesystem);
    }

    public function handle(Filesystem $filesystem)
    {
        $this->info('Updating assets, database, views and config files...');

        $this->info('Migrating any database changes...');
        $this->call('migrate', ['--force' => $this->option('force')]);

        $this->info('Dumping the autoloaded files and reloading all new files...');
        $composer = $this->findComposer();
        $process = new Process([$composer.' dump-autoload']);
        $process->setTimeout(null);
        $process->setWorkingDirectory(base_path())->run();

        $initData = $this->ask('Do you want to update the Locations theme views? CAUTION this will overwrite any views you may have altered. If you do not update the views you may need to update them manually. See documentation for more info. [Y/N]');

        if( $initData === 'y' || $initData === 'Y' ){
            $this->call('vendor:publish', ['--provider' => 'Newelement\Locations\LocationsServiceProvider', '--tag' => 'views', '--force' => true ]);
        }
        $this->call('vendor:publish', ['--provider' => 'Newelement\Locations\LocationsServiceProvider', '--tag' => 'adminviews', '--force' => true ]);

        $this->info('Updating assets...');
        $this->call('vendor:publish', ['--provider' => 'Newelement\Locations\LocationsServiceProvider', '--tag' => 'public', '--force' => true ]);

        $this->info('Clearing application cache...');
        \Storage::disk('public')->delete('assets/css/all.css');
        \Storage::disk('public')->delete('assets/js/all.js');
        $this->call('cache:clear');

        $this->info('Successfully updated Locations.');

    }
}
