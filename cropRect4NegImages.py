#written by DuyLD

import cv2
import sys

if(len(sys.argv) != 8):
    print('Usage {} imageName imageDir x y width height outDir'.format(sys.argv[0]))

img_name = sys.argv[1]
img_dir = sys.argv[2]
left = int(sys.argv[3])
top = int(sys.argv[4])
width = int(sys.argv[5])
height = int(sys.argv[6])
out_dir = sys.argv[7]

'''
img_name = 'MAH00019-000110'
# img_dir = 'C:/Users/ledduy/tdsjp/keyframe/MAH00019'
img_dir = '/home/mmlab/mbase/tdsjp/keyframe/MAH00019'
left = 50
top = 50
width = 800
height = 800
#out_dir = 'C:/Users/ledduy/tdsjp/tmp'
out_dir = '/home/mmlab/mbase/tdsjp/code/tmp'
'''
imgfile = '{}/{}.jpg'.format(img_dir, img_name)

print('Loading file {} ...'.format(imgfile))
img = cv2.imread(imgfile)

imheight, imwidth, channels = img.shape

if img is None:
    print('Cannot load file {}'.format(imgfile))
    quit()

right = min(left+width-1, imwidth)
bottom = min(top+height-1, imheight)
crop_img = img[left:right, top:bottom]
print('Crop rect [{}, {}, {}, {}]'.format(left, top,right, bottom))
outputfile = '{}/{}-{}-{}-{}-{}-neg.jpg'.format(out_dir, img_name, left, top, width, height)
print('Saving file {}'.format(outputfile))
cv2.imwrite(outputfile, crop_img)
