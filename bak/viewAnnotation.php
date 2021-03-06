<?php

/*
- just for reference - use viewAnnotation2.php for updates
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

function img2base64($szImgURL)
{
    $imgzz = imagecreatefromjpeg($szImgURL);
    $widthzz = imagesx($imgzz);
    $heightzz = imagesy($imgzz);
    // calculate thumbnail size
    $new_width = $thumbWidth = 800;  // to reduce loading time
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

function imgrect2base64($szLine)
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
    $new_width = $thumbWidth = 1000;  // to reduce loading time
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

$szLabelName = "limit50";

if(isset($_REQUEST["labelName"]))
{
    $szLabelName = $_REQUEST["labelName"];
}

$szTrialName = "Train1";

if(isset($_REQUEST["trialName"]))
{
    $szTrialName = $_REQUEST["trialName"];
}

$szCodeDir = "/home/mmlab/mbase/tdsjp/code";
$szAnnDir = sprintf("%s/%s", $szCodeDir, $szTrialName);

// /home/mmlab/mbase/tdsjp/code/Train1/limit50.dat
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

    $szImgContent = imgrect2base64($szLine);

    // url to update annotation
    $szAnnURL = sprintf("LabelMeAnnotationTool/tool.html?username=duy&collection=LabelMe&mode=f&folder=%s&image=%s&objects=noparking,neg-noparking,neg-noparkingx,limit40,limit50,greenguide,blueguide", $szVideoID, $szKeyFrameID);

    printf("<A HREF='%s' TARGET='_blank'><IMG  TITLE='%s' SRC='data:image/jpeg;base64,". $szImgContent ."' /></A>", $szAnnURL, $szLine);

    // printf("<BR><IMG SRC='%s'>\n", $szURL);

    //break;

}
?>
