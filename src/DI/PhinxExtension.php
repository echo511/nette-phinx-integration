<?php

namespace Echo511\NettePhinxIntegration\DI;

use Echo511\NettePhinxIntegration\Config\ConfigFactory;
use Nette\DI\CompilerExtension;
use Phinx\Config\Config;
use Phinx\Console\Command\Breakpoint;
use Phinx\Console\Command\Create;
use Phinx\Console\Command\Migrate;
use Phinx\Console\Command\Rollback;
use Phinx\Console\Command\SeedCreate;
use Phinx\Console\Command\SeedRun;
use Phinx\Console\Command\Status;
use Phinx\Console\Command\Test;


class PhinxExtension extends CompilerExtension
{
    protected $defaults = [
        'paths' => [
            'migrations' => '%appDir%/db/migrations',
            'seeds' => '%appDir%/db/seeds',
        ]
    ];

    public function loadConfiguration()
    {
        $config = $this->getConfig($this->defaults);
        $containerBuilder = $this->getContainerBuilder();

        $phinxConfig = $containerBuilder->addDefinition($this->prefix('config'))
            ->setClass(Config::class);

        if (!isset($config['environments'])) {
            $phinxConfig->setFactory(ConfigFactory::class . '::createConfigFromNetteDatabase', ['@container', $config]);
        } else {
            $phinxConfig->setArguments([$config]);
        }

        $containerBuilder->addDefinition($this->prefix('commandCreate'))
            ->setClass(Create::class)
            ->addSetup('setName', ['db:migration:create'])
            ->addSetup('setConfig', ['@' . $this->prefix('config')])
            ->addTag('kdyby.console.command');

        $containerBuilder->addDefinition($this->prefix('commandMigrate'))
            ->setClass(Migrate::class)
            ->addSetup('setName', ['db:migrate'])
            ->addSetup('setConfig', ['@' . $this->prefix('config')])
            ->addTag('kdyby.console.command');

        $containerBuilder->addDefinition($this->prefix('commandRollback'))
            ->setClass(Rollback::class)
            ->addSetup('setName', ['db:rollback'])
            ->addSetup('setConfig', ['@' . $this->prefix('config')])
            ->addTag('kdyby.console.command');

        $containerBuilder->addDefinition($this->prefix('commandStatus'))
            ->setClass(Status::class)
            ->addSetup('setName', ['db:status'])
            ->addSetup('setConfig', ['@' . $this->prefix('config')])
            ->addTag('kdyby.console.command');

        $containerBuilder->addDefinition($this->prefix('commandBreakpoint'))
            ->setClass(Breakpoint::class)
            ->addSetup('setName', ['db:breakpoint'])
            ->addSetup('setConfig', ['@' . $this->prefix('config')])
            ->addTag('kdyby.console.command');

        $containerBuilder->addDefinition($this->prefix('commandTest'))
            ->setClass(Test::class)
            ->addSetup('setName', ['db:test'])
            ->addSetup('setConfig', ['@' . $this->prefix('config')])
            ->addTag('kdyby.console.command');

        $containerBuilder->addDefinition($this->prefix('commandSeedCreate'))
            ->setClass(SeedCreate::class)
            ->addSetup('setName', ['db:seed:create'])
            ->addSetup('setConfig', ['@' . $this->prefix('config')])
            ->addTag('kdyby.console.command');

        $containerBuilder->addDefinition($this->prefix('commandSeedRun'))
            ->setClass(SeedRun::class)
            ->addSetup('setName', ['db:seed:run'])
            ->addSetup('setConfig', ['@' . $this->prefix('config')])
            ->addTag('kdyby.console.command');

    }
}