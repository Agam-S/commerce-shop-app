function showSection(sectionId) {
    const sections = ["thread-section", "comment-section", "user-section", "manage-product-section", "product-section", "feedback-section"];

    sections.forEach((section) => {
        document.getElementById(section).style.display = 'none';
    });

    localStorage.setItem('lastSection', sectionId); // Store the sectionId value in localStorage
    document.getElementById(sectionId).style.display = 'block';
}

const lastSection = localStorage.getItem('lastSection');
if (lastSection) {
    showSection(lastSection);
}
