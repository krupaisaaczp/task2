document.addEventListener('DOMContentLoaded', function () {
    // Fetch members and tasks when the page loads
    fetchMembers();
    fetchTasks();

    // Add member form submission
    const addMemberForm = document.getElementById('add-member-form');
    addMemberForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Get the input values
        const name = document.getElementById('name').value;
        const role = document.getElementById('role').value;
        const email = document.getElementById('email').value;

        // Make the POST request to add a new member
        fetch('http://localhost/uptoskills/Leader_Dashboard/backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ name, role, email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                fetchMembers(); // Refresh the members list after adding
                addMemberForm.reset(); // Reset the form fields
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error("Error adding member:", error);
            alert("There was an error adding the member.");
        });
    });

    // Assign task form submission
    const assignTaskForm = document.getElementById('assign-task-form');
    assignTaskForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Get the form values
        const taskName = document.getElementById('taskName').value;
        const description = document.getElementById('description').value;
        const deadline = document.getElementById('deadline').value;
        const priority = document.getElementById('priority').value;
        const memberId = document.getElementById('memberId').value;

        // Make the POST request to assign the task
        fetch('http://localhost/uptoskills/Leader_Dashboard/backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ taskName, description, deadline, priority, memberId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                fetchTasks(); // Refresh the tasks list after assigning a task
                assignTaskForm.reset(); // Reset the form fields
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error("Error assigning task:", error);
            alert("There was an error assigning the task.");
        });
    });

    // Fetch members for dropdown
    function fetchMembers() {
        fetch('http://localhost/uptoskills/Leader_Dashboard/backend.php?action=get_members')
        .then(response => response.json())
        .then(members => {
            const membersList = document.getElementById('members-list');
            const memberSelect = document.getElementById('memberId');
            membersList.innerHTML = '';
            memberSelect.innerHTML = ''; // Clear the member dropdown

            // Populate the members list
            members.forEach(member => {
                // Add to members list in the UI
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = `<strong>${member.name}</strong><br><small>${member.role} - ${member.email}</small>`;
                membersList.appendChild(li);

                // Add to the dropdown for task assignment
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = `${member.name} (${member.role})`;
                memberSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error fetching members:", error);
            alert("There was an error fetching the members.");
        });
    }

    // Fetch tasks for displaying
    function fetchTasks() {
        fetch('http://localhost/uptoskills/Leader_Dashboard/backend.php?action=get_tasks')
        .then(response => response.json())
        .then(tasks => {
            const tasksList = document.getElementById('tasks-list');
            tasksList.innerHTML = '';

            // Populate the tasks list in the UI
            tasks.forEach(task => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <div>
                        <strong>${task.name}</strong><br>
                        <small>${task.description}</small><br>
                        <small>Deadline: ${task.deadline}</small><br>
                        <span class="badge bg-${task.priority === 'High' ? 'danger' : task.priority === 'Medium' ? 'warning' : 'success'}">${task.priority}</span>
                    </div>
                    <div>
                        <small>Assigned to: ${task.member_name}</small>
                    </div>
                `;
                tasksList.appendChild(li);
            });
        })
        .catch(error => {
            console.error("Error fetching tasks:", error);
            alert("There was an error fetching the tasks.");
        });
    }
});
