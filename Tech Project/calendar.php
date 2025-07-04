<?php
session_start();
include_once 'includes/config.php';

// Check if user is logged in
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - Diabetes Wellness Assistant</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/accessibility.css">
    <style>
        .calendar-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .calendar {
            flex: 2;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .calendar-header button:hover {
            background-color: #45a049;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day {
            aspect-ratio: 1;
            padding: 5px;
            border: 1px solid #4CAF50;
            cursor: pointer;
            position: relative;
            color: #4CAF50;
        }

        .calendar-day:hover {
            background-color: #e8f5e9;
        }

        .calendar-day.today {
            background-color: #4CAF50;
            color: white;
        }

        .calendar-day.has-events::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background-color: #4CAF50;
            border-radius: 50%;
        }

        .event-form {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .event-form input,
        .event-form textarea,
        .event-form select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            border: 1px solid #4CAF50;
            border-radius: 4px;
            color: #4CAF50;
        }

        .event-form input::placeholder,
        .event-form textarea::placeholder {
            color: #4CAF50;
            opacity: 0.7;
        }

        .event-form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .event-form button:hover {
            background-color: #45a049;
        }

        .label-container {
            margin-top: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .label-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .label-item {
            padding: 5px 10px;
            border-radius: 15px;
            color: white;
            cursor: pointer;
            background-color: #4CAF50;
        }

        .label-item.selected {
            box-shadow: 0 0 0 2px #4CAF50;
        }

        .label-input {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 8px;
        }

        .label-input input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #4CAF50;
            border-radius: 4px;
            background: white;
            color: #4CAF50;
        }

        .label-input input::placeholder {
            color: #4CAF50;
            opacity: 0.7;
        }

        .label-input button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            margin-top: 10px;
        }

        .label-input button:hover {
            background-color: #45a049;
        }

        #addLabelBtn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        #addLabelBtn:hover {
            background-color: #45a049;
        }

        .event-list {
            margin-top: 20px;
        }

        .event-item {
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            background-color: #e8f5e9;
            color: #4CAF50;
        }

        .save-message {
            display: none;
            color: #4CAF50;
            margin-top: 10px;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <?php include 'includes/accessibility_widget.php'; ?>

    <div class="container">
        <h1>Calendar</h1>
        
        <div class="calendar-container">
            <div class="calendar">
                <div class="calendar-header">
                    <button id="prevMonth">&lt;</button>
                    <h2 id="currentMonth">Month Year</h2>
                    <button id="nextMonth">&gt;</button>
                </div>
                <div class="calendar-grid" id="calendarGrid">
                    <!-- Calendar days will be generated here -->
                </div>
            </div>

            <div class="event-form">
                <h3>Add Event</h3>
                <form id="eventForm">
                    <input type="hidden" id="eventDate" name="date">
                    <input type="text" name="title" placeholder="Event Title" required>
                    <textarea name="description" placeholder="Event Description"></textarea>
                    <input type="datetime-local" name="start_time" required>
                    <input type="datetime-local" name="end_time" required>
                    
                    <div class="label-container">
                        <h4>Labels</h4>
                        <div class="label-list" id="labelList">
                            <!-- Labels will be loaded here -->
                        </div>
                        <button type="button" id="addLabelBtn">Add New Label</button>
                        <div id="labelInput" class="label-input">
                            <input type="text" id="newLabelName" placeholder="Enter label name">
                            <div>
                                <button type="button" id="saveLabelBtn">Save Label</button>
                                <button type="button" id="cancelLabelBtn">Cancel</button>
                            </div>
                        </div>
                    </div>

                    <button type="submit">Save Event</button>
                </form>
                <div id="saveMessage" class="save-message">Date Saved</div>

                <div class="event-list" id="eventList">
                    <!-- Events for selected day will be shown here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Calendar functionality
        let currentDate = new Date();
        let selectedDate = null;
        let events = JSON.parse(localStorage.getItem('calendarEvents')) || {};
        let labels = JSON.parse(localStorage.getItem('calendarLabels')) || [];

        function updateCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            document.getElementById('currentMonth').textContent = 
                `${currentDate.toLocaleString('default', { month: 'long' })} ${year}`;

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startingDay = firstDay.getDay();
            const totalDays = lastDay.getDate();

            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';

            // Add day headers
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            days.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.textContent = day;
                dayHeader.style.fontWeight = 'bold';
                grid.appendChild(dayHeader);
            });

            // Add empty cells for days before the first day of the month
            for (let i = 0; i < startingDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day';
                grid.appendChild(emptyDay);
            }

            // Add days of the month
            for (let day = 1; day <= totalDays; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;

                const currentDay = new Date(year, month, day);
                if (currentDay.toDateString() === new Date().toDateString()) {
                    dayElement.classList.add('today');
                }

                // Check if day has events
                const dateStr = currentDay.toISOString().split('T')[0];
                if (events[dateStr] && events[dateStr].length > 0) {
                    dayElement.classList.add('has-events');
                }

                dayElement.addEventListener('click', () => selectDate(currentDay));
                grid.appendChild(dayElement);
            }
        }

        function selectDate(date) {
            selectedDate = date;
            document.getElementById('eventDate').value = date.toISOString().split('T')[0];
            loadEventsForDate(date);
        }

        function loadEventsForDate(date) {
            const dateStr = date.toISOString().split('T')[0];
            const eventList = document.getElementById('eventList');
            eventList.innerHTML = '';

            if (events[dateStr]) {
                events[dateStr].forEach(event => {
                    const eventElement = document.createElement('div');
                    eventElement.className = 'event-item';
                    eventElement.style.borderLeft = `4px solid ${event.color || '#4CAF50'}`;
                    eventElement.innerHTML = `
                        <h4>${event.title}</h4>
                        <p>${event.description}</p>
                        <small>${new Date(event.start_time).toLocaleTimeString()} - 
                               ${new Date(event.end_time).toLocaleTimeString()}</small>
                    `;
                    eventList.appendChild(eventElement);
                });
            }
        }

        function loadLabels() {
            const labelList = document.getElementById('labelList');
            labelList.innerHTML = '';

            labels.forEach(label => {
                const labelElement = document.createElement('div');
                labelElement.className = 'label-item';
                labelElement.style.backgroundColor = label.color;
                labelElement.textContent = label.name;
                labelElement.dataset.id = label.id;
                labelElement.addEventListener('click', () => selectLabel(label.id));
                labelList.appendChild(labelElement);
            });
        }

        function selectLabel(labelId) {
            const labels = document.querySelectorAll('.label-item');
            labels.forEach(label => {
                label.classList.toggle('selected', label.dataset.id === labelId.toString());
            });
        }

        // Event Listeners
        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateCalendar();
        });

        document.getElementById('addLabelBtn').addEventListener('click', () => {
            const labelInput = document.getElementById('labelInput');
            labelInput.style.display = 'block';
            document.getElementById('newLabelName').focus();
        });

        document.getElementById('cancelLabelBtn').addEventListener('click', () => {
            const labelInput = document.getElementById('labelInput');
            labelInput.style.display = 'none';
            document.getElementById('newLabelName').value = '';
        });

        document.getElementById('saveLabelBtn').addEventListener('click', () => {
            const name = document.getElementById('newLabelName').value.trim();
            if (name) {
                const newLabel = {
                    id: Date.now(),
                    name: name,
                    color: '#4CAF50'
                };
                labels.push(newLabel);
                localStorage.setItem('calendarLabels', JSON.stringify(labels));
                loadLabels();
                const labelInput = document.getElementById('labelInput');
                labelInput.style.display = 'none';
                document.getElementById('newLabelName').value = '';
            }
        });

        document.getElementById('newLabelName').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('saveLabelBtn').click();
            }
        });

        document.getElementById('eventForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const selectedLabel = document.querySelector('.label-item.selected');
            
            const event = {
                title: formData.get('title'),
                description: formData.get('description'),
                start_time: formData.get('start_time'),
                end_time: formData.get('end_time'),
                color: selectedLabel ? selectedLabel.style.backgroundColor : '#4CAF50'
            };

            const dateStr = formData.get('date');
            if (!events[dateStr]) {
                events[dateStr] = [];
            }
            events[dateStr].push(event);
            localStorage.setItem('calendarEvents', JSON.stringify(events));

            // Show save message
            const saveMessage = document.getElementById('saveMessage');
            saveMessage.style.display = 'block';
            setTimeout(() => {
                saveMessage.style.display = 'none';
            }, 3000);

            // Reset form and update calendar
            e.target.reset();
            updateCalendar();
            loadEventsForDate(new Date(dateStr));
        });

        // Initialize
        updateCalendar();
        loadLabels();
    </script>
    <script src="js/accessibility.js"></script>
</body>
</html> 