-- Create Departments Table
create table departments
(
    department_id INT PRIMARY KEY AUTO_INCREMENT,  
    department_name VARCHAR(50) NOT NULL,
    head_of_department VARCHAR(10) NOT NULL
);

-- Create Employee Details Table
create table employee_details
(   
    employee_id INT PRIMARY KEY AUTO_INCREMENT,  
    employee_fullname VARCHAR(50) NOT NULL,
    age INT NOT NULL,
    gender CHAR(1) NOT NULL,  
    department_id INT NOT NULL,  
    employee_type VARCHAR(15) NOT NULL,
    date_of_joining DATE NOT NULL
);

-- Create Patient Records Table
create table patient_records
(
    patient_id INT PRIMARY KEY AUTO_INCREMENT,  
    patient_fullname VARCHAR(50) NOT NULL,
    record_last_updated_on DATE NOT NULL,
    age INT NOT NULL,
    gender CHAR(1) NOT NULL,  
    address VARCHAR(50) NOT NULL,
    recent_treatment_or_diagnosis VARCHAR(50) NOT NULL
);

-- Create Appointments Table
create table appointments
(
    appointment_id INT PRIMARY KEY AUTO_INCREMENT,  
    patient_id INT NOT NULL,  
    age INT NOT NULL,
    gender CHAR(1) NOT NULL,  
    address VARCHAR(50) NOT NULL,
    date_of_appointment DATE NOT NULL,
    time_of_appointment TIME NOT NULL,
    doctor_id INT NOT NULL,  
    concern VARCHAR(50) NOT NULL
);

-- Create Diagnosis and Treatments Table
create table diagnosis_treatments
(
    patient_id INT PRIMARY KEY,  
    treatment_diagnosis VARCHAR(40) NOT NULL,
    treated_by INT NOT NULL,  
    treated_on DATE NOT NULL
);

-- Step 2: Add Foreign Key Constraints

-- Add foreign key to employee_details for department_id
ALTER TABLE employee_details 
ADD FOREIGN KEY (department_id) REFERENCES departments(department_id);

-- Add foreign key to appointments for doctor_id
ALTER TABLE appointments 
ADD FOREIGN KEY (doctor_id) REFERENCES employee_details(employee_id);

-- Add foreign key to appointments for patient_id
ALTER TABLE appointments 
ADD FOREIGN KEY (patient_id) REFERENCES patient_records(patient_id);

-- Add foreign key to diagnosis_treatments for treated_by (doctor)
ALTER TABLE diagnosis_treatments 
ADD FOREIGN KEY (treated_by) REFERENCES employee_details(employee_id);

-- Add foreign key to diagnosis_treatments for patient_id
ALTER TABLE diagnosis_treatments 
ADD FOREIGN KEY (patient_id) REFERENCES patient_records(patient_id);


