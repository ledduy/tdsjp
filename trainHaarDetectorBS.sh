# train model

TRIAL_NAME=TrainBS
LABEL_NAME=noparking

#LABEL_NAME=limit50

echo 'Generating vec file for positive sample'

POS_OUTPUTFILE=$TRIAL_NAME/$TRIAL_NAME.$LABEL_NAME.vec
POS_INPUTFILE=$TRIAL_NAME/$LABEL_NAME.dat

# max positive samples --> parse errorDone. neu so luong khac voi so luong thuc te
NUM_POS=10000
WIDTH=30
HEIGHT=30

#opencv_createsamples  -info $POS_INPUTFILE  -num  $NUM_POS  -w $WIDTH  -h  $HEIGHT  -vec  $POS_OUTPUTFILE

# show samples from vec file
# showvec $POS_OUTPUTFILE

# train classifier

DETECTOR_DIR=$TRIAL_NAME/$LABEL_NAME-DETECTOR
mkdir $DETECTOR_DIR

BG_FILE=$TRIAL_NAME/neg-noparking.datbs

BG_FILE1=$TRIAL_NAME/neg-noparking.dat2
BG_FILE2=$TRIAL_NAME/neg-noparkingx.dat2

# concatenate
#cat $BF_FILE1 $BF_FILE2 > $BG_FILE

# so luong POS phai <= so luong thuc su trong file .vec
NUM_POS=620 #600
NUM_NEG=620 #600
NUM_STAGES=20 #20
MIN_HIT_RATE=0.999
MAX_FA_RATE=0.3

opencv_traincascade -data $DETECTOR_DIR -vec $POS_OUTPUTFILE -bg $BG_FILE -numPos $NUM_POS -numNeg $NUM_NEG -numStages $NUM_STAGES -w $WIDTH  -h  $HEIGHT -minHitRate $MIN_HIT_RATE -maxFalseAlarmRate $MAX_FA_RATE -precalcValBufSize 10000 -precalcIdxBufSize 10000
