-- Drop the database if it exists to ensure a clean slate
DROP DATABASE IF EXISTS 1insul_in_sync;

-- Create the database
CREATE DATABASE 1insul_in_sync;

-- Use the database
USE 1insul_in_sync;

-- Create patients table
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    acctype ENUM('patient', 'care_giver') NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    dob VARCHAR(255) NOT NULL,
    insulin VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    height VARCHAR(255) NOT NULL,
    weight VARCHAR(255) NOT NULL,
    conditions TEXT NOT NULL,
    address TEXT NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    em_name VARCHAR(100) NOT NULL,
    em_address TEXT NOT NULL,
    em_phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create care_givers table
CREATE TABLE care_givers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    acctype ENUM('patient', 'care_giver') NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    qualification ENUM('nurse', 'doctor', 'family', 'other') NOT NULL,
    experience INT NOT NULL,
    specialization VARCHAR(100),
    address TEXT NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create patient_glucose table
CREATE TABLE patient_glucose (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    glucose VARCHAR(255) NOT NULL,
    time DATETIME NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- Create patient_pressure table
CREATE TABLE patient_pressure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    systolic VARCHAR(255) NOT NULL,
    diastolic VARCHAR(255) NOT NULL,
    heart_rate VARCHAR(255) NOT NULL,
    time DATETIME NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- Create patient_cholesterol table
CREATE TABLE patient_cholesterol (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    hdl VARCHAR(255) NOT NULL,
    ldl VARCHAR(255) NOT NULL,
    triglycerides VARCHAR(255) NOT NULL,
    total_cholesterol VARCHAR(255) NOT NULL,
    time DATETIME NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- Create grant_access table
CREATE TABLE grant_access (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caregiver_id INT NOT NULL,
    patient_id INT NOT NULL,
    access_glucose BOOLEAN DEFAULT FALSE,
    access_pressure BOOLEAN DEFAULT FALSE,
    access_cholesterol BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (caregiver_id) REFERENCES care_givers(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    UNIQUE KEY unique_caregiver_patient (caregiver_id, patient_id)
);
