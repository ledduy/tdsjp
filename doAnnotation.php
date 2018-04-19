<?php

/*
    GOAL: for annotation of keyframes in the keyframe dir
    Requirement: keyframes are already extracted
    Step1: Scan keyframe dir to get list of videoID and list of videoID-keyframeID
    Step2: Generate link to annotate using LabelMeTool

    Step3: Check quality of annotations by using viewAnnotation2.php
*/

require_once "kl-IOTools.php";

/*
*** var to use with LabelMeTool - tool.htm
- objects=car,person,building - When popup bubble appears asking the user for the object name, the user selects one of these objects appearing as a drop-down list.
- collection=LabelMe - Uses the default LabelMe collection list. See below for setting up a new collection list.
- folder=MyLabelMeFolder - LabelMe folder where the image lives.
- image=image.jpg - LabelMe image to annotate.
- mode=f - Pressing "next image" button goes to next image in the folder.
*/

// list of available labels to be used with objects=xxx
$szLabelList = "noparking,neg-noparking,neg-noparkingx,limit40,limit50,greenguide,blueguide";

$nSamplingRate = 10;  // to reduce number of keyframes to show
$nShuffle = 0; // to shuffle the list before showing

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

// this code runs ONCE to generate list of keyframes for each videoID

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
        $szURL = sprintf("%s?vAction=1&vVideoID=%s&vSamplingRate=%d&vShuffle=%d", $_SERVER['PHP_SELF'], $szVideoID, $nSamplingRate, $nShuffle);
        printf("<P><B><A HREF='%s'>%s</A>\n", $szURL, $szVideoID);
    }
}

if($nAction ==1)
{
    $nShuffle = intval($_REQUEST["vShuffle"]);
    $nSamplingRate = intval($_REQUEST["vSamplingRate"]);

    $szVideoID = $_REQUEST["vVideoID"];
    printf("<H1>Annotation for video [%s]</H1>\n", $szVideoID);

    // load keyframes
    $szFPInputFN = sprintf("%s/%s.txt", $szInputDirName, $szVideoID);
    loadListFile($arKFList, $szFPInputFN);

    sort($arKFList);
    $nNumKF = count($arKFList);

    if($nShuffle)
    {
        shuffle($arKFList);
    }

    // sampling rate 10
    for($i=0; $i<$nNumKF; $i+=$nSamplingRate)
    {
        $szKeyFrameID = $arKFList[$i];
        // http://192.168.28.68/html/LabelMeAnnotationTool/tool.html?username=duy&collection=LabelMe&folder=20180224_03&image=20180224_03-000005.jpg
        $szCoreURL = "http://192.168.28.68/html/LabelMeAnnotationTool/tool.html?username=duy&collection=LabelMe&mode=f";
        $szURL = sprintf("%s&folder=%s&image=%s.jpg&objects=%s", $szCoreURL, $szVideoID, $szKeyFrameID, $szLabelList);
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
