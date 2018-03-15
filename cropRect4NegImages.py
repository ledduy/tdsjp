#written by DuyLD

import cv2
import sys

'''
if(len(sys.argv) != 8):
    print('Usage {}'.format(sys.argv[0]))

img_name = sys.argv[1]
img_dir = sys.argv[2]
left = sys.argv[3]
top = sys.argv[4]
width = sys.argv[5]
height = sys.argv[6]
out_dir = sys.argv[7]
'''

img_name = 'MAH00019-000110'
img_dir = 'C:/Users/ledduy/tdsjp/keyframe/MAH00019'
left = 50
top = 50
width = 800
height = 800
out_dir = 'C:/Users/ledduy/tdsjp'


imgfile = '{}/{}.jpg'.format(img_dir, img_name)

img = cv2.imread(imgfile)

crop_img = img[left:left+width-1, top:top+height-1]

outputfile = '{}/{}-{}-{}-{}-{}.jpg'.format(out_dir, img_name, left, top, width, height)

cv2.imwrite(outputfile, crop_img)

