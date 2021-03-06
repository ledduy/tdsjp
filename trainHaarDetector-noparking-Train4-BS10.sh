# train model

# Train2-ex --> bug - cropRect4NegImages - confused crop_img = img[top:bottom, left:right] and imwidth --> crop wrong rect
# Train2-ex -- 19 rounds - still confusing with limit50
# Training time 18 hours
# Pos: 620

# Train2 --> fix bugs in croping rect of neg-noparking, neg-noparkingx, and adding negNOT-limit50, negNOT-limit40
#TRIAL_NAME=Train2
#LABEL_NAME=noparking

# Train3 --> enrich annotations and work for all 3 traffic signs
# numPos: 1203 http://192.168.28.68/html/viewAnnotation2.php?labelName=noparking&trialName=Train3
# numNeg: 2135 - Train3/neg-noparkingx2.dat2
#TRIAL_NAME=Train3

# Train4 --> more annotation from VIDEOID_New
# numPos:  http://192.168.28.68/html/viewAnnotation2.php?labelName=noparking&trialName=Train4
# numNeg:  - Train4/neg-noparkingx2.dat2
# TRIAL_NAME=Train4

# Train4-BS10-noparking --> more annotation from VIDEOID_New
# numPos:  http://192.168.28.68/html/viewAnnotation2.php?labelName=noparking&trialName=Train4-BS10-noparking
# numNeg:  - Train4-BS10-noparking/neg-noparkingx2.dat2
TRIAL_NAME=Train4-BS10-noparking

LABEL_NAME=noparking

echo 'Generating vec file for positive sample'

POS_OUTPUTFILE=$TRIAL_NAME/$TRIAL_NAME.$LABEL_NAME.vec
POS_INPUTFILE=$TRIAL_NAME/$LABEL_NAME.dat

# max positive samples --> parse errorDone. neu so luong khac voi so luong thuc te
NUM_POS=1320  # exact number of pos samples
WIDTH=30
HEIGHT=30

opencv_createsamples  -info $POS_INPUTFILE  -num  $NUM_POS  -w $WIDTH  -h  $HEIGHT  -vec  $POS_OUTPUTFILE

#exit
#quit

# show samples from vec file
# showvec $POS_OUTPUTFILE

# train classifier

DETECTOR_DIR=$TRIAL_NAME/$LABEL_NAME-DETECTOR-$TRIAL_NAME
mkdir $DETECTOR_DIR

BG_FILE=$TRIAL_NAME/neg-noparkingx2.dat2

# so luong POS phai <= so luong thuc su trong file .vec
NUM_POS=1300 #
NUM_NEG=2000 #
NUM_STAGES=18

MIN_HIT_RATE=0.999
MAX_FA_RATE=0.3

opencv_traincascade -data $DETECTOR_DIR -vec $POS_OUTPUTFILE -bg $BG_FILE -numPos $NUM_POS -numNeg $NUM_NEG -numStages $NUM_STAGES -w $WIDTH  -h  $HEIGHT -minHitRate $MIN_HIT_RATE -maxFalseAlarmRate $MAX_FA_RATE -precalcValBufSize 5000 -precalcIdxBufSize 5000
