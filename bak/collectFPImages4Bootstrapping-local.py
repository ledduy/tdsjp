# written by Duy-Dinh Le

# run current detector on the train dataset to collect false positive imagess

import cv2
import os


def detect(tds_classifier_xml, frame, gray_img, sign_name):

   tds_cascade = cv2.CascadeClassifier(tds_classifier_xml)

   sign = tds_cascade.detectMultiScale(gray_img, 1.25, 3)
   cnt = 0
   for (x,y,w,h) in sign:
       cv2.rectangle(frame,(x,y),(x+w,y+h),(255,0,0),2)
       print ('%s: %d - %d - %d - %d' % (sign_name, x, y, w, h
))
       cnt = cnt+1


 #   cv2.rectangle(frame,(10,10),(100,100),(255,0,0),2)

   return frame, cnt

root_dir = '/Users/ledinhduy/tdsjp'

video_name = 'MAH00019'
video_ext = 'mp4'

#video_name = '20180306_01'
#video_ext = 'mp4'

#video_name = '20180306_03'
#video_ext = 'avi'

#video_name = '20180224_02'
#video_ext = 'avi'

#video_name = '20180224_01'
#video_ext = 'avi'


camera_url = '{}/video/{}.{}'.format(root_dir, video_name, video_ext)
print(camera_url)

video = cv2.VideoCapture(camera_url)

frame_w =video.get(cv2.CAP_PROP_FRAME_WIDTH)
frame_h = video.get(cv2.CAP_PROP_FRAME_HEIGHT)
legend_loc_x = int(frame_w*0.1)
legend_loc_y = int(frame_h*0.1)

# mmlab@ubuntu:~/mbase/tdsjp/code$ mv Train1/noparking-DETECTOR/* Train1/noparking-DETECTOR2/
model_dir = '{}/code/Train1/noparking-DETECTOR2/cascade.xml'.format(root_dir)

model_list = {'noparking': model_dir}

font = cv2.FONT_HERSHEY_SIMPLEX
cnt = 0
frame_id = 0
frame_rate = 30


file_output = '{}-bs.htm'.format(video_name)  # collection of LabelMeAnnTool for boostrapping round

f= open(file_output,'w')

max_KF = 1000
count = 0

url_default = 'http://192.168.28.68/html/LabelMeAnnotationTool/tool.html?mode=f&username=duy'
while(count < max_KF):
    # Capture frame-by-frame
    ret, frame = video.read()

    if ret != True:
        break
    cnt = cnt + 1
    frame_id = frame_id + 1

    if (cnt % frame_rate == 0):
        cnt = 0
        gray_img = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

        output_text = 'frame %d' % (frame_id)
        print(output_text)
        all_cnt = 0
        for sign_name in model_list:
            #print(model_list[sign_name])
            frame, cnt_out = detect(model_list[sign_name], frame, gray_img, sign_name)
            if (cnt_out > 0):
                all_cnt = all_cnt + cnt_out
                output_text = '%s - %s' % (output_text, sign_name)
                print(output_text)

        #cv2.putText(frame, output_text, (legend_loc_x, legend_loc_y), font, 1, (0,255,0), 2, cv2.LINE_AA)

        if all_cnt > 0:
            #output_file = '%s-%s.jpg' % (video_name, output_text)
            output_file = '{}-{:06d}.jpg'.format(video_name, frame_id)
            
            # MAH00019,MAH00019-063175.jpg
            #line = '{},{}'.format(video_name, output_file)
            
            line = "<P><A HREF='{}&folder={}&image={}'>{}</A>".format(url_default, video_name, output_file, output_file)
            
            count += 1

            print('###{}.{}'.format(count, line))
            f.write('{}\n'.format(line))
            
            tmp_output = '{}/tmp/{}'.format(root_dir, output_file)
            #cv2.imwrite(tmp_output, frame)

        #cv2.imshow('Demo TDS', frame)
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

# When everything done, release the capture
video.release()
cv2.destroyAllWindows()

f.close()
