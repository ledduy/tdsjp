<?php

/*
- view annotations of a label
- can be view with/without frame - use cropView option
*/

/*
if (extension_loaded('gd') && function_exists('gd_info')) {
    echo "PHP GD library is installed on your web server";
}
else {
    echo "PHP GD library is NOT installed on your web server";
}

phpinfo();
*/

require_once "kl-IOTools.php";

function img2base64($szImgURL, $thumbWidth=800)
{
    $imgzz = imagecreatefromjpeg($szImgURL);
    $widthzz = imagesx($imgzz);
    $heightzz = imagesy($imgzz);
    // calculate thumbnail size
    $new_width = $thumbWidth;  // to reduce loading time
    $new_height = floor($heightzz*($thumbWidth/$widthzz));
    // create a new temporary image
    $tmp_img = imagecreatetruecolor($new_width, $new_height);
    // copy and resize old image into new image
    // imagecopyresized($tmp_img, $imgzz, 0, 0, 0, 0, $new_width, $new_height, $widthzz, $heightzz);
    // better quality compared with imagecopyresized
    imagecopyresampled($tmp_img, $imgzz, 0, 0, 0, 0, $new_width, $new_height, $widthzz, $heightzz);
    //output to buffer
    ob_start();
    imagejpeg($tmp_img);
    $szImgContent = base64_encode(ob_get_clean());
    // sprintf("<IMG  TITLE='%s - %s' SRC='data:image/jpeg;base64,". $szImgContent ."' />", $szQueryImg, $fScore);
    imagedestroy($imgzz);
    imagedestroy($tmp_img);

    return $szImgContent;
}

function imgrect2base64($szLine, $thumbWidth=800)
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

    $imgzz = imagecreatefromjpeg($szURL);
    $widthzz = imagesx($imgzz);
    $heightzz = imagesy($imgzz);
    $greencolor   = imagecolorallocate($imgzz, 0, 255,   0);

    $nNumRects = intval($arTmp[1]);

//    printf("%d \n", $nNumRects);
//    exit();
    for($k=0; $k<$nNumRects; $k++)
    {
        $left = intval($arTmp[2+4*$k]);
        $top = intval($arTmp[2+4*$k+1]);
        $right = $left + intval($arTmp[2+4*$k+2]);
        $bottom = $top + intval($arTmp[2+4*$k+3]);

        //printf("%d %d %d %d - [%d-%d]<BR>\n", $left, $top, $right, $bottom, $widthzz, $heightzz);

        $nRet = imagerectangle($imgzz, $left, $top, $right, $bottom, $greencolor);

        if(!$nRet)
        {
            printf("FAILED HERE\n");
        }
    }

    // calculate thumbnail size
    $new_width = $thumbWidth;  // to reduce loading time
    $new_height = floor($heightzz*($thumbWidth/$widthzz));
    // create a new temporary image
    $tmp_img = imagecreatetruecolor($new_width, $new_height);
    // copy and resize old image into new image
    // imagecopyresized($tmp_img, $imgzz, 0, 0, 0, 0, $new_width, $new_height, $widthzz, $heightzz);
    // better quality compared with imagecopyresized
    imagecopyresampled($tmp_img, $imgzz, 0, 0, 0, 0, $new_width, $new_height, $widthzz, $heightzz);
    //output to buffer
    ob_start();
    imagejpeg($tmp_img);
    $szImgContent = base64_encode(ob_get_clean());
    // sprintf("<IMG  TITLE='%s - %s' SRC='data:image/jpeg;base64,". $szImgContent ."' />", $szQueryImg, $fScore);
    imagedestroy($imgzz);
    imagedestroy($tmp_img);

    return $szImgContent;
}

// crop bounding box
function imgrect2base64BB($szLine)
{
    $szKFDir = "/home/mmlab/mbase/tdsjp/keyframe";

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
    $szURL = sprintf("%s/%s/%s", $szKFDir, $szVideoID, $szKeyFrameID);

    $imgzz = imagecreatefromjpeg($szURL);

    printf("<!--Load image from [%s]-->\n", $szURL);

    if(!$imgzz)
    {
        printf("<!--Failed to load image from [%s]-->\n", $szURL);
    }
    $widthzz = imagesx($imgzz);
    $heightzz = imagesy($imgzz);
    $greencolor   = imagecolorallocate($imgzz, 0, 255,   0);

    $nNumRects = intval($arTmp[1]);

    // url to update annotation
    $szAnnURL = sprintf("LabelMeAnnotationTool/tool.html?username=duy&collection=LabelMe&mode=f&folder=%s&image=%s&objects=noparking,neg-noparking,neg-noparkingx,limit40,limit50,greenguide,blueguide", $szVideoID, $szKeyFrameID);

//    printf("%d \n", $nNumRects);
//    exit();
    for($k=0; $k<$nNumRects; $k++)
    {
        $left = intval($arTmp[2+4*$k]);
        $top = intval($arTmp[2+4*$k+1]);
        $right = $left + intval($arTmp[2+4*$k+2])-1;
        $bottom = $top + intval($arTmp[2+4*$k+3])-1;

        printf("<!--%d %d %d %d - [%d-%d]-->\n", $left, $top, $right, $bottom, $widthzz, $heightzz);

        // calculate thumbnail size
        $new_width = $right - $left +1;
        $new_height = $bottom - $top +1;
        // create a new temporary image
        $tmp_img = imagecreatetruecolor($new_width, $new_height);

        //$nRet = imagecopyresampled($tmp_img, $imgzz, 0, 0, $left, $top, $new_width, $new_height, $new_width, $new_height);
        $nRet = imagecopy($tmp_img, $imgzz, 0, 0, $left, $top, $new_width, $new_height);


        //output to buffer
        ob_start();
        imagejpeg($tmp_img);
        $szImgContent = base64_encode(ob_get_clean());
        printf("<A HREF='%s' TARGET='_blank'><IMG WIDTH='100' HEIGHT='100' TITLE='%s' SRC='data:image/jpeg;base64,". $szImgContent ."' /></A> ", $szAnnURL, $szLine);
        imagedestroy($tmp_img);
    }
    imagedestroy($imgzz);

}

$szLabelName = "limit50";
if(isset($_REQUEST["labelName"]))
{
    $szLabelName = $_REQUEST["labelName"];
}

$szTrialName = "Train2";

if(isset($_REQUEST["trialName"]))
{
    $szTrialName = $_REQUEST["trialName"];
}

$nViewCrop = 1;
if(isset($_REQUEST["viewCrop"]))
{
    $nViewCrop = $_REQUEST["viewCrop"];
}

$szCodeDir = "/home/mmlab/mbase/tdsjp/code";
$szAnnDir = sprintf("%s/%s", $szCodeDir, $szTrialName);

// /home/mmlab/mbase/tdsjp/code/Train1/limit50.dat
if(strstr($szLabelName, 'neg'))
{
    $szAnnFile = sprintf("%s/%s2.dat2", $szAnnDir, $szLabelName); // neg-noparkingx2.dat2
    loadListFile($arList, $szAnnFile);


    printf("<H1>Annotations for [%s] - [%s]: %d </H1>\n", $szTrialName, $szLabelName, count($arList));

    foreach($arList as $szLine)
    {
        $szTrialDir = '/home/mmlab/mbase/tdsjp/code';
        // Train3/negNOT-noparking/MAH00019-056905-645-436-68-73-neg.jpg
        $szURL = sprintf("%s/%s", $szTrialDir, $szLine);
        printf("<!--%s-->\n", $szURL);

        $szImgContent = img2base64($szURL, 100);

        $arTmp = explode("/", $szLine);
        $szLine2 = trim($arTmp[2]);
        $arTmp = explode("-", $szLine2);
        $szVideoID = trim($arTmp[0]);
        $szFrameOffset = trim($arTmp[1]);
        $szKeyFrameID = sprintf("%s-%s.jpg", $szVideoID, $szFrameOffset);

        // url to update annotation
        $szAnnURL = sprintf("LabelMeAnnotationTool/tool.html?username=duy&collection=LabelMe&mode=f&folder=%s&image=%s&objects=noparking,neg-noparking,neg-noparkingx,limit40,limit50,greenguide,blueguide,neg-blueguide", $szVideoID, $szKeyFrameID);

        printf("<A HREF='%s' TARGET='_blank'><IMG  TITLE='%s' SRC='data:image/jpeg;base64,". $szImgContent ."' /></A>", $szAnnURL, $szLine);

    }
}
else
{
    $szAnnFile = sprintf("%s/%s.dat", $szAnnDir, $szLabelName);
    loadListFile($arList, $szAnnFile);
    printf("<H1>Annotations for [%s] - [%s]: %d </H1>\n", $szTrialName, $szLabelName, count($arList));

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

        //$szImgContent = img2base64($szURL);

        if($nViewCrop)
        {
            imgrect2base64BB($szLine);
        }
        else {
            $szImgContent = imgrect2base64($szLine);

            // url to update annotation
            $szAnnURL = sprintf("LabelMeAnnotationTool/tool.html?username=duy&collection=LabelMe&mode=f&folder=%s&image=%s&objects=noparking,neg-noparking,neg-noparkingx,limit40,limit50,greenguide,blueguide,neg-blueguide", $szVideoID, $szKeyFrameID);

            printf("<A HREF='%s' TARGET='_blank'><IMG  TITLE='%s' SRC='data:image/jpeg;base64,". $szImgContent ."' /></A>", $szAnnURL, $szLine);
        }

        // printf("<BR><IMG SRC='%s'>\n", $szURL);

        //break;
}
}
?>
