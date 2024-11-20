document.addEventListener('DOMContentLoaded', () => {
    const calendar = document.getElementById('calendar');
    const currentMonthYear = document.getElementById('currentMonthYear');
    const prevMonthButton = document.getElementById('prevMonth');
    const nextMonthButton = document.getElementById('nextMonth');
    const prevYearButton = document.getElementById('prevYear');
    const nextYearButton = document.getElementById('nextYear');
    const form = document.getElementById('projectForm');
    const projectNameInput = document.getElementById('projectName');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const selectedDateInput = document.createElement('input');

    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth();
    let projects = [];

    selectedDateInput.type = 'hidden';
    selectedDateInput.id = 'selectedDate';
    form.appendChild(selectedDateInput);

    const monthNames = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ];

    const updateCalendar = () => {
        currentMonthYear.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        generateCalendar(currentYear, currentMonth);
        displayProjects();
    };

    const generateCalendar = (year, month) => {
        calendar.innerHTML = '';

        const monthDiv = document.createElement('div');
        monthDiv.className = 'month';
        const monthTitle = document.createElement('h2');
        monthTitle.textContent = `${monthNames[month]} ${year}`;
        monthDiv.appendChild(monthTitle);

        const weekdaysDiv = document.createElement('div');
        weekdaysDiv.className = 'weekdays';
        ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'].forEach(day => {
            const dayDiv = document.createElement('div');
            dayDiv.textContent = day;
            weekdaysDiv.appendChild(dayDiv);
        });
        monthDiv.appendChild(weekdaysDiv);

        const daysDiv = document.createElement('div');
        daysDiv.className = 'days';

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const prevMonthDays = firstDay === 0 ? 6 : firstDay - 1;
        for (let i = 0; i < prevMonthDays; i++) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'day';
            daysDiv.appendChild(emptyDiv);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayDiv = document.createElement('div');
            dayDiv.className = 'day';
            dayDiv.textContent = day;
            dayDiv.dataset.date = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            dayDiv.addEventListener('click', (e) => {
                const selectedDate = e.target.dataset.date;
                selectedDateInput.value = selectedDate;
                startDateInput.value = selectedDate;
                endDateInput.value = selectedDate;
                form.scrollIntoView({ behavior: 'smooth' });
            });
            daysDiv.appendChild(dayDiv);
        }

        monthDiv.appendChild(daysDiv);
        calendar.appendChild(monthDiv);
    };

    const generateRandomColor = () => {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    };

    const addProjectToCalendar = (name, startDate, endDate) => {
        const color = generateRandomColor();
        projects.push({ name, startDate, endDate, color });
        displayProjects();
    };

    const displayProjects = () => {
        // Clear existing project displays
        document.querySelectorAll('.day').forEach(dayDiv => {
            dayDiv.classList.remove('project');
            dayDiv.style.backgroundColor = '';
            const projectList = dayDiv.querySelector('.project-list');
            if (projectList) {
                projectList.remove();
            }
        });

        // Display each project on the correct dates
        projects.forEach(project => {
            const { name, startDate, endDate, color } = project;
            const start = new Date(startDate);
            const end = new Date(endDate);

            document.querySelectorAll('.day').forEach(dayDiv => {
                const currentDate = new Date(dayDiv.dataset.date);
                if (currentDate >= start && currentDate <= end) {
                    dayDiv.classList.add('project');
                    dayDiv.style.backgroundColor = color;
                    const projectList = dayDiv.querySelector('.project-list') || document.createElement('ul');
                    projectList.className = 'project-list';

                    const projectItem = document.createElement('li');
                    projectItem.textContent = name;

                    projectList.appendChild(projectItem);
                    if (!dayDiv.contains(projectList)) {
                        dayDiv.appendChild(projectList);
                    }
                }
            });
        });
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const name = projectNameInput.value;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        addProjectToCalendar(name, startDate, endDate);

        form.reset();
        selectedDateInput.value = '';
    });

    prevMonthButton.addEventListener('click', () => {
        if (currentMonth === 0) {
            currentMonth = 11;
            currentYear--;
        } else {
            currentMonth--;
        }
        updateCalendar();
    });

    nextMonthButton.addEventListener('click', () => {
        if (currentMonth === 11) {
            currentMonth = 0;
            currentYear++;
        } else {
            currentMonth++;
        }
        updateCalendar();
    });

    prevYearButton.addEventListener('click', () => {
        currentYear--;
        updateCalendar();
    });

    nextYearButton.addEventListener('click', () => {
        currentYear++;
        updateCalendar();
    });

    updateCalendar();
});
