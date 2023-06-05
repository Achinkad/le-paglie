#! /usr/bin/python

from websocket import create_connection
from imutils.video import VideoStream
import imutils
import face_recognition
import cv2
import numpy as np
import os

# This is a demo of running face recognition on live video from your webcam. It's a little more complicated than the
# other example, but it includes some basic performance tweaks to make things run a lot faster:
#   1. Process each video frame at 1/4 resolution (though still display it at full resolution)
#   2. Only detect faces in every other frame of video.

# PLEASE NOTE: This example requires OpenCV (the `cv2` library) to be installed only to read from your webcam.
# OpenCV is *not* required to use the face_recognition library. It's only required if you want to run this
# specific demo. If you have trouble installing it, try any of the other demos that don't require it instead.

# Get a reference to webcam #0 (the default one)
# video_capture = cv2.VideoCapture("rtsp://tapo123:tapo123@10.10.88.248/stream1")
ws = create_connection("ws://10.10.88.244:8080/")
video_stream = VideoStream(src='rtsp://tapo123:tapo123@10.10.88.248/stream1',framerate=20).start()

# Directory path containing the images of known individuals
directory = "/home/rasp/Desktop/photos/"

# Create arrays of known face encodings and their names
known_face_encodings = []
known_face_names = []

# Iterate over files in the directory
for filename in os.listdir(directory):
    if filename.endswith(".jpg") or filename.endswith(".png"):
        # Load the image file
        image_path = os.path.join(directory, filename)
        image = face_recognition.load_image_file(image_path)
        
        # Generate face encodings for the image
        face_encoding = face_recognition.face_encodings(image)
        
        # If a face encoding is found in the image
        if len(face_encoding) > 0:
            known_face_encodings.append(face_encoding[0])
            
            # Extract the name from the filename (assuming the filename is in the format "name.jpg" or "name.png")
            name = os.path.splitext(filename)[0]
            known_face_names.append(name)

# Print the populated arrays
print(known_face_encodings)
print(known_face_names)

# Initialize some variables
face_locations = []
face_encodings = []
face_names = []
process_this_frame = 1

while True:
    # Grab a single frame of video
    frame = video_stream.read()
    small_frame = imutils.resize(frame, width=500)

    # Only process every other frame of video to save time
    if process_this_frame == 1:
        # Resize frame of video to 1/4 size for faster face recognition processing
        # small_frame = cv2.resize(frame, (0, 0), fx=0.5, fy=0.5)

        # Convert the image from BGR color (which OpenCV uses) to RGB color (which face_recognition uses)
        rgb_small_frame = cv2.cvtColor(small_frame, cv2.COLOR_BGR2RGB)

        # Find all the faces and face encodings in the current frame of video
        face_locations = face_recognition.face_locations(rgb_small_frame)
        face_encodings = face_recognition.face_encodings(rgb_small_frame, face_locations)

        face_names = []
        for face_encoding in face_encodings:
            # See if the face is a match for the known face(s)
            matches = face_recognition.compare_faces(known_face_encodings, face_encoding)
            name = "Unknown"

            # # If a match was found in known_face_encodings, just use the first one.
            # if True in matches:
            #     first_match_index = matches.index(True)
            #     name = known_face_names[first_match_index]

            # Or instead, use the known face with the smallest distance to the new face
            face_distances = face_recognition.face_distance(known_face_encodings, face_encoding)
            best_match_index = np.argmin(face_distances)
            if matches[best_match_index]:
                name = known_face_names[best_match_index]

            face_names.append(name)
            
            if name == "Unknown":
                ws.send("Alert Intrusion!:10.10.88.248")
                print(ws.recv())
    
    if process_this_frame == 20:
        process_this_frame = 1
    else:
        process_this_frame = process_this_frame + 1
    
    print(process_this_frame)
    
    # Display the results
    for (top, right, bottom, left), name in zip(face_locations, face_names):

        # Draw a box around the face
        cv2.rectangle(small_frame, (left, top), (right, bottom), (0, 0, 255), 2)

        # Draw a label with a name below the face
        # cv2.rectangle(small_frame, (left, bottom - 35), (right, bottom), (0, 0, 255), cv2.FILLED)
        cv2.rectangle(small_frame, (left, bottom), (right, bottom), (0, 0, 255), cv2.FILLED)
        font = cv2.FONT_HERSHEY_DUPLEX
        # cv2.putText(small_frame, name, (left + 6, bottom - 6), font, 1.0, (255, 255, 255), 1)
        cv2.putText(small_frame, name, (left + 6, bottom + 16), font, 0.5, (255, 255, 255), 1)

    # Display the resulting image
    cv2.imshow('Video', small_frame)

    # Hit 'q' on the keyboard to quit!
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

# Release handle to the webcam
ws.close()
video_capture.release()
cv2.destroyAllWindows()
