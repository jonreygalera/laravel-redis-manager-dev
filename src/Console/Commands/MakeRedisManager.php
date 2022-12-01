<?php

namespace Jonreyg\LaravelRedisManager\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;

class MakeRedisManager extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:redismanager {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Redis Manager';


    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'RedisManager';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../Stubs/redis-manager.stub';
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        return $this->replaceAccessor($name);
    }

    /**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace . "\RedisManager";
	}

    public function handle() {        
        parent::handle();
    }

    public function replaceAccessor($name)
    {
        $namespace = explode('\\', $this->argument('name'));
        
        $manager_name = end($namespace);
        $folder = current(explode('_', \Str::snake($manager_name)));

        $stub = parent::buildClass($name);

        return str_replace('folder_name', $folder, $stub);
    }
}
