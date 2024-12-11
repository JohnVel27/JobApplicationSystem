CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY, -- Unique identifier for each user
    Firstname VARCHAR(100) NOT NULL,       -- Firstname of the user
    Lastname VARCHAR(100) NOT NULL,        -- Lastname of the user
    Address TEXT NOT NULL,                 -- Address of the user
    EmailAddress VARCHAR(150) NOT NULL UNIQUE, -- Email address (must be unique)
    UserRole ENUM('Applicant', 'HR') NOT NULL, -- User role (Applicant or HR)
    Username VARCHAR(50) NOT NULL UNIQUE,  -- Username for login (must be unique)
    Password VARCHAR(255) NOT NULL,        -- This will store the hashed password
    Confirm_Password VARCHAR(255) NOT NULL, -- Optional, can store a hashed confirm password
    Date_Added TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Automatically set the time when the row is added
);
