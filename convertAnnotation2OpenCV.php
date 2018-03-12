<?php

# written by Duy-Dinh Le
# last update: Mar 12, 2018


/*

# copy annotation from server to local
Les-MacBook-Pro:tdsjp ledinhduy$ scp -r mmlab@192.168.28.68:/var/www/html/LabelMeAnnotationTool/Annotations ./
*/
require_once "kl-IOTools.php";

# run python code
$arLabels = array('noparking', 'limit40', 'limit50');
$arVideos = array('MAH00019', '20180224_01', '20180224_02', '20180224_03', '20180306_01', '20180306_02', '20180306_03', 'traffic_sign_video2802');

$annDir = "Annotations";

$szKeyFrameDir = "/home/mmlab/mbase/tdsjp/keyframe";
foreach($arLabels as $labelName)
{
    $arAll = array();
    foreach($arVideos as $videoID)
    {
        $szCmd = sprintf("python convertLM2OpenCV.py %s %s %s", $labelName, $annDir, $videoID);
        printf("%s\n", $szCmd);
        exec($szCmd);

        $szFileName = sprintf("%s-%s.dat", $labelName, $videoID);
        loadListFile($arData, $szFileName);

        $imgDir = $labelName;
        makeDir($imgDir);

        // copy positive images to this dir
        foreach($arData as $szLine)
        {
            // noparking/MAH00019-058720.jpg 1 685 325 76 79

            $arAll[] = $szLine;

            $arTmp = explode('/', $szLine);

            $szTmp = $arTmp[1];
            $arTmpx = explode(' ', $szTmp);
            $imgName = $arTmpx[0];

            $fullPathImg = sprintf("%s/%s/%s", $szKeyFrameDir, $videoID, $imgName);

            $szCmd = sprintf("sshpass -p 'abcd123' scp -r mmlab@192.168.28.68:%s %s ", $fullPathImg, $imgDir);
            printf("%s\n", $szCmd);
            exec($szCmd);

            //break;
            //exit();
        }

    }

    $szFileName = sprintf("%s.dat", $labelName);
    saveDataFromMem2File($arAll, $szFileName);
    //break;

}

?>
