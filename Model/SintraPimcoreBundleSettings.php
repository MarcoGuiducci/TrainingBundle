<?php

namespace Sintra\TrainingBundle\Model;

use Pimcore\Model\AbstractModel;
use Pimcore\Logger;

class SintraPimcoreBundleSettings extends AbstractModel {
 
    /**
     * @var int
     */
    public $id;
    
    /**
     * @var string
     */
    public $pimcoreurl;
    
    /**
     * @var string
     */
    public $exportfolder;
    
    /**
     * @var string
     */
    public $customnamespace;
 
    /**
     * get score by id
     *
     * @param $id
     * @return null|self
     */
    public static function getById($id) {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        }
        catch (\Exception $ex) {
            Logger::warn("SintraPimcoreBundleSettings with id $id not found");
        }
 
        return null;
    }

    /**
     * @return int
     */
    function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    function getPimcoreurl() {
        return $this->pimcoreurl;
    }

    /**
     * @return string
     */
    function getExportfolder() {
        return $this->exportfolder;
    }

    /**
     * @return string
     */
    function getCustomnamespace() {
        return $this->customnamespace;
    }

    /**
     * @param int $id
     */
    function setId(int $id) {
        $this->id = $id;
    }

    /**
     * @param string|null $pimcoreurl
     */
    function setPimcoreurl($pimcoreurl) {
        $this->pimcoreurl = $pimcoreurl;
    }

    /**
     * @param string|null $exportfolder
     */
    function setExportfolder($exportfolder) {
        $this->exportfolder = $exportfolder;
    }

    /**
     * @param string|null $customnamespace
     */
    function setCustomnamespace($customnamespace) {
        $this->customnamespace = $customnamespace;
    }


}