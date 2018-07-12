# written by Duy-Dinh Le
# last update: Mar 10,2 018

import cv2
import os
import sys

# output keyframe dir = vidName
# keyframe file name = videoName-frameID.jpg
def doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate):

    vidFullPath = '{}/{}.{}'.format(vidPath, vidName, vidExt)

    # output keyframe dir for video
    kfPath4Vid = '{}/{}'.format(kfPath, vidName)

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

    print('*** Number of frames: {}'.format(frameCount))

    while(True):
        frameID += 1

        if (frameID % samplingRate != 0) :
            continue

        # Capture frame-by-frame
        ret, frame = cap.read()

        if (not ret):
            print('>>> Reaching end of file')
            break

        frameName = '{}-{:06d}'.format(vidName, frameID)

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

samplingRate = 5 # 5fps

if (len(sys.argv) != 3):
    print('### Usage: {} videoName videoExt'.format(sys.argv[0]))
    print('### Usage: {} MAH00019 mp4'.format(sys.argv[0]))
    quit()

vidName = sys.argv[1] # 'MAH00019'
vidExt = sys.argv[2] # 'mp4'

doKFExtraction(vidName, vidExt, vidPath, kfPath, samplingRate)

quit()
