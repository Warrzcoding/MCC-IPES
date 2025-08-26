<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SemisSubject;

class SemisSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // BSIT - 1ST YEAR
            ['semester' => '1', 'department' => 'BSIT', 'year' => '1ST YEAR', 'subcode' => 'IT101', 'subname' => 'Introduction to Computing'],
            ['semester' => '1', 'department' => 'BSIT', 'year' => '1ST YEAR', 'subcode' => 'MATH101', 'subname' => 'College Mathematics'],
            ['semester' => '1', 'department' => 'BSIT', 'year' => '1ST YEAR', 'subcode' => 'ENG101', 'subname' => 'English Communication'],
            ['semester' => '2', 'department' => 'BSIT', 'year' => '1ST YEAR', 'subcode' => 'IT102', 'subname' => 'Programming Fundamentals'],
            ['semester' => '2', 'department' => 'BSIT', 'year' => '1ST YEAR', 'subcode' => 'MATH102', 'subname' => 'Statistics'],

            // BSIT - 2ND YEAR
            ['semester' => '1', 'department' => 'BSIT', 'year' => '2ND YEAR', 'subcode' => 'IT201', 'subname' => 'Data Structures and Algorithms'],
            ['semester' => '1', 'department' => 'BSIT', 'year' => '2ND YEAR', 'subcode' => 'IT202', 'subname' => 'Database Management Systems'],
            ['semester' => '2', 'department' => 'BSIT', 'year' => '2ND YEAR', 'subcode' => 'IT203', 'subname' => 'Web Development'],
            ['semester' => '2', 'department' => 'BSIT', 'year' => '2ND YEAR', 'subcode' => 'IT204', 'subname' => 'Object-Oriented Programming'],

            // BSIT - 3RD YEAR
            ['semester' => '1', 'department' => 'BSIT', 'year' => '3RD YEAR', 'subcode' => 'IT301', 'subname' => 'System Analysis and Design'],
            ['semester' => '1', 'department' => 'BSIT', 'year' => '3RD YEAR', 'subcode' => 'IT302', 'subname' => 'Network Administration'],
            ['semester' => '2', 'department' => 'BSIT', 'year' => '3RD YEAR', 'subcode' => 'IT303', 'subname' => 'Software Engineering'],
            ['semester' => '2', 'department' => 'BSIT', 'year' => '3RD YEAR', 'subcode' => 'IT304', 'subname' => 'Mobile Application Development'],

            // BSIT - 4TH YEAR
            ['semester' => '1', 'department' => 'BSIT', 'year' => '4TH YEAR', 'subcode' => 'IT401', 'subname' => 'Capstone Project 1'],
            ['semester' => '1', 'department' => 'BSIT', 'year' => '4TH YEAR', 'subcode' => 'IT402', 'subname' => 'Information Security'],
            ['semester' => '2', 'department' => 'BSIT', 'year' => '4TH YEAR', 'subcode' => 'IT403', 'subname' => 'Capstone Project 2'],
            ['semester' => '2', 'department' => 'BSIT', 'year' => '4TH YEAR', 'subcode' => 'IT404', 'subname' => 'Practicum/Internship'],

            // BSBA - 1ST YEAR
            ['semester' => '1', 'department' => 'BSBA', 'year' => '1ST YEAR', 'subcode' => 'BA101', 'subname' => 'Introduction to Business'],
            ['semester' => '1', 'department' => 'BSBA', 'year' => '1ST YEAR', 'subcode' => 'ECON101', 'subname' => 'Principles of Economics'],
            ['semester' => '1', 'department' => 'BSBA', 'year' => '1ST YEAR', 'subcode' => 'ACCT101', 'subname' => 'Fundamentals of Accounting'],
            ['semester' => '2', 'department' => 'BSBA', 'year' => '1ST YEAR', 'subcode' => 'BA102', 'subname' => 'Business Mathematics'],
            ['semester' => '2', 'department' => 'BSBA', 'year' => '1ST YEAR', 'subcode' => 'ECON102', 'subname' => 'Microeconomics'],

            // BSBA - 2ND YEAR
            ['semester' => '1', 'department' => 'BSBA', 'year' => '2ND YEAR', 'subcode' => 'MKTG201', 'subname' => 'Principles of Marketing'],
            ['semester' => '1', 'department' => 'BSBA', 'year' => '2ND YEAR', 'subcode' => 'MGMT201', 'subname' => 'Principles of Management'],
            ['semester' => '2', 'department' => 'BSBA', 'year' => '2ND YEAR', 'subcode' => 'FIN201', 'subname' => 'Business Finance'],
            ['semester' => '2', 'department' => 'BSBA', 'year' => '2ND YEAR', 'subcode' => 'ACCT201', 'subname' => 'Cost Accounting'],

            // BSBA - 3RD YEAR
            ['semester' => '1', 'department' => 'BSBA', 'year' => '3RD YEAR', 'subcode' => 'BA301', 'subname' => 'Business Law'],
            ['semester' => '1', 'department' => 'BSBA', 'year' => '3RD YEAR', 'subcode' => 'MGMT301', 'subname' => 'Operations Management'],
            ['semester' => '2', 'department' => 'BSBA', 'year' => '3RD YEAR', 'subcode' => 'MGMT302', 'subname' => 'Strategic Management'],
            ['semester' => '2', 'department' => 'BSBA', 'year' => '3RD YEAR', 'subcode' => 'MKTG301', 'subname' => 'Digital Marketing'],

            // BSBA - 4TH YEAR
            ['semester' => '1', 'department' => 'BSBA', 'year' => '4TH YEAR', 'subcode' => 'BA401', 'subname' => 'Business Ethics'],
            ['semester' => '1', 'department' => 'BSBA', 'year' => '4TH YEAR', 'subcode' => 'BA402', 'subname' => 'Thesis Writing 1'],
            ['semester' => '2', 'department' => 'BSBA', 'year' => '4TH YEAR', 'subcode' => 'BA403', 'subname' => 'Thesis Writing 2'],
            ['semester' => '2', 'department' => 'BSBA', 'year' => '4TH YEAR', 'subcode' => 'BA404', 'subname' => 'Business Internship'],

            // BSHM - 1ST YEAR
            ['semester' => '1', 'department' => 'BSHM', 'year' => '1ST YEAR', 'subcode' => 'HM101', 'subname' => 'Introduction to Hospitality Management'],
            ['semester' => '1', 'department' => 'BSHM', 'year' => '1ST YEAR', 'subcode' => 'FS101', 'subname' => 'Food Service Operations'],
            ['semester' => '1', 'department' => 'BSHM', 'year' => '1ST YEAR', 'subcode' => 'COMM101', 'subname' => 'Communication Skills'],
            ['semester' => '2', 'department' => 'BSHM', 'year' => '1ST YEAR', 'subcode' => 'HM102', 'subname' => 'Tourism Geography'],
            ['semester' => '2', 'department' => 'BSHM', 'year' => '1ST YEAR', 'subcode' => 'FS102', 'subname' => 'Food and Beverage Service'],

            // BSHM - 2ND YEAR
            ['semester' => '1', 'department' => 'BSHM', 'year' => '2ND YEAR', 'subcode' => 'HM201', 'subname' => 'Hotel Operations Management'],
            ['semester' => '1', 'department' => 'BSHM', 'year' => '2ND YEAR', 'subcode' => 'TOUR201', 'subname' => 'Tour Guiding and Travel Services'],
            ['semester' => '2', 'department' => 'BSHM', 'year' => '2ND YEAR', 'subcode' => 'HM202', 'subname' => 'Customer Relations Management'],
            ['semester' => '2', 'department' => 'BSHM', 'year' => '2ND YEAR', 'subcode' => 'ACCT201', 'subname' => 'Hospitality Accounting'],

            // BSHM - 3RD YEAR
            ['semester' => '1', 'department' => 'BSHM', 'year' => '3RD YEAR', 'subcode' => 'HM301', 'subname' => 'Event Management'],
            ['semester' => '1', 'department' => 'BSHM', 'year' => '3RD YEAR', 'subcode' => 'REST301', 'subname' => 'Restaurant Management'],
            ['semester' => '2', 'department' => 'BSHM', 'year' => '3RD YEAR', 'subcode' => 'MKTG301', 'subname' => 'Hospitality Marketing'],
            ['semester' => '2', 'department' => 'BSHM', 'year' => '3RD YEAR', 'subcode' => 'HM302', 'subname' => 'Front Office Operations'],

            // BSHM - 4TH YEAR
            ['semester' => '1', 'department' => 'BSHM', 'year' => '4TH YEAR', 'subcode' => 'HM401', 'subname' => 'Hotel Administration'],
            ['semester' => '1', 'department' => 'BSHM', 'year' => '4TH YEAR', 'subcode' => 'HM402', 'subname' => 'Thesis/Research Project 1'],
            ['semester' => '2', 'department' => 'BSHM', 'year' => '4TH YEAR', 'subcode' => 'HM403', 'subname' => 'Thesis/Research Project 2'],
            ['semester' => '2', 'department' => 'BSHM', 'year' => '4TH YEAR', 'subcode' => 'HM404', 'subname' => 'On-the-Job Training'],

            // BSED - 1ST YEAR
            ['semester' => '1', 'department' => 'BSED', 'year' => '1ST YEAR', 'subcode' => 'ED101', 'subname' => 'Foundations of Education'],
            ['semester' => '1', 'department' => 'BSED', 'year' => '1ST YEAR', 'subcode' => 'PSYC101', 'subname' => 'General Psychology'],
            ['semester' => '1', 'department' => 'BSED', 'year' => '1ST YEAR', 'subcode' => 'ENG101', 'subname' => 'English for Academic Purposes'],
            ['semester' => '2', 'department' => 'BSED', 'year' => '1ST YEAR', 'subcode' => 'ED102', 'subname' => 'Child and Adolescent Development'],
            ['semester' => '2', 'department' => 'BSED', 'year' => '1ST YEAR', 'subcode' => 'MATH101', 'subname' => 'College Mathematics'],

            // BSED - 2ND YEAR
            ['semester' => '1', 'department' => 'BSED', 'year' => '2ND YEAR', 'subcode' => 'ED201', 'subname' => 'Principles of Teaching'],
            ['semester' => '1', 'department' => 'BSED', 'year' => '2ND YEAR', 'subcode' => 'CURR201', 'subname' => 'Curriculum Development'],
            ['semester' => '2', 'department' => 'BSED', 'year' => '2ND YEAR', 'subcode' => 'ASSESS201', 'subname' => 'Educational Assessment'],
            ['semester' => '2', 'department' => 'BSED', 'year' => '2ND YEAR', 'subcode' => 'ED202', 'subname' => 'Educational Psychology'],

            // BSED - 3RD YEAR
            ['semester' => '1', 'department' => 'BSED', 'year' => '3RD YEAR', 'subcode' => 'ED301', 'subname' => 'Classroom Management'],
            ['semester' => '1', 'department' => 'BSED', 'year' => '3RD YEAR', 'subcode' => 'SPED301', 'subname' => 'Special Education'],
            ['semester' => '2', 'department' => 'BSED', 'year' => '3RD YEAR', 'subcode' => 'TECH301', 'subname' => 'Educational Technology'],
            ['semester' => '2', 'department' => 'BSED', 'year' => '3RD YEAR', 'subcode' => 'ED302', 'subname' => 'Research in Education'],

            // BSED - 4TH YEAR
            ['semester' => '1', 'department' => 'BSED', 'year' => '4TH YEAR', 'subcode' => 'ED401', 'subname' => 'Student Teaching/Practice Teaching'],
            ['semester' => '1', 'department' => 'BSED', 'year' => '4TH YEAR', 'subcode' => 'ED402', 'subname' => 'Action Research'],
            ['semester' => '2', 'department' => 'BSED', 'year' => '4TH YEAR', 'subcode' => 'ED403', 'subname' => 'Professional Development'],
            ['semester' => '2', 'department' => 'BSED', 'year' => '4TH YEAR', 'subcode' => 'ED404', 'subname' => 'School Leadership and Management'],

            // BEED - 1ST YEAR
            ['semester' => '1', 'department' => 'BEED', 'year' => '1ST YEAR', 'subcode' => 'EE101', 'subname' => 'Child Development and Learning'],
            ['semester' => '1', 'department' => 'BEED', 'year' => '1ST YEAR', 'subcode' => 'EE102', 'subname' => 'Teaching Fundamentals'],
            ['semester' => '1', 'department' => 'BEED', 'year' => '1ST YEAR', 'subcode' => 'MATH101', 'subname' => 'Mathematics for Elementary Teachers'],
            ['semester' => '2', 'department' => 'BEED', 'year' => '1ST YEAR', 'subcode' => 'EE103', 'subname' => 'Filipino Language Teaching'],
            ['semester' => '2', 'department' => 'BEED', 'year' => '1ST YEAR', 'subcode' => 'ENG101', 'subname' => 'English Language Teaching'],

            // BEED - 2ND YEAR
            ['semester' => '1', 'department' => 'BEED', 'year' => '2ND YEAR', 'subcode' => 'EE201', 'subname' => 'Elementary Curriculum and Instruction'],
            ['semester' => '1', 'department' => 'BEED', 'year' => '2ND YEAR', 'subcode' => 'LA201', 'subname' => 'Language Arts Methods'],
            ['semester' => '2', 'department' => 'BEED', 'year' => '2ND YEAR', 'subcode' => 'SCI201', 'subname' => 'Science Methods for Elementary'],
            ['semester' => '2', 'department' => 'BEED', 'year' => '2ND YEAR', 'subcode' => 'MATH201', 'subname' => 'Mathematics Methods for Elementary'],

            // BEED - 3RD YEAR
            ['semester' => '1', 'department' => 'BEED', 'year' => '3RD YEAR', 'subcode' => 'SS301', 'subname' => 'Social Studies Methods'],
            ['semester' => '1', 'department' => 'BEED', 'year' => '3RD YEAR', 'subcode' => 'ARTS301', 'subname' => 'Arts Integration in Elementary'],
            ['semester' => '2', 'department' => 'BEED', 'year' => '3RD YEAR', 'subcode' => 'ASSESS301', 'subname' => 'Assessment in Elementary Education'],
            ['semester' => '2', 'department' => 'BEED', 'year' => '3RD YEAR', 'subcode' => 'EE301', 'subname' => 'Classroom Management for Elementary'],

            // BEED - 4TH YEAR
            ['semester' => '1', 'department' => 'BEED', 'year' => '4TH YEAR', 'subcode' => 'EE401', 'subname' => 'Student Teaching in Elementary'],
            ['semester' => '1', 'department' => 'BEED', 'year' => '4TH YEAR', 'subcode' => 'EE402', 'subname' => 'Action Research in Elementary Education'],
            ['semester' => '2', 'department' => 'BEED', 'year' => '4TH YEAR', 'subcode' => 'EE403', 'subname' => 'Professional Development for Elementary Teachers'],
            ['semester' => '2', 'department' => 'BEED', 'year' => '4TH YEAR', 'subcode' => 'EE404', 'subname' => 'Elementary School Administration']
        ];

        foreach ($subjects as $subject) {
            SemisSubject::create($subject);
        }
    }
}
