# written by Duy-Dinh Le
# last update: Jul 2, 2018

# fix stupid bug --> not sampling - consecutive frames are numbered by 0, 5,  --> too dense
# MAH00019 --> 60K --> 12K frames (sampling rate =5) equal to 6 mins (30fps *60secs/min )
# nSkipFrames = 30K --> to keep existing
# samplingRate = 10 --> to reduce the number of keyframes

# MAH00019_New --> new name for keyframe dir
# vidNameNew = '{}_New'.format(vidName)

import cv2
import os
import sys

# output keyframe dir = vidName
# keyframe file name = videoName-frameID.jpg
def doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate):

    vidFullPath = '{}/{}.{}'.format(vidPath, vidName, vidExt)

    # MAH00019_New --> new name for keyframe
    vidNameNew = '{}_New'.format(vidName)
    # output keyframe dir for video
    kfPath4Vid = '{}/{}'.format(kfPath, vidNameNew)

    if not os.path.exists(kfPath4Vid):
        os.makedirs(kfPath4Vid)

    print('### Extracting keyframes {} - {} - {}'.format(vidFullPath, kfPath4Vid, samplingRate))

    cap = cv2.VideoCapture(vidFullPath)

    if not cap.isOpened():
        print('>>> Error in opening video {}'.format(vidFullPath))
        return

    frameID = 0
    maxNumFrames =  30000 # 20K for 60 min video

    numFrames = 0

    frameCount = cap.get(cv2.CAP_PROP_FRAME_COUNT)

    # magic number is estimated from buggy version
    nNumFramesDone = int(frameCount/5)
    nSkipFrames = nNumFramesDone + 200

    print('*** Number of frames: {}'.format(frameCount))
    print('*** Number of frames to be skipped due to previous run: {}'.format(nSkipFrames))

    while(True):
        frameID += 1

        # Capture frame-by-frame
        ret, frame = cap.read()

        if (not ret):
            print('>>> Reaching end of file')
            break

        if(frameID < nSkipFrames):
            continue

        if (frameID % samplingRate != 0) :
            continue

        frameName = '{}-{:06d}'.format(vidNameNew, frameID)

        frameFullPath = '{}/{}.jpg'.format(kfPath4Vid, frameName)

        cv2.imwrite(frameFullPath, frame)

        print('{}. Saving frame {}'.format(frameID, frameName))

        numFrames += 1
        if(numFrames >= maxNumFrames):
            print('>>> Reaching max frames')
            break

        if(frameID >= frameCount):
            print('>>> Reaching frame count')
            break

        # for visualization
        #cv2.imshow(vidName, frame)
        #if cv2.waitKey(1) & 0xFF == ord('q'):
        #    break

    # When everything done, release the capture
    cap.release()
    #cv2.destroyAllWindows()
    print('### DONE')

homeDir = '/Users/ledinhduy/mbase/tdsjp/'
homeDir = '../'
vidPath = homeDir + 'video'
kfPath = homeDir + 'keyframe'

# make dir
if not os.path.exists(kfPath):
    os.makedirs(kfPath)

#samplingRate = 5 # 5fps
samplingRate = 10 # 5fps


if (len(sys.argv) != 3):
    print('### Usage: {} videoName videoExt'.format(sys.argv[0]))
    print('### Usage: {} MAH00019 mp4'.format(sys.argv[0]))
    quit()

vidName = sys.argv[1] # 'MAH00019'
vidExt = sys.argv[2] # 'mp4'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)

quit()
'''


vidName = 'MAH00019'
vidExt = 'mp4'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)

quit();

vidName = '20180224_01'
vidExt = 'avi'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)


vidName = '20180224_02'
vidExt = 'avi'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)

vidName = '20180224_03'
vidExt = 'avi'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)

vidName = '20180306_01'
vidExt = 'mp4'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)

vidName = '20180306_03'
vidExt = 'avi'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)

vidName = 'drive'
vidExt = 'mp4'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)

vidName = 'traffic_sign_video2802'
vidExt = 'avi'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)


vidName = '20180306_02'
vidExt = 'avi'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)
'''
