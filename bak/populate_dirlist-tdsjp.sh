# written by Duy-Dinh Le

HOMEIMAGES='/home/mmlab/mbase/tdsjp/keyframe'
HOMEDIRLIST='/var/www/html/LabelMeAnnotationTool/annotationCache/DirLists'

# Inputs:
dirlist=$1
folder=$2

# Handle empty input argument cases:
if [ "$dirlist" == "" ]; then
    dirlist='labelme.txt';
fi

if [ "$folder" == "" ]; then
   ImageDir=$HOMEIMAGES;
else
   ImageDir="$HOMEIMAGES/$folder";
fi

# delete before running
rm $HOMEDIRLIST/$dirlist

# Populate dirlist: - use sort
find $ImageDir | sort | while read i; do
    if [[ $i =~ ^.*\.jpg$ ]]; then
#       echo $i
                dname=$(dirname $i | sed -e s=$HOMEIMAGES/==);
                iname=$(basename $i);
                echo "$dname,$iname";
                echo "$dname,$iname" >> $HOMEDIRLIST/$dirlist;
    fi
done

cp $HOMEDIRLIST/$dirlist ./
cat $HOMEDIRLIST/$dirlist | wc