# Department Mapping Guide for OPES System

## 📋 **Overview**
This guide explains the relationship between student courses (in `users` table) and staff departments (in `staff` table) for the evaluation system.

## 🗄️ **Database Structure**

### **Users Table (Students)**
```sql
users table:
- id
- username
- email
- password
- full_name
- school_id
- role (student/admin)
- course ← This is the key field for students
- status
- profile_image
- created_at
- updated_at
```

### **Staff Table (Instructors/Staff)**
```sql
staff table:
- id
- staff_id
- full_name
- email
- phone
- department ← This is the key field for staff
- staff_type (teaching/non-teaching)
- image_path
- profile_image
- created_at
- updated_at
```

## 🎯 **Course to Department Mapping**

### **Student Courses → Staff Departments**

| Student Course (users.course) | Staff Department (staff.department) | Description |
|-------------------------------|-------------------------------------|-------------|
| `BSIT` | `BSIT` | Bachelor of Science in Information Technology |
| `BSHM` | `BSHM` | Bachelor of Science in Hospitality Management |
| `BSBA` | `BSBA` | Bachelor of Science in Business Administration |
| `BSED` | `BSED` | Bachelor of Science in Education |
| `BEED` | `BEED` | Bachelor of Elementary Education |

## 🔄 **How the Mapping Works**

### **1. Student Login Process**
```php
// When a student logs in
$user = auth()->user(); // Gets current logged-in user
$studentCourse = $user->course; // Gets student's course (e.g., 'BSIT')
```

### **2. Staff Filtering Logic**
```php
// Map student course to staff department
$courseToDepartmentMap = [
    'BSIT' => 'BSIT',
    'BSHM' => 'BSHM', 
    'BSBA' => 'BSBA',
    'BSED' => 'BSED',
    'BEED' => 'BEED'
];

$studentDepartment = $courseToDepartmentMap[$studentCourse] ?? null;

// Get teaching staff from the same department
$teachingStaff = Staff::where('staff_type', 'teaching')
    ->when($studentDepartment, function($query) use ($studentDepartment) {
        return $query->where('department', $studentDepartment);
    })
    ->get();
```

## 📝 **Example Scenarios**

### **Scenario 1: BSIT Student**
```php
// Student data
$student = User::find(1);
$student->course = 'BSIT';

// Staff filtering
$teachingStaff = Staff::where('staff_type', 'teaching')
    ->where('department', 'BSIT')
    ->get();

// Result: Only shows teaching staff with department = 'BSIT'
```

### **Scenario 2: BSHM Student**
```php
// Student data
$student = User::find(2);
$student->course = 'BSHM';

// Staff filtering
$teachingStaff = Staff::where('staff_type', 'teaching')
    ->where('department', 'BSHM')
    ->get();

// Result: Only shows teaching staff with department = 'BSHM'
```

## 🛠️ **Implementation in Controllers**

### **EvaluationController.php**
```php
public function showForm()
{
    // Get current user (student)
    $user = auth()->user();
    $studentCourse = $user->course;
    
    // Map course to department
    $courseToDepartmentMap = [
        'BSIT' => 'BSIT',
        'BSHM' => 'BSHM', 
        'BSBA' => 'BSBA',
        'BSED' => 'BSED',
        'BEED' => 'BEED'
    ];
    
    $studentDepartment = $courseToDepartmentMap[$studentCourse] ?? null;
    
    // Filter teaching staff by department
    $teachingStaff = Staff::where('staff_type', 'teaching')
        ->when($studentDepartment, function($query) use ($studentDepartment) {
            return $query->where('department', $studentDepartment);
        })
        ->get();
        
    // Non-teaching staff (no filtering needed)
    $nonTeachingStaff = Staff::where('staff_type', 'non-teaching')->get();
    
    // ... rest of the method
}
```

### **DashboardController.php**
```php
if ($page === 'evaluates') {
    // Same logic as EvaluationController
    $user = Auth::user();
    $studentCourse = $user->course;
    
    $courseToDepartmentMap = [
        'BSIT' => 'BSIT',
        'BSHM' => 'BSHM', 
        'BSBA' => 'BSBA',
        'BSED' => 'BSED',
        'BEED' => 'BEED'
    ];
    
    $studentDepartment = $courseToDepartmentMap[$studentCourse] ?? null;
    
    $teachingStaff = Staff::where('staff_type', 'teaching')
        ->when($studentDepartment, function($query) use ($studentDepartment) {
            return $query->where('department', $studentDepartment);
        })
        ->get();
}
```

## 📊 **Database Examples**

### **Sample Users (Students)**
```sql
INSERT INTO users (username, email, full_name, course, role) VALUES
('john.doe', 'john@example.com', 'John Doe', 'BSIT', 'student'),
('jane.smith', 'jane@example.com', 'Jane Smith', 'BSHM', 'student'),
('mike.wilson', 'mike@example.com', 'Mike Wilson', 'BSBA', 'student');
```

### **Sample Staff (Instructors)**
```sql
INSERT INTO staff (staff_id, full_name, email, department, staff_type) VALUES
('TH001', 'Prof. Alice Johnson', 'alice@example.com', 'BSIT', 'teaching'),
('TH002', 'Prof. Bob Brown', 'bob@example.com', 'BSIT', 'teaching'),
('TH003', 'Prof. Carol Davis', 'carol@example.com', 'BSHM', 'teaching'),
('TH004', 'Prof. David Wilson', 'david@example.com', 'BSBA', 'teaching'),
('NT001', 'Ms. Eve Admin', 'eve@example.com', 'Registrar', 'non-teaching');
```

## 🎯 **Evaluation Flow**

1. **Student Login**: Student logs in with their course stored in `users.course`
2. **Department Mapping**: System maps student course to staff department
3. **Staff Filtering**: Only teaching staff from matching department are shown
4. **Evaluation**: Student can only evaluate instructors from their department

## ⚠️ **Important Notes**

- **Teaching Staff**: Filtered by department matching student's course
- **Non-Teaching Staff**: No filtering - all students can evaluate all non-teaching staff
- **Department Names**: Must exactly match the course codes (BSIT, BSHM, etc.)
- **Case Sensitivity**: Department names are case-sensitive

## 🔧 **Troubleshooting**

### **No Teaching Staff Found**
If a student sees "No teaching staff found for your department", check:
1. Student's course is correctly set in `users.course`
2. Teaching staff exist with matching `staff.department`
3. Staff have `staff_type = 'teaching'`

### **Debug Query**
```php
// Debug: Check what departments exist
$departments = Staff::where('staff_type', 'teaching')
    ->distinct()
    ->pluck('department');
    
// Debug: Check student's course
$studentCourse = auth()->user()->course;
```

This mapping ensures that students only evaluate instructors who are actually teaching in their specific department/course! 🎓✨ 