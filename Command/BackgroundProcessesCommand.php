<?php
/**
 * Created by PhpStorm.
 * User: Luca Piccini <l.piccini@sintraconsulting.eu>
 * Date: 24/03/20
 * Time: 12:08
 */

namespace Sintra\TrainingBundle\Command;

use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ProductTest;
use ProcessManagerBundle\Logger\ProcessLogger;
use ProcessManagerBundle\Model\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use ProcessManagerBundle\Factory\ProcessFactoryInterface;

class BackgroundProcessesCommand extends AbstractCommand
{

    /**
     * @var ProcessFactoryInterface
     */
    private $processFactory;

    /**
     * @var ProcessLogger
     */
    private $processLogger;

    public function __construct(ProcessFactoryInterface $processFactory, ProcessLogger $processLogger, string $name = null)
    {
        $this->processFactory = $processFactory;
        $this->processLogger = $processLogger;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('sintra-training:background-process')
            ->setDescription('A background process');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = Carbon::now();

        if(class_exists('\ProcessManagerBundle\Model\Process')) { //Always check if the plugin is installed

            $logger = $this->processLogger;

            /* @var Process $process*/
            $process = $this->processFactory->createProcess(
                sprintf(
                    'Background Process  (%s): %s',
                    $date->formatLocalized('%A %d %B %Y'),
                    'Creating dummy Object'
                ),                                                  //Name
                'message',                                     //Type
                'Loading',                                          //Message Text
                100,                                                //Total Steps
                0                                                   //Current Step
            );

            $process->save();

        }


        sleep(5);

        if(class_exists('\ProcessManagerBundle\Model\Process')) {
            if($process instanceof \ProcessManagerBundle\Model\Process) {
                $process->progress(25,'First 5 seconds');
                $logger->info($process,'First 5 seconds');
                $process->save();
            }
        }

        sleep(5);

        if(class_exists('\ProcessManagerBundle\Model\Process')) {
            if($process instanceof \ProcessManagerBundle\Model\Process) {
                $process->progress(25, 'Next 5 Seconds');
                $logger->info($process,'Next 5 Seconds');
                $process->save();
            }
        }

        sleep(5);

        if(class_exists('\ProcessManagerBundle\Model\Process')) {
            if($process instanceof \ProcessManagerBundle\Model\Process) {
                $process->progress(25, 'Creating Object');
                $logger->info($process,'Creating Object');
                $process->save();
            }
        }

        $productTest = new ProductTest();



        $sku = 'test'.$date->getTimestamp();

        $parentObject = DataObject::getByPath("/ProductTest");

        $productTest->setParent($parentObject);

        $productTest->setSku($sku);
        $productTest->setKey($sku);
        $productTest->save();

        sleep(5);

        if(class_exists('\ProcessManagerBundle\Model\Process')) {
            if($process instanceof \ProcessManagerBundle\Model\Process) {
                $process->progress(25,'Saving Object');
                $logger->info($process,
                    sprintf('Saving Object with sku %s (id: %d)',$productTest->getSku(),$productTest->getId())
                );
                $process->setCompleted($date->getTimestamp());
                $process->save();
            }
        }

        sleep(5);

        if(class_exists('\ProcessManagerBundle\Model\Process')) {
            $logger->info($process,'This is an INFO');
            $logger->emergency($process,'This is an EMERGENCY');
            $logger->error($process,'This is an ERROR');
            $logger->alert($process,'This is an ALERT');
            $logger->critical($process,'This is a CRITICAL');
            $logger->warning($process,'This is a WARNING');
            $logger->debug($process,'This is a DEBUG');
            $logger->notice($process,'This is a NOTICE');
        }

        if(class_exists('\ProcessManagerBundle\Model\Process')) {
            if($process instanceof \ProcessManagerBundle\Model\Process) {
                $logger->info($process,'Process Completed');
                //$process->delete();
            }
        }
    }
}