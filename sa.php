<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two Videos</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }

        .video-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .video-box {
            flex: 1;
            min-width: 280px;
            background: white;
            padding: 15px;
            border-radius: 12px;
        }

        video {
            width: 100%;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <h2>Our Videos</h2>

    <div class="video-container">

        <div class="video-box">
            <h3>Video 1</h3>
            <video controls>
                <source src="video1.mp4" type="video/mp4">
                Your browser does not support video.
            </video>
        </div>

        <div class="video-box">
            <h3>Video 2</h3>
            <video controls>
                <source src="video2.mp4" type="video/mp4">
                Your browser does not support video.
            </video>
        </div>

    </div>

</body>
</html>