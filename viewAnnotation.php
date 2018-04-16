<?php

/*
- given a labelName and 
*/

if (extension_loaded('gd') && function_exists('gd_info')) {
    echo "PHP GD library is installed on your web server";
}
else {
    echo "PHP GD library is NOT installed on your web server";
}

phpinfo();

require_once "kl-IOTools.php";

$szLabelName = $_REQUEST["labelName"] = "limit50";
$szTrialName = $_REQUEST["trialName"] = "Train1";


$szCodeDir = "/home/mmlab/mbase/tdsjp/code"
;
$szAnnDir = sprintf("%s/%s", $szCodeDir, $szTrialName)
;

// /home/mmlab/mbase/tdsjp/code/Train1/limit50.dat
$szAnnFile = sprintf("%s/%s.dat", $szAnnDir, $szLabelName);
loadListFile($arList, $szAnnFile);

printf("<H1>Annotations for [%s] - [%s] </H1>\n", $szTrialName, $szLabelName
);

foreach($arList as $szLine)
{
    
    // limit50/20180306_01-028980.jpg 1 477 186 70 76
    $arTmp = explode(" ", $szLine);
     
    $szKeyFrameIDx = trim($arTmp[0]);
    
    //printf("<BR>%s\n", $szKeyFrameIDx);
    $arTmp1 = explode("/", $szKeyFrameIDx);
    
    $szKeyFrameID = trim($arTmp1[1]);
    //printf("<BR>%s\n", $szKeyFrameID);
    
    $arTmp2 = explode("-", $szKeyFrameID);
    $szVideoID = trim($arTmp2[0]);
    //printf("<BR>%s\n", $szVideoID);
    
    // LabelMeAnnotationTool/Images/$VIDEOID
    $szURL = sprintf("LabelMeAnnotationTool/Images/%s/%s", $szVideoID, $szKeyFrameID);
    
    printf("<BR><IMG SRC='%s'>\n", $szURL);
    
    //break;
    
}
?>