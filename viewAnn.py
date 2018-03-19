# written by Duy-Dinh Le

import cv2
import sys

#imgDir = sys.argv[1] # noparking
#imgName = sys.argv[2] # MAH00019-xxx.jpg
# noparking/MAH00019-058720.jpg 1 685 325 76 79 

imgDir = '../../../tdsjp/code/TrainBS'

listFile = '../../../tdsjp/code/TrainBS/noparking.dat'

nFlag = 0
with open(listFile, "r") as fInput:  
    for line in fInput: 
        #print(line)
        words = line.split()
        #print(words)
        imgPath = words[0]
        #print(imgPath)
        numBB = int(words[1])
        #print(numBB)
        for idx in range(numBB):
            left = int(words[2+idx*4])
            top = int(words[2+idx*4+1])
            width = int(words[2+idx*4+2])
            height = int(words[2+idx*4+3])
            right = left + width-1
            bottom = top + height-1

            fpImgFile = '{}/{}'.format(imgDir, imgPath)

            img = cv2.imread(fpImgFile)

            img = cv2.rectangle(img, (left, top),(right, bottom),(0,255,0),3)

            cv2.imshow(imgDir, img)

            keyInput = cv2.waitKey(0) & 0xFF
            print(keyInput == ord('q'))
            if(keyInput == ord('q')):
                nFlag = 1
                break
        
        if (nFlag > 0):
            cv2.destroyAllWindows()
            quit()    
        
cv2.destroyAllWindows()
