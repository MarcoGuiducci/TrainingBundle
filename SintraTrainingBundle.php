<?php

namespace Sintra\TrainingBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Sintra\TrainingBundle\Installer\SintraTrainingBundleInstaller;

class SintraTrainingBundle extends AbstractPimcoreBundle
{
    public function getInstaller()
    {
        return $this->container->get(SintraTrainingBundleInstaller::class);
    }

    public function getJsPaths()
    {
        return [
            '/bundles/sintratraining/js/pimcore/startup.js',
            '/bundles/sintratraining/js/pimcore/settings.js',
            '/bundles/sintratraining/js/pimcore/object/importcolumn/operator/FieldSetter.js'
        ];
    }

    public function getCssPaths()
    {
        return [
            '/bundles/sintratraining/css/icons.css'
        ];
    }
}