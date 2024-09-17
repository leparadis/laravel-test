<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtered Affiliate List</title>
    @vite('resources/js/app.js')
    <style>
        body {
            background-color: #121212;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        #app {
            width: 100%;
            max-width: 800px;
        }
    </style>
</head>
<body>
<div id="app">
    <filtered-affiliate-list></filtered-affiliate-list>
</div>
</body>
</html>
