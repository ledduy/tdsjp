# written by DuyLD
# last update: Apr 25

# opencv must be installed in advance along with python
import cv2
import sys
import os

def detect(tds_classifier_xml, frame, gray_img, sign_name):

   tds_cascade = cv2.CascadeClassifier(tds_classifier_xml)

   sign = tds_cascade.detectMultiScale(gray_img, 1.25, 3)
   cnt = 0
   for (x,y,w,h) in sign:
       cv2.rectangle(frame,(x,y),(x+w,y+h),(255,0,0),2)
       print ('%s: %d - %d - %d - %d' % (sign_name, x, y, w, h))
       cnt = cnt+1

 #   cv2.rectangle(frame,(10,10),(100,100),(255,0,0),2)

   return frame, cnt

if (len(sys.argv) != 3):
    print('### Usage: {} videoName videoExt'.format(sys.argv[0]))
    print('### Usage: {} MAH00019 MP4'.format(sys.argv[0]))
    quit()

#video_name = 'MAH00019'
#video_name = '20180224_03'
#video_name = '20180224_01'
video_name = '20180306_01'
#video_name = sys.argv[1]

video_ext = 'mp4'
#video_ext = 'avi'
#video_ext = sys.argv[2]

#camera_url = './20180224_02.avi'

video_dir = './video'
camera_url = '{}/{}.{}'.format(video_dir, video_name, video_ext)

video = cv2.VideoCapture(camera_url)

frame_w =video.get(cv2.CAP_PROP_FRAME_WIDTH)
frame_h = video.get(cv2.CAP_PROP_FRAME_HEIGHT)
legend_loc_x = int(frame_w*0.1)
legend_loc_y = int(frame_h*0.1)

model_list = {'blueguide' :
#'./Train3/blueguide-DETECTOR-Train3/cascade15.xml' # many false p#
'./Train3/blueguide-DETECTOR-Train3/cascade24.xml' # many false p#
}

output_dir = './tmp/blueguide-DETECTOR-Train3'
if not os.path.exists(output_dir):
    os.makedirs(output_dir)


font = cv2.FONT_HERSHEY_SIMPLEX
cnt = 0
frame_id = 0
frame_rate = 10

nCount = 0;
nSkip = 5000

cntx = 0
while(True):
    # Capture frame-by-frame
    ret, frame = video.read()

    if ret != True:
        break
    cnt = cnt + 1
    frame_id = frame_id + 1

    cntx +=1
    if (cntx < nSkip):
        continue

    if (cnt % frame_rate == 0):
        cnt = 0
        gray_img = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

        output_text = 'frame %d' % (frame_id)
        all_cnt = 0
        for sign_name in model_list:
            frame, cnt_out = detect(model_list[sign_name], frame, gray_img, sign_name)
            if (cnt_out > 0):
                all_cnt = all_cnt + cnt_out
                output_text = '%s - %s' % (output_text, sign_name)
                print(output_text)

        cv2.putText(frame, output_text, (legend_loc_x, legend_loc_y), font, 1, (0,255,0), 2, cv2.LINE_AA)

        if all_cnt > 0:
            output_file = '%s/%s-%s.jpg' % (output_dir, video_name, output_text)

            nCount += 1
            if(nCount <= 100):
                cv2.imwrite(output_file, frame)

        cv2.imshow('Demo TDS', frame)
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

# When everything done, release the capture
video.release()
cv2.destroyAllWindows()
