# written by Duy-Dinh Le

# get videoID as input

VIDEOID=$1

# create soft link
sudo ln -s /home/mmlab/mbase/tdsjp/keyframe/$VIDEOID /var/www/html/LabelMeAnnotationTool/Images/$VIDEOID

# generate collection
./populate_dirlist-tdsjp.sh $VIDEOID.txt $VIDEOID

# link to annotate

# mode=c --> next in collection videoID.txt
# mode=f -- random
ALINK='http://192.168.28.68/html/LabelMeAnnotationTool/tool.html?username=duy&mode=c&collection='$VIDEOID'&folder='$VIDEOID'&image='$VIDEOID'-000005.jpg'

echo $ALINK

echo "<P><A HREF='$ALINK'>$VIDEOID</A>" >> ./tdsjp-annotation.htm

sudo cp ./tdsjp-annotation.htm /var/www/html/LabelMeAnnotationTool/