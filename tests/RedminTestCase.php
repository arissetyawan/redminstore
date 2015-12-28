<?php namespace Redooor\Redminstore\Test;

use \Orchestra\Testbench\TestCase as TestBenchTestCase;

class RedminTestCase extends TestBenchTestCase
{
    /**
     * Overrides environment with in-memory sqlite database.
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = __DIR__ . '/../src';

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ));
    }
    
    /**
     * Sets up environment for each test.
     * Temporariliy increase memory limit, run migrations and set Mail::pretend to true.
     */
    public function setUp()
    {
        parent::setUp();

        ini_set('memory_limit', '400M'); // Temporarily increase memory limit to 400MB
        
        /**
         * By default, Laravel keeps a log in memory of all queries that have been run for
         * the current request. Disable logging for test to reduce memory.
         */
        \DB::connection()->disableQueryLog();

        // Migrate RedminPortal tables for test
        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__.'/../vendor/redooor/redminportal/src/database/migrations'),
        ]);

        \Mail::pretend(true);
    }

    /**
     * Points base path to testbench's fixture.
     */
    protected function getApplicationPaths()
    {
        $basePath = realpath(__DIR__.'/../vendor/orchestra/testbench/fixture');

        return array(
            'app'     => "{$basePath}/app",
            'public'  => "{$basePath}/public",
            'base'    => $basePath,
            'storage' => "{$basePath}/storage",
        );
    }
    
    /**
     * Appends additional ServiceProvider for the test.
     */
    protected function getPackageProviders($app)
    {
        return [
            'Redooor\Redminstore\RedminstoreServiceProvider',
            'Redooor\Redminportal\RedminportalServiceProvider',
            'Illuminate\Html\HtmlServiceProvider'
        ];
    }

    /**
     * Appends additional Aliases for the test.
     */
    protected function getPackageAliases($app)
    {
        return [
            'Redminstore' => 'Redooor\Redminstore\Facades\Redminstore',
            'Redminportal' => 'Redooor\Redminportal\Facades\Redminportal',
            'Form'      => 'Illuminate\Html\FormFacade',
            'HTML'      => 'Illuminate\Html\HtmlFacade'
        ];
    }
}
