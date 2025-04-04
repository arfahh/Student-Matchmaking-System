function searchProjects() {
    const searchTerm = document.getElementById('search-bar').value.toLowerCase();
    const projects = document.querySelectorAll('.project-item');

    projects.forEach(project => {
        const title = project.getAttribute('data-title').toLowerCase();
        const skills = project.getAttribute('data-skills').toLowerCase();

        if (title.includes(searchTerm) || skills.includes(searchTerm)) {
            project.style.display = 'block';
        } else {
            project.style.display = 'none';
        }
    });
}
