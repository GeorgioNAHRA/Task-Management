document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        events: tasks.map(task => ({
            id: task.IDTache,
            title: task.Titre,
            start: task.datedebut,
            end: task.datefin
        })),
        eventClick: function (info) {
            var task = tasks.find(t => t.IDTache == info.event.id);
            document.getElementById('taskTitle').innerText = task.Titre;
            document.getElementById('taskDescription').innerText = task.description;
            document.getElementById('taskStart').innerText = task.datedebut;
            document.getElementById('taskEnd').innerText = task.datefin;
            document.getElementById('taskUsers').innerText = task.IDUser || 'Aucun utilisateur';
            document.getElementById('taskId').value = task.IDTache;

            document.getElementById('taskModal').style.display = 'block';
        }
    });
    calendar.render();

    var modal = document.getElementById("taskModal");
    var closeModal = document.getElementsByClassName("close")[0];
    closeModal.onclick = function () {
        modal.style.display = "none";
    };
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
});