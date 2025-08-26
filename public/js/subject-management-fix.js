// Fix for edit modal not loading data
document.addEventListener('DOMContentLoaded', function() {
    console.log("Subject management fix loaded");
    
    // Override the loadSubjectData function
    window.originalLoadSubjectData = window.loadSubjectData;
    
    window.loadSubjectData = function(code, name, department, year, section, semester, instructor, subjectType) {
        console.log("Fixed loadSubjectData called with:", {code, name, department, year, section, semester, instructor, subjectType});
        
        try {
            // Set form values
            document.getElementById('editSubjectCode').value = code;
            document.getElementById('editSubjectName').value = name;
            document.getElementById('editDepartment').value = department;
            document.getElementById('editYear').value = year;
            document.getElementById('editSemester').value = semester || '';
            document.getElementById('editSubjectType').value = subjectType || 'Major';
            document.getElementById('originalSubjectCode').value = code;
            
            // Set section
            const sectionSelect = document.getElementById('editSection');
            if (sectionSelect) {
                // First try to select by value
                sectionSelect.value = section || '';
                
                // If that didn't work, try to find a matching option
                if (!sectionSelect.value && section) {
                    for (let i = 0; i < sectionSelect.options.length; i++) {
                        if (sectionSelect.options[i].textContent.trim() === section.trim()) {
                            sectionSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            }
            
            // Set instructor
            const instructorSelect = document.getElementById('editInstructor');
            if (instructorSelect) {
                // Reset to empty first
                instructorSelect.value = '';
                
                // If we have an instructor, try to find a match
                if (instructor) {
                    // First try to select by value
                    instructorSelect.value = instructor;
                    
                    // If that didn't work, try to find a matching option by text
                    if (!instructorSelect.value) {
                        for (let i = 0; i < instructorSelect.options.length; i++) {
                            if (instructorSelect.options[i].textContent.trim() === instructor.trim()) {
                                instructorSelect.selectedIndex = i;
                                console.log("Selected instructor by text:", instructor);
                                break;
                            }
                        }
                    }
                }
            }
            
            console.log("Form values set successfully");
        } catch (error) {
            console.error("Error in fixed loadSubjectData:", error);
        }
    };
    
    // Fix for edit buttons
    const editButtons = document.querySelectorAll('.btn-outline-primary[data-bs-target="#editModal"]');
    editButtons.forEach(button => {
        const originalOnclick = button.getAttribute('onclick');
        if (originalOnclick && originalOnclick.includes('instructor_staff_id')) {
            // Replace instructor_staff_id with assign_instructor
            const newOnclick = originalOnclick.replace('instructor_staff_id', 'assign_instructor');
            button.setAttribute('onclick', newOnclick);
            console.log("Fixed edit button onclick:", newOnclick);
        }
    });
    
    // Override the viewSubjectData function
    window.originalViewSubjectData = window.viewSubjectData;
    
    window.viewSubjectData = function(subjectCode, subjectName, department, year, section, semester, instructor, subjectType) {
        console.log("Fixed viewSubjectData called with:", {subjectCode, subjectName, department, year, section, semester, instructor, subjectType});
        
        try {
            // Set view elements
            document.getElementById('viewSubjectCode').textContent = subjectCode;
            document.getElementById('viewSubjectName').textContent = subjectName;
            document.getElementById('viewDepartment').textContent = department;
            document.getElementById('viewYear').textContent = year;
            document.getElementById('viewSection').textContent = section || 'N/A';
            document.getElementById('viewSemester').textContent = semester || 'N/A';
            document.getElementById('viewInstructor').textContent = instructor || 'Not Assigned';
            document.getElementById('viewSubjectType').textContent = subjectType || 'Major';
            
            console.log("View elements set successfully");
        } catch (error) {
            console.error("Error in fixed viewSubjectData:", error);
        }
    };
});