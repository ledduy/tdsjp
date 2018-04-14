<?php

require_once "kl-IOTools.php";

if(isset($_REQUEST["vAction"]))
{
    $nAction = $_REQUEST["vAction"];
}
else
{
    $nAction = 0;
}

// collect dirs - run from console tdsjp/code: // php -f xxx.php

$szInputDirName = "/home/mmlab/mbase/tdsjp/keyframe"
;
//printf("<P>Scanning dir [%s]\n", $szInputDirName);
$arDirs = collectDirsInOneDir($szInputDirName);

/*
$nTotalFiles = 0;
foreach($arDirs as $szDir)
{
    printf("<P>Collecting files in [%s]\n", $szDir);
    $szFPDir = sprintf("%s/%s", $szInputDirName, $szDir);
    
    //$arList = collectFilesInOneDir($szFPDir);
    //printf("<BR>Num files: [%d]\n", size($arList));
    
    $szFPOutputFN = sprintf("%s/%s.txt", $szInputDirName, $szDir);
    
    //saveDataFromMem2File($arList, $szFPOutputFN, "wt");
    //$nTotalFiles += count($arList);
}
//printf("<P>DONE! Total keyframes: [%d]\n", $nTotalFiles);
*/

// display links to annotation videoID
if($nAction == 0)
{
    printf("<H1>List of VideoIDs</H1>\n");
    foreach($arDirs as $szVideoID)
    {
        $szURL = sprintf("%s?vAction=1&vVideoID=%s", $_SERVER['PHP_SELF'], $szVideoID);
        printf("<P><B><A HREF='%s'>%s</A>\n", $szURL, $szVideoID);
    }
}

if($nAction ==1)
{
    $szVideoID = $_REQUEST["vVideoID"];
    printf("<H1>Annotation for video [%s]</H1>\n", $szVideoID);
    
    // load keyframes
    $szFPInputFN = sprintf("%s/%s.txt", $szInputDirName, $szVideoID);
    loadListFile($arKFList, $szFPInputFN);
    sort($arKFList);
    $nNumKF = count($arKFList);
    
    // sampling rate 10
    for($i=0; $i<$nNumKF; $i+=10)
    {
        $szKeyFrameID = $arKFList[$i];
        // http://192.168.28.68/html/LabelMeAnnotationTool/tool.html?username=duy&collection=LabelMe&folder=20180224_03&image=20180224_03-000005.jpg
        $szCoreURL = "http://192.168.28.68/html/LabelMeAnnotationTool/tool.html?username=duy&collection=LabelMe&mode=f";
        $szURL = sprintf("%s&folder=%s&image=%s.jpg", $szCoreURL, $szVideoID, $szKeyFrameID);
        printf("<P><A HREF='%s' TARGET=_blank>%s</A>\n", $szURL, $szKeyFrameID);
    }
}

if($nAction == 2)
{
    $szVideoID = $_REQUEST["vVideoID"];
    $szKeyFrameID = $_REQUEST["vKeyFrameID"];
    printf("<H1>Annotation for [%s] . [%s]</H1>\n", $szVideoID, $szKeyFrameID);
}

?>