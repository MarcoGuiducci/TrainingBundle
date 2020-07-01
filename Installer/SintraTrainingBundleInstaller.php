<?php

namespace Sintra\TrainingBundle\Installer;

use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Db\ConnectionInterface;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Pimcore\Migrations\MigrationManager;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\DataObject\Folder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class SintraTrainingBundleInstaller extends MigrationInstaller{

    /**
     * @var string
     */
    private $installSourcesPath;

    /**
     * @var array
     */
    private $classesToInstall = [
        'ProductTest' => 'SC_ProductTest',
    ];

    /**
     * @var array
     */
    private $tablesToInstall = array(
        "sintrapimcorebundle_settings" =>
            "CREATE TABLE IF NOT EXISTS `sintrapimcorebundle_settings` (
                `id` INT NOT NULL,
                `pimcoreurl` VARCHAR(255) NULL,
                `exportfolder` VARCHAR(255) NULL,
                `customnamespace` VARCHAR(255) NULL,
                PRIMARY KEY (`id`))
              ENGINE = InnoDB
              MAX_ROWS = 1;
              
            INSERT INTO sintrapimcorebundle_settings (`id`) VALUES(1);",
        
        "importbundle_settings" =>
            "CREATE TABLE IF NOT EXISTS `importbundle_settings` (
                `id` INT NOT NULL,
                `pimcoreurl` VARCHAR(255) NULL,
                `customnamespace` VARCHAR(255) NULL,
                PRIMARY KEY (`id`))
              ENGINE = InnoDB
              MAX_ROWS = 1;
              
            INSERT INTO importbundle_settings (`id`) VALUES(1);"
    );

    public function __construct(
        BundleInterface $bundle,
        ConnectionInterface $connection,
        MigrationManager $migrationManager
    ) {
        $this->installSourcesPath = __DIR__ . '/../Resources/install';
        parent::__construct($bundle, $connection, $migrationManager);
    }

    public function migrateInstall(Schema $schema, Version $version) {
        $this->installClasses();
        $this->installTables($schema, $version);

        $configurationFolder = "ProductTest";
        $folder = Folder::getByPath("/".$configurationFolder);
        if($folder == null){
            $folder = new Folder();

            $folder->setKey($configurationFolder);
            $folder->setParent(Folder::getByPath("/"));
            $folder->save();
        }
    }

    public function migrateUninstall(Schema $schema, Version $version) {
        $this->uninstallTables($schema);
    }

    private function installTables(Schema $schema, Version $version)
    {
        foreach ($this->tablesToInstall as $name => $statement) {
            if ($schema->hasTable($name)) {
                $this->outputWriter->write(sprintf(
                    '     <comment>WARNING:</comment> Skipping table "%s" as it already exists',
                    $name
                ));

                continue;
            }

            $version->addSql($statement);
        }
    }

    private function uninstallTables(Schema $schema)
    {
        foreach (array_keys($this->tablesToInstall) as $table) {
            if (!$schema->hasTable($table)) {
                $this->outputWriter->write(sprintf(
                    '     <comment>WARNING:</comment> Not dropping table "%s" as it doesn\'t exist',
                    $table
                ));

                continue;
            }

            $schema->dropTable($table);
        }
    }

    private function installClasses()
    {
        $classes = $this->getClassesToInstall();

        $mapping = $this->classesToInstall;

        foreach ($classes as $key => $path) {
            $class = ClassDefinition::getByName($key);

            if ($class) {
                $this->outputWriter->write(sprintf(
                    '     <comment>WARNING:</comment> Skipping class "%s" as it already exists',
                    $key
                ));

                continue;
            }

            $class = new ClassDefinition();

            $classId = $mapping[$key];

            $class->setName($key);
            $class->setId($classId);

            $data = file_get_contents($path);
            $success = Service::importClassDefinitionFromJson($class, $data, false, true);

            if (!$success) {
                throw new AbortMigrationException(sprintf(
                    'Failed to create class "%s"',
                    $key
                ));
            }
        }
    }

    private function getClassesToInstall(): array
    {
        $result = [];
        foreach (array_keys($this->classesToInstall) as $className) {
            $filename = sprintf('class_%s_export.json', $className);
            $path = $this->installSourcesPath . '/class_sources/' . $filename;
            $path = realpath($path);

            if (false === $path || !is_file($path)) {
                throw new AbortMigrationException(sprintf(
                    'Class export for class "%s" was expected in "%s" but file does not exist',
                    $className,
                    $path
                ));
            }

            $result[$className] = $path;
        }

        return $result;
    }

}
