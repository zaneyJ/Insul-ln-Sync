<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Reminder</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/accessibility.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/accessibility_widget.php'; ?>
    <div class="container">
        <h1>Reminders</h1>
        <div class="box">
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 32px; margin-bottom: 0;">
                <div class="reminder-box" id="quickReminder">
                    <h2>Quick Reminder</h2>
                    <div class="input-group">
                        <label for="quickReminderLabel">Label</label>
                        <input type="text" id="quickReminderLabel" placeholder="Enter reminder label">
                    </div>
                    <div class="input-group">
                        <label for="minutes">Duration (minutes)</label>
                        <input type="number" id="minutes" placeholder="Enter minutes" min="1" max="86400">
                    </div>
                    <button onclick="setQuickReminder()">Set Quick Reminder</button>
                    <div id="quickReminderStatus"></div>
                    <div id="quickReminderCountdown"></div>
                    <div id="quickReminderLabelDisplay" class="reminder-label"></div>
                </div>

                <div class="reminder-box" id="scheduledReminder">
                    <h2>Schedule a Reminder</h2>
                    <div class="input-group">
                        <label for="scheduledReminderLabel">Label</label>
                        <input type="text" id="scheduledReminderLabel" placeholder="Enter reminder label">
                    </div>
                    <div class="input-group">
                        <label for="scheduleInput">Date and Time</label>
                        <input type="datetime-local" id="scheduleInput">
                    </div>
                    <button onclick="scheduleReminder()">Schedule</button>
                    <div id="countdown"></div>
                    <div id="scheduledReminderLabelDisplay" class="reminder-label"></div>
                </div>
            </div>
        </div>
        <hr style="margin: 40px 0; border: none; border-top: 2px solid #e0e0e0;" />
        <!-- Embedded Calendar Start -->
        <div class="container" id="calendar-section">
            <h1>Calendar</h1>
            <div class="calendar-container">
                <div class="calendar">
                    <div class="calendar-header">
                        <button id="prevMonth">&lt;</button>
                        <h2 id="currentMonth">Month Year</h2>
                        <button id="nextMonth">&gt;</button>
                    </div>
                    <div class="calendar-grid" id="calendarGrid"></div>
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
                            <div class="label-list" id="labelList"></div>
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
                    <div class="event-list" id="eventList"></div>
                </div>
            </div>
        </div>
        <!-- Embedded Calendar End -->
    </div>
    <script>
        let activeReminders = {
            quick: null,
            scheduled: null
        };

        function showMessage(elementId, message) {
            const element = document.getElementById(elementId);
            element.textContent = message;
            setTimeout(() => {
                element.textContent = '';
            }, 3000);
        }

        function updateBoxState(boxId, isActive) {
            const box = document.getElementById(boxId);
            if (isActive) {
                box.classList.add('active');
            } else {
                box.classList.remove('active');
            }
        }

        function formatTime(ms) {
            const days = Math.floor(ms / (1000 * 60 * 60 * 24));
            const hours = Math.floor((ms % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((ms % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((ms % (1000 * 60)) / 1000);

            let timeString = '';
            if (days > 0) timeString += `${days}d `;
            if (hours > 0) timeString += `${hours}h `;
            if (minutes > 0) timeString += `${minutes}m `;
            timeString += `${seconds}s`;
            return timeString;
        }

        function setQuickReminder() {
            const minutesInput = document.getElementById('minutes');
            const labelInput = document.getElementById('quickReminderLabel');
            const minutes = parseInt(minutesInput.value);
            const label = labelInput.value.trim() || 'Quick Reminder';
            const ms = minutes * 60000;

            if (!isNaN(ms) && ms > 0 && ms <= 5184000000) { // Max 60 days (2 months)
                // Clear any existing quick reminder
                if (activeReminders.quick) {
                    clearTimeout(activeReminders.quick);
                }

                updateBoxState('quickReminder', true);
                showMessage('quickReminderStatus', 'Reminder set!');
                document.getElementById('quickReminderLabelDisplay').textContent = `Label: ${label}`;
                const countdownEl = document.getElementById('quickReminderCountdown');
                const endTime = Date.now() + ms;

                const interval = setInterval(() => {
                    const remaining = endTime - Date.now();
                    if (remaining <= 0) {
                        clearInterval(interval);
                        countdownEl.textContent = '';
                        document.getElementById('quickReminderLabelDisplay').textContent = '';
                        updateBoxState('quickReminder', false);
                        alert(`${label} - Time's up!`);
                    } else {
                        countdownEl.textContent = `Time remaining: ${formatTime(remaining)}`;
                    }
                }, 1000);

                activeReminders.quick = setTimeout(() => {
                    clearInterval(interval);
                    updateBoxState('quickReminder', false);
                }, ms);
            } else {
                showMessage('quickReminderStatus', 'Please enter a valid number between 1 and 86400 minutes (60 days).');
            }
        }

        function scheduleReminder() {
            const input = document.getElementById("scheduleInput").value;
            const labelInput = document.getElementById("scheduledReminderLabel");
            const label = labelInput.value.trim() || 'Scheduled Reminder';

            if (!input) {
                showMessage('countdown', 'Please select a date and time.');
                return;
            }

            const targetTime = new Date(input).getTime();
            const now = Date.now();
            const msUntil = targetTime - now;

            if (msUntil <= 0) {
                showMessage('countdown', 'Please pick a future time.');
                return;
            }

            if (msUntil > 5184000000) { // More than 60 days (2 months)
                showMessage('countdown', 'Please pick a time within the next 2 months.');
                return;
            }

            // Clear any existing scheduled reminder
            if (activeReminders.scheduled) {
                clearInterval(activeReminders.scheduled);
            }

            updateBoxState('scheduledReminder', true);
            showMessage('countdown', 'Reminder scheduled!');
            document.getElementById('scheduledReminderLabelDisplay').textContent = `Label: ${label}`;
            const countdownEl = document.getElementById("countdown");

            const interval = setInterval(() => {
                const remaining = targetTime - Date.now();
                if (remaining <= 0) {
                    clearInterval(interval);
                    countdownEl.textContent = "";
                    document.getElementById('scheduledReminderLabelDisplay').textContent = '';
                    updateBoxState('scheduledReminder', false);
                    alert(`${label} - Reminder time reached!`);
                } else {
                    countdownEl.textContent = `Time remaining: ${formatTime(remaining)}`;
                }
            }, 1000);

            activeReminders.scheduled = interval;
        }

        // Set minimum datetime to current time and maximum to 2 months from now
        const now = new Date();
        const maxDate = new Date(now.getTime() + 5184000000); // 60 days from now
        document.getElementById('scheduleInput').min = now.toISOString().slice(0, 16);
        document.getElementById('scheduleInput').max = maxDate.toISOString().slice(0, 16);

        // Calendar functionality (embedded)
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
        // Calendar navigation with 5-year limit
        const minYear = new Date().getFullYear();
        const maxYear = minYear + 5;
        document.getElementById('prevMonth').addEventListener('click', () => {
            if (currentDate.getFullYear() > minYear - 5 || (currentDate.getFullYear() === minYear - 5 && currentDate.getMonth() > 0)) {
                currentDate.setMonth(currentDate.getMonth() - 1);
                updateCalendar();
            }
        });
        document.getElementById('nextMonth').addEventListener('click', () => {
            if (currentDate.getFullYear() < maxYear || (currentDate.getFullYear() === maxYear && currentDate.getMonth() < 11)) {
                currentDate.setMonth(currentDate.getMonth() + 1);
                updateCalendar();
            }
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