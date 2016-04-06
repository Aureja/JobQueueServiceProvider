<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Provider\JobQueue\Tests;

use Aureja\JobQueue\Extension\Php\PhpJobFactory;
use Aureja\JobQueue\Extension\Shell\ShellJobFactory;
use Aureja\JobQueue\Extension\Symfony\Command\CommandJobFactory;
use Aureja\JobQueue\JobQueue;
use Aureja\JobQueue\JobRestoreManager;
use Aureja\JobQueue\Model\Manager\JobConfigurationManagerInterface;
use Aureja\JobQueue\Model\Manager\JobReportManagerInterface;
use Aureja\JobQueue\Provider\JobProvider;
use Aureja\JobQueue\Register\JobFactoryRegistry;
use Aureja\Provider\JobQueue\JobQueueServiceProvider;
use PHPUnit_Framework_TestCase as TestCase;
use Pimple\Container;

/**
 * Class JobQueueServiceProviderTest.
 *
 * @package Aureja\Provider\JobQueue\Tests
 */
class JobQueueServiceProviderTest extends TestCase
{
    public function testJobRestoreManager()
    {
        $app = $this->createMockDefaultApp();

        $this->assertInstanceOf(JobRestoreManager::class, $app['aureja_job_queue.manager.restore']);
    }

    public function testPhpJobFactory()
    {
        $app = $this->createMockDefaultApp();

        $this->assertInstanceOf(PhpJobFactory::class, $app['aureja_job_queue.job_factory.php']);
    }

    public function testShellJobFactory()
    {
        $app = $this->createMockDefaultApp();

        $this->assertInstanceOf(ShellJobFactory::class, $app['aureja_job_queue.job_factory.shell']);
    }

    public function testSymfonyCommandJobFactory()
    {
        $app = $this->createMockDefaultApp();

        $this->assertInstanceOf(CommandJobFactory::class, $app['aureja_job_queue.job_factory.symfony_command']);
    }

    public function testJobFactoryRegistry()
    {
        $app = $this->createMockDefaultApp();

        $this->assertInstanceOf(JobFactoryRegistry::class, $app['aureja_job_queue.registry.factory']);
    }

    public function testJobProvider()
    {
        $app = $this->createMockDefaultApp();

        $this->assertInstanceOf(JobProvider::class, $app['aureja_job_queue.provider.job']);
    }

    public function testJobQueue()
    {
        $app = $this->createMockDefaultApp();

        $this->assertInstanceOf(JobQueue::class, $app['aureja_job_queue']);
    }

    /**
     * @expectedException \Aureja\Provider\JobQueue\Exception\ServiceNotFoundException
     * @expectedExceptionMessage Not found service "aureja_job_queue.manager.configuration".
     */
    public function testJobConfigurationManagerNonExistService()
    {
        $container = new Container();
        $container->register(new JobQueueServiceProvider());
    }

    /**
     * @expectedException \Aureja\Provider\JobQueue\Exception\ServiceNotFoundException
     * @expectedExceptionMessage Not found service "aureja_job_queue.manager.report".
     */
    public function testJobReportManagerNonExistService()
    {
        $container = new Container();

        $container['aureja_job_queue.manager.configuration'] = function () use ($container) {
            return $this->getMock(JobConfigurationManagerInterface::class);
        };

        $container->register(new JobQueueServiceProvider());
    }

    /**
     * @return Container
     */
    private function createMockDefaultApp()
    {
        $container = new Container();

        $container['aureja_job_queue.manager.configuration'] = function () use ($container) {
            return $this->getMock(JobConfigurationManagerInterface::class);
        };

        $container['aureja_job_queue.manager.report'] =  function () use ($container) {
            return $this->getMock(JobReportManagerInterface::class);
        };

        $container->register(new JobQueueServiceProvider());

        return $container;
    }
}
