<style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@100;400;500;700&display=swap');
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Roboto", sans-serif; /* Ganti font menjadi Roboto */
            background: url('bglik3.jpg') no-repeat;
            background-size: cover;
            background-position: center;
            color: #fff; /* Ubah warna teks menjadi putih */
        }

        .container {
            width: 590px; /* Sesuaikan lebar container */
            height: 100vh;
            margin: 0 auto;
        }

        .header {
            padding: 15px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .title {
            font-size: 24px;
        }

        .content {
            padding: 15px;
        }

        .card {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            color: #000; /* Ubah warna teks di dalam card menjadi hitam */
        }

        .input-control {
            width: 100%;
            display: block;
            padding: 0.5rem;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .text-right {
            text-align: right;
        }

        .btn {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            cursor: pointer;
            background: #2193b0;  /* fallback for old browsers */
            background: -webkit-linear-gradient(to right, #6dd5ed, #2193b0);  /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to right, #6dd5ed, #2193b0); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            color: #fff;
            border: 1px solid;
            border-radius: 3px;
        }

        .task-item {
            display: flex;
            justify-content: space-between;
        }

        .text-orange {
            color: orange;
        }

        .text-red {
            color: red;
        }

        .task-item.done span {
            text-decoration: line-through; /* Coret teks */
            color: #ccc;
        }

        .date-small {
            font-size: 20px; /* Ubah ukuran sesuai keinginan */
            color: #fff; /* Bisa disesuaikan agar lebih estetik */
        }

        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .task-left {
            display: flex;
            align-items: center;
        }

        .task-left input {
            margin-right: 10px;
        }

        .task-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .due-date {
            font-size: 14px;
            color: #555;
            font-weight: bold;
        }

        card h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-control {
            flex: 1;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

    </style>