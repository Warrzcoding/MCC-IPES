#!/usr/bin/env python3
import re

file_path = r"c:\Users\9X\Documents\applaravel\ORIGIPES\FILES_BCKUP\MCC-IPES\resources\views\pages\add-students.blade.php"

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add @if before DOMContentLoaded for success alert
pattern1 = r"(\}\);\n\n)(document\.addEventListener\('DOMContentLoaded', function\(\) \{\n    // Show success message with SweetAlert)"
replacement1 = r"\1@if(session('message') && session('message_type') == 'success')\n\2"

content = re.sub(pattern1, replacement1, content)

# Add @endif after the Swal.fire for success alert
pattern2 = r"(        hideClass: \{\n            popup: 'animate__animated animate__fadeOutUp'\n        \}\n    \}\);\n\}\);\n)(\n// Form submission handling with SweetAlert)"
replacement2 = r"\1@endif\2"

content = re.sub(pattern2, replacement2, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("File updated successfully")
