<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Provider\JobQueue;

use Aureja\JobQueue\Extension\Php\PhpJobFactory;
use Aureja\JobQueue\Extension\Shell\ShellJobFactory;
use Aureja\JobQueue\Extension\Symfony as SymfonyExtension;
use Aureja\JobQueue\JobQueue;
use Aureja\JobQueue\JobRestoreManager;
use Aureja\JobQueue\Provider\JobProvider;
use Aureja\JobQueue\Register\JobFactoryRegistry;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class JobQueueServiceProvider.
 *
 * @package Aureja\Provider\JobQueue
 */
class JobQueueServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        foreach ($this->getJobQueueDefaults() as $key => $value) {
            if (false === isset($container[$key])) {
                $container[$key] = $value;
            }
        }

        $container['aureja_job_queue.manager.restore'] = function () use ($container) {
            return new JobRestoreManager($container['aureja_job_queue.manager.report']);
        };

        $container['aureja_job_queue.job_factory.php'] = function () use ($container) {
            return new PhpJobFactory($container['aureja_job_queue.manager.report']);
        };

        $container['aureja_job_queue.job_factory.shell'] = function () use ($container) {
            return new ShellJobFactory($container['aureja_job_queue.manager.report']);
        };

        $container['aureja_job_queue.job_factory.symfony_command'] = function () use ($container) {;
            $config = $container['aureja_job_queue.job']['symfony'];

            return new SymfonyExtension\Command\CommandJobFactory(
                new SymfonyExtension\Command\CommandBuilder($config['console_dir'], $config['env']),
                $container['aureja_job_queue.manager.report']
            );
        };

        $container['aureja_job_queue.registry.factory'] = function () use ($container) {
            $registry = new JobFactoryRegistry();
            $registry->add($container['aureja_job_queue.job_factory.php'], 'php_job_factory');
            $registry->add($container['aureja_job_queue.job_factory.shell'], 'shell_job_factory');
            $registry->add($container['aureja_job_queue.job_factory.symfony_command'], 'symfony_command_job_factory');

            return $registry;
        };

        $container['aureja_job_queue.provider.job'] = function () use ($container) {
            return new JobProvider($container['aureja_job_queue.registry.factory']);
        };

        $container['aureja_job_queue'] = function () use ($container) {
            return new JobQueue(
                $container['aureja_job_queue.manager.configuration'],
                $container['aureja_job_queue.provider.job'] ,
                $container['aureja_job_queue.manager.report'],
                $container['aureja_job_queue.manager.restore'],
                $container['aureja_job_queue.queue'],
                $container['aureja_job_queue.reset']['timeout']
            );
        };
    }

    /**
     * @return array
     */
    protected function getJobQueueDefaults()
    {
        return [
            'aureja_job_queue.queue' => ['default'],
            'aureja_job_queue.reset' => [
                'timeout' => 600,
            ],
            'aureja_job_queue.job' => [
                'symfony' => [
                    'console_dir' => __DIR__ . '/../../../../',
                    'env' => 'prod',
                ],
            ],
        ];
    }
}
