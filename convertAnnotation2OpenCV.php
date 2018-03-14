<?php

# written by Duy-Dinh Le
# last update: Mar 12, 2018


/*

# copy annotation from server to local
Les-MacBook-Pro:tdsjp ledinhduy$ scp -r mmlab@192.168.28.68:/var/www/html/LabelMeAnnotationTool/Annotations ./
*/
require_once "kl-IOTools.php";


# copy data 
$szCmd = sprintf("cp -r /var/www/html/LabelMeAnnotationTool/Annotations ./"
);
printf("%s\n", $szCmd);
exec($szCmd);

# run python code
$arLabels = array('noparking', 'limit40', 'limit50', 'neg-noparking'
);

//
$arAllVideos = array('MAH00019', '20180224_01', '20180224_02', '20180224_03', '20180306_01', '20180306_02', '20180306_03', 'traffic_sign_video2802');

$arTrainVideos1 = array('MAH00019', '20180224_01', '20180224_02', '20180306_01', '20180306_03');
$szTrial1 = 'Train1';
makeDir($szTrial1);

$arTrainVideos = $arTrainVideos1;

$annDir = "Annotations";

$szKeyFrameDir = "/home/mmlab/mbase/tdsjp/keyframe";

//print_r($arLabels); exit();
foreach($arLabels as $labelName)
{
    $arAll = array(); // include bounding box
    $arAll2 = array(); // list of keyframe only
    
    printf("### Processing label [%s]\n", $labelName
);
    //exit();
    foreach($arTrainVideos as $videoID)
    {
        $szCmd = sprintf("python convertLM2OpenCV.py %s %s %s", $labelName, $annDir, $videoID);
        printf("%s\n", $szCmd);
        exec($szCmd);

        $szFileName = sprintf("%s-%s.dat", $labelName, $videoID);
        loadListFile($arData, $szFileName);

        $imgDir = sprintf("%s/%s", $szTrial1, $labelName);
        makeDir($imgDir);

        // copy positive images to this dir
        foreach($arData as $szLine)
        {
            // noparking/MAH00019-058720.jpg 1 685 325 76 79

            $arTmp = explode(' ', $szLine);
            $arAll2[] = sprintf("%s/%s"
, $szTrial1, trim($arTmp[0])); 
    
            $arAll[] = sprintf("%s", $szLine);

            $arTmp = explode('/', $szLine);

            $szTmp = $arTmp[1];
            $arTmpx = explode(' ', $szTmp);
            $imgName = $arTmpx[0];

            $fullPathImg = sprintf("%s/%s/%s", $szKeyFrameDir, $videoID, $imgName);

            // for debugging in local
            //$szCmd = sprintf("sshpass -p 'abcd123' scp mmlab@192.168.28.68:%s %s ", $fullPathImg, $imgDir);
            
            $szCmd = sprintf("cp %s %s ", $fullPathImg, $imgDir);
            printf("%s\n", $szCmd);
            exec($szCmd);

            //break;
            //exit();
        }

    }

    $szFileName = sprintf("%s/%s.dat", $szTrial1, $labelName);
    saveDataFromMem2File($arAll, $szFileName);

    $szFileName = sprintf("%s/%s.dat2", $szTrial1, $labelName);
    saveDataFromMem2File($arAll2, $szFileName);

    //break;

}

?>
