<?php

namespace Sintra\TrainingBundle\Controller;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sintra\TrainingBundle\Model\SintraPimcoreBundleSettings;
use Sintra\TrainingBundle\Model\ImportBundleSettings;

/**
 * @Route("/settings")
 */
class SettingsController extends AdminController
{
    /**
     * @Route("/get-bundles-settings")
     */
    public function getBundlesSettingsAction(Request $request)
    {
        $response = [];
        
        if(class_exists("Sintra\\TrainingBundle\\Model\\SintraPimcoreBundleSettings")){
            $sintraPimcoreBundleSettings = SintraPimcoreBundleSettings::getById(1);
            
            if($sintraPimcoreBundleSettings !== null){
                $response[] = array(
                    "name" => "SintraPimcoreBundle",
                    "label" => "SintraPimcoreBundle",
                    "data" => array(
                        "pimcoreurl" => $sintraPimcoreBundleSettings->getPimcoreurl() ?? "",
                        "exportfolder" => $sintraPimcoreBundleSettings->getExportfolder() ?? "",
                        "customnamespace" => $sintraPimcoreBundleSettings->getCustomnamespace() ?? "",
                    )
                );
            }
        }
        
        if(class_exists("Sintra\\TrainingBundle\\Model\\ImportBundleSettings")){
            $importBundleSettings = ImportBundleSettings::getById(1);
            
            if($importBundleSettings !== null){
                $response[] = array(
                    "name" => "ImportBundle",
                    "label" => "ImportBundle",
                    "data" => array(
                        "pimcoreurl" => $importBundleSettings->getPimcoreurl() ?? "",
                        "customnamespace" => $importBundleSettings->getCustomnamespace() ?? "",
                    )
                );
            }
        }
        
        return $this->adminJson(["data" => $response]);
    }
    
    /**
     * @Route("/save-bundles-settings", methods={"POST"})
     */
    public function saveBundlesSettingsAction(Request $request){
        $values = $request->get("values");
        
        $settings = json_decode($values,true);
        
        $response = array('success' => true);
        
        foreach ($settings as $bundle => $bundleSettings) {
            switch ($bundle) {
                case "SintraPimcoreBundle":
                    $this->saveSintraPimcoreBundleSettings($bundleSettings);
                    break;
                
                case "ImportBundle":
                    $this->saveImportBundleSettings($bundleSettings);
                    break;
                
                default:
                    $response["success"] = false;
                    $response["message"] = "invalid_bundle_name";
            }
        }
        
        return $this->adminJson($response);
    }
    
    private function saveSintraPimcoreBundleSettings(array $bundleSettings){
        $sintraPimcoreBundleSettings = SintraPimcoreBundleSettings::getById(1);
            
        if($sintraPimcoreBundleSettings !== null){
            foreach ($bundleSettings as $key => $value) {
                $setMethod = "set".ucfirst($key);
                $sintraPimcoreBundleSettings->$setMethod($value);
            }
            
            $sintraPimcoreBundleSettings->save();
        }
    }
    
    private function saveImportBundleSettings(array $bundleSettings){
        $importBundleSettings = ImportBundleSettings::getById(1);
            
        if($importBundleSettings !== null){
            foreach ($bundleSettings as $key => $value) {
                $setMethod = "set".ucfirst($key);
                $importBundleSettings->$setMethod($value);
            }
            
            $importBundleSettings->save();
        }
    }
}
