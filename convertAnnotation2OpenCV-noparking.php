<?php

# written by Duy-Dinh Le
# last update: Apr 12, 2018

# update for neg-noparking and neg-noparkingx
# update for BOOTSTRAPPING --> special treatment for neg-noparkingx - use bounding box to crop into new image

/*

# copy annotation from server to local
Les-MacBook-Pro:tdsjp ledi$ scp -r mmx@192.168.28.6x:/var/www/html/LabelMeAnnotationTool/Annotations ./
*/


/*
    Step 1: Parse annotation data from videos
    Step 2: Copy keyframes having at least one bounding box to trialName/labelName
    Step 3: For neg-noparkingx - crop bounding box image to trialName/labelName
    Step 4: For annotation of positive images --> use trialName/labelName.dat --> noparking/MAH00019-058720.jpg 1 685 325 76 79
    Steop 5:
*/

require_once "kl-IOTools.php";

# copy data to tdsjp/code
$szCmd = sprintf("cp -r /var/www/html/LabelMeAnnotationTool/Annotations ./");
printf("%s\n", $szCmd);
exec($szCmd);

# run python code

$arLabels = array('noparking', 'neg-noparking', 'neg-noparkingx');
$arLabelBS = array('neg-noparkingx', 'neg-noparking');
//
$arAllVideos = array('MAH00019', '20180224_01', '20180224_02', '20180224_03', '20180306_01', '20180306_02', '20180306_03', 'traffic_sign_video2802');

$arTrainVideos1 = array('MAH00019', '20180224_01', '20180224_02', '20180306_01', '20180306_03');

$szTrial = 'Train2';
makeDir($szTrial);

$arTrainVideos = $arTrainVideos1;

$annDir = "Annotations";

$szKeyFrameDir = "/home/mmlab/mbase/tdsjp/keyframe";

//print_r($arLabels); exit();

$arAllNeg = array(); // to merge neg-noparking and neg-noparkingx
foreach($arLabels as $labelName)
{
    $arPos = array(); // include bounding box
    $arNeg = array(); // list of keyframe only
    $arAllCmd = array(); // neg image selected by bootstrapping

    printf("### Processing label [%s]\n", $labelName);
    //exit();
    foreach($arTrainVideos as $videoID)
    {
        $szCmd = sprintf("python convertLM2OpenCV.py %s %s %s", $labelName, $annDir, $videoID);
        printf("%s\n", $szCmd);
        exec($szCmd);

        $szFileName = sprintf("%s-%s.dat", $labelName, $videoID);
        loadListFile($arData, $szFileName);

        $imgDir = sprintf("%s/%s", $szTrial, $labelName);
        makeDir($imgDir);

        // copy positive images to this dir
        foreach($arData as $szLine)
        {
            // noparking/MAH00019-058720.jpg 1 685 325 76 79

            $arTmp = explode(' ', $szLine);

            $szTmpName = trim($arTmp[0]);

            $left = intval($arTmp[2]);
            $top = intval($arTmp[3]);
            $width = intval($arTmp[4]);
            $height = intval($arTmp[5]);


            $arTmp = explode('/', $szLine);

            $szTmp = $arTmp[1];
            $arTmpx = explode(' ', $szTmp);
            $imgName = $arTmpx[0];

            $imgNameShort = str_replace(".jpg", "", $imgName);

            $fullPathImg = sprintf("%s/%s/%s", $szKeyFrameDir, $videoID, $imgName);

            // for debugging in local
            //$szCmd = sprintf("sshpass -p 'abcd123' scp mmlab@192.168.28.68:%s %s ", $fullPathImg, $imgDir);
            $szCmd = sprintf("cp %s %s ", $fullPathImg, $imgDir);
            printf("%s\n", $szCmd);
            exec($szCmd);

            if($labelName == "noparking")
            {
                // use for opencv_createtrainingsamples for vec (positive samples)
                $arPos[] = sprintf("%s", $szLine);  # duong dan tuong doi noparking/xxx
            }
            else
            {
                if($labelName == "neg-noparking")  // neg-noparking --> default bounding box is whole image
                {

                    // whole image = 1920x1080
                    $left = 100;
                    $top = 100;
                    $width = 1600;
                    $height = 700;

                    $outDir = $imgDir;
                    $szParam = sprintf("%s %s %s %s %s %s %s", $imgNameShort, $imgDir, $left, $top, $width, $height, $outDir);
                    $szCmd = sprintf("python cropRect4NegImages.py %s", $szParam);
                    printf("%s\n", $szCmd);
                    $arAllCmd[] = $szCmd;
                    exec($szCmd);

                    // outputfile = '{}/{}-{}-{}-{}-{}-neg.jpg'.format(out_dir, img_name, left, top, width, height)

                    $szCropFN = sprintf("%s-%s-%s-%s-%s-neg.jpg", $imgNameShort, $left, $top, $width, $height);

                    $arNeg[] = sprintf("%s/%s/%s", $szTrial, $labelName, $szCropFN);

                    $arAllNeg[] = sprintf("%s/%s/%s", $szTrial, $labelName, $szCropFN);
                }
                else
                {
                    if($labelName == "neg-noparkingx")  // neg-noparkingx --> bounding box is used to crop
                    {
                        // special treatment for bootstrapping - neg-noparkingx
                        // print('Usage {} imageName imageDir x y width height outDir'.format(sys.argv[0]))
                        $outDir = $imgDir;
                        $szParam = sprintf("%s %s %s %s %s %s %s", $imgNameShort, $imgDir, $left, $top, $width, $height, $outDir);
                        $szCmd = sprintf("python cropRect4NegImages.py %s", $szParam);
                        printf("%s\n", $szCmd);
                        $arAllCmd[] = $szCmd;
                        exec($szCmd);

                        // outputfile = '{}/{}-{}-{}-{}-{}-neg.jpg'.format(out_dir, img_name, left, top, width, height)

                        $szCropFN = sprintf("%s-%s-%s-%s-%s-neg.jpg", $imgNameShort, $left, $top, $width, $height);

                        $arNeg[] = sprintf("%s/%s/%s", $szTrial, $labelName, $szCropFN);

                        $arAllNeg[] = sprintf("%s/%s/%s", $szTrial, $labelName, $szCropFN);
                    }
               }

            }

            //break;
            //exit();
        }

        // move to szTrial
        $szCmd = sprintf("mv %s %s", $szFileName, $szTrial);
        exec($szCmd);
    }

    $szFileName = sprintf("%s/%s.dat", $szTrial, $labelName);
    if(count($arPos))
        saveDataFromMem2File($arPos, $szFileName);

    $szFileName = sprintf("%s/%s.dat2", $szTrial, $labelName);
    if(count($arNeg))
        saveDataFromMem2File($arNeg, $szFileName);

    $szFileName = sprintf("runme4crop-%s.sh", $labelName);
    saveDataFromMem2File($arAllCmd, $szFileName);
    //break;

}

// merge neg-noparkingx and neg-noparking
$labelName = "neg-noparkingx2";
$szFileName = sprintf("%s/%s.dat2", $szTrial, $labelName);
if(count($arAllNeg))
    saveDataFromMem2File($arAllNeg, $szFileName);

?>
