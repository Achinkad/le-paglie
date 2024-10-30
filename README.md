## Le Paglie

This project is a web-based application developed to facilitate the management and monitoring of RTSP cameras, with added features for facial and animal recognition. The platform allows users to set up cameras, register known individuals or animals, and receive real-time security alerts whenever an unrecognized entity is detected. This project was developed collaboratively by **José Areia**, **Bruno Santos**, and **Tiago Gomes**.

### Project Purpose

The primary goal of `Le Paglie` is to provide a secure and easy-to-use surveillance system with AI-powered recognition capabilities. With support for both people and animal recognition, `Le Paglie` offers enhanced monitoring by notifying users of potential security breaches through:

- **Real-time recognition**: Detects registered faces, people, or animals and flags any unrecognized individuals or animals in the camera's view.
- **Customizable alerts**: Users can receive notifications when unknown entities are detected.
- **Camera management**: Manage multiple RTSP cameras from a central interface.

### Key Features

- **RTSP Camera Integration**: Add, manage, and monitor multiple RTSP cameras.
- **Face and Animal Recognition**: Register individuals and animals for personalized recognition and alerts.
- **Real-Time Notifications**: Receive security alerts for unrecognized entities, using WebSockets for instant updates.
- **User-Friendly Interface**: Built with Vue for a responsive, accessible UI.
- **Scalable Backend**: Laravel and MySQL provide a robust backend to handle multiple cameras and recognition processes.

### Technologies Used

- **Backend**: Laravel
- **Frontend**: Vue.js
- **Recognition Service**: Python (for facial and animal recognition)
- **RTSP Streaming**: Node.js with WebSocket support for real-time communication

### Installation

**Prerequisites**

- PHP 8.0 or higher
- Node.js and npm
- Python 3.x (for recognition services)
- MySQL database
- RTSP-compatible cameras

**Steps**

1. **Clone the repository:**
```
$ git clone https://github.com/your-repo/le-paglie.git
$ cd le-paglie
```

2. **Install dependecies:**
```
$ composer install
$ npm install && npm run dev
```

3. **Setup environment**: Copy `.env.example` to `.env` and update the necessary configurations.

4. **Run migrations**:
```
$ php artisan migrate
```

5. **Start the RTSP server**:
```
$ node rtsp-server.js
```

6. **Start the Laravel server**:
```
$ php artisan serve
```

7. **Run the Python recognition service** (if applicable):
```
$ python face_rec.py
```

### Usage

After setting up, you can log in to `Le Paglie` to add cameras, register known faces or animals, and configure alert notifications. `Le Paglie's` dashboard will display camera feeds and provide alerts for any unrecognized entities in real-time.

### Contributors

- José Areia
- Bruno Santos
- Tiago Gomes
