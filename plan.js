// Participant du projet
let sidebar = document.querySelector(".sidebar");
      let sidebarBtn = document.querySelector(".sidebarBtn");
      sidebarBtn.onclick = function () {
        sidebar.classList.toggle("active");
        if (sidebar.classList.contains("active")) {
          sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
        } else sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
      };

      let tasks = [];
      let participants = [];
      let teams = [];

      function addTask() {
        const taskInput = document.getElementById('new-task');
        const taskDeadlineInput = document.getElementById('new-task-deadline');
        const taskName = taskInput.value;
        const taskDeadline = taskDeadlineInput.value;

        if (taskName && taskDeadline) {
          const task = {
            id: tasks.length + 1,
            name: taskName,
            deadline: taskDeadline,
            done: false
          };
          tasks.push(task);
          renderTasks();
          taskInput.value = '';
          taskDeadlineInput.value = '';
        }
      }

      function deleteTask(taskId) {
        tasks = tasks.filter(task => task.id !== taskId);
        renderTasks();
      }

      function toggleTaskDone(taskId) {
        const task = tasks.find(task => task.id === taskId);
        if (task) {
          task.done = !task.done;
          renderTasks();
        }
      }

      function renderTasks() {
        const taskList = document.querySelector('.task-list');
        taskList.innerHTML = `
          <div class="task-header">
            <span>Tâche</span>
            <span>Date Limite</span>
            <span>Actions</span>
          </div>
        `;

        tasks.forEach(task => {
          const taskElement = document.createElement('div');
          taskElement.className = 'task';
          taskElement.innerHTML = `
            <input type="checkbox" ${task.done ? 'checked' : ''} onclick="toggleTaskDone(${task.id})">
            <span class="task-name ${task.done ? 'done' : ''}">${task.name}</span>
            <span class="task-deadline">${task.deadline}</span>
            <button onclick="deleteTask(${task.id})">Supprimer</button>
          `;
          taskList.appendChild(taskElement);
        });
      }

      function addParticipant() {
        const participantNameInput = document.getElementById('new-participant-name');
        const participantRoleInput = document.getElementById('new-participant-role');
        const participantName = participantNameInput.value;
        const participantRole = participantRoleInput.value;

        if (participantName && participantRole) {
          const participant = {
            name: participantName,
            role: participantRole
          };
          participants.push(participant);
          renderParticipants();
          participantNameInput.value = '';
          participantRoleInput.value = '';
        }
      }

      function renderParticipants() {
        const participantList = document.querySelector('.participant-list');
        participantList.innerHTML = `
          <div class="participant-header">
            <span>Nom</span>
            <span>Rôle</span>
          </div>
        `;

        participants.forEach(participant => {
          const participantElement = document.createElement('div');
          participantElement.className = 'participant';
          participantElement.innerHTML = `
            <span class="participant-name">${participant.name}</span>
            <span class="participant-role">${participant.role}</span>
          `;
          participantList.appendChild(participantElement);
        });
      }

      function addTeam() {
        const teamNameInput = document.getElementById('new-team-name');
        const teamName = teamNameInput.value;

        if (teamName) {
          const team = {
            id: teams.length + 1,
            name: teamName,
            members: []
          };
          teams.push(team);
          renderTeams();
          teamNameInput.value = '';
        }
      }

      function renderTeams() {
        const teamList = document.querySelector('.team-list');
        teamList.innerHTML = `
          <div class="team-header">
            <span>Équipe</span>
            <span>Membres</span>
            <span>Actions</span>
          </div>
        `;

        teams.forEach(team => {
          const teamElement = document.createElement('div');
          teamElement.className = 'team';
          teamElement.innerHTML = `
            <span class="team-name">${team.name}</span>
            <span class="team-members">${team.members.join(', ')}</span>
            <button onclick="addMemberToTeam(${team.id})">Ajouter Membre</button>
            <button onclick="deleteTeam(${team.id})">Supprimer Équipe</button>
          `;
          teamList.appendChild(teamElement);
        });
      }

      function addMemberToTeam(teamId) {
        const memberName = prompt('Nom du membre à ajouter:');
        if (memberName) {
          const team = teams.find(team => team.id === teamId);
          if (team) {
            team.members.push(memberName);
            renderTeams();
          }
        }
      }

      function deleteTeam(teamId) {
        teams = teams.filter(team => team.id !== teamId);
        renderTeams();
      }

      renderTasks();
      renderParticipants();
      renderTeams();


