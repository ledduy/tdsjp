# written by Duy-Dinh Le

import cv2
import sys

#imgDir = sys.argv[1] # noparking
#imgName = sys.argv[2] # MAH00019-xxx.jpg
# noparking/MAH00019-058720.jpg 1 685 325 76 79 

imgDir = 'noparking'
imgName = 'MAH00019-058720.jpg'
fpImgFile = '{}/{}'.format(imgDir, imgName)

img = cv2.imread(fpImgFile)

img = cv2.rectangle(img, (685, 325),(761, 404),(0,255,0),3)

cv2.imshow(imgDir, img)

if cv2.waitKey(0) & 0xFF == ord('q'):
    cv2.destroyAllWindows()
