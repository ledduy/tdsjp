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
TRIAL_NAME=Train3
LABEL_NAME=noparking

echo 'Generating vec file for positive sample'

POS_OUTPUTFILE=$TRIAL_NAME/$TRIAL_NAME.$LABEL_NAME.vec
POS_INPUTFILE=$TRIAL_NAME/$LABEL_NAME.dat

# max positive samples --> parse errorDone. neu so luong khac voi so luong thuc te
NUM_POS=1203  # exact number of pos samples
WIDTH=30
HEIGHT=30

#opencv_createsamples  -info $POS_INPUTFILE  -num  $NUM_POS  -w $WIDTH  -h  $HEIGHT  -vec  $POS_OUTPUTFILE

#exit
#quit

# show samples from vec file
# showvec $POS_OUTPUTFILE

# train classifier

DETECTOR_DIR=$TRIAL_NAME/$LABEL_NAME-DETECTOR-$TRIAL_NAME
mkdir $DETECTOR_DIR

BG_FILE=$TRIAL_NAME/neg-noparkingx2.dat2

# so luong POS phai <= so luong thuc su trong file .vec
NUM_POS=1000 #
NUM_NEG=1000 # new
NUM_STAGES=10 # 10 - 15 - 20 - tang dan de co ket qua trung gian

NUM_POS=1000 #
NUM_NEG=1000 # new
NUM_STAGES=20 # 10 - 15 - 20 - tang dan de co ket qua trung gian

MIN_HIT_RATE=0.999
MAX_FA_RATE=0.3

opencv_traincascade -data $DETECTOR_DIR -vec $POS_OUTPUTFILE -bg $BG_FILE -numPos $NUM_POS -numNeg $NUM_NEG -numStages $NUM_STAGES -w $WIDTH  -h  $HEIGHT -minHitRate $MIN_HIT_RATE -maxFalseAlarmRate $MAX_FA_RATE -precalcValBufSize 5000 -precalcIdxBufSize 5000
