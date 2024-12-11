CREATE TABLE JobCategory (
    CategoryID INT AUTO_INCREMENT PRIMARY KEY, -- Unique identifier for each category
    CategoryName VARCHAR(255) NOT NULL UNIQUE  -- Name of the job category
);

-- Insert predefined job categories
INSERT INTO JobCategory (CategoryName)
VALUES
    ('Technology and IT'),
    ('Healthcare and Medicine'),
    ('Education'),
    ('Finance and Business'),
    ('Construction and Engineering'),
    ('Creative and Design'),
    ('Science and Research'),
    ('Retail and Sales'),
    ('Manufacturing and Logistic'),
    ('Agriculture and Natural Resources'),
    ('Government and Non Profit');


CREATE TABLE Jobs (
    JobID INT AUTO_INCREMENT PRIMARY KEY,            -- Unique identifier for each job
    JobTitle VARCHAR(255) NOT NULL,                  -- Title of the job
    JobDescription TEXT NOT NULL,                    -- Description of the job
    CompanyName VARCHAR(255) NOT NULL,               -- Name of the company
    Location VARCHAR(255) NOT NULL,                  -- Job location
    SalaryRange VARCHAR(100),                        -- Salary range for the job
    PostingDate DATE NOT NULL DEFAULT CURRENT_DATE,  -- Date the job was posted
    ExpiryDate DATE NOT NULL,                        -- Expiry date for the job posting
    EmploymentType ENUM('Full-time', 'Part-time', 'Contract', 'Temporary', 'Internship', 'Freelance') NOT NULL, -- Type of employment
    Status ENUM('Active', 'Closed') NOT NULL DEFAULT 'Active', -- Status of the job posting
    CategoryID INT,                                  -- Foreign key to JobCategory
    UserID INT,                                      -- Foreign key to Users (who posted the job)
    FOREIGN KEY (CategoryBy) REFERENCES JobCategory(CategoryID),
    FOREIGN KEY (CreatedByHR) REFERENCES Users(UserID) 
);

CREATE TABLE JobApplicationForm (
    ApplicationID INT AUTO_INCREMENT PRIMARY KEY,              -- Unique identifier for each application
    Firstname VARCHAR(100) NOT NULL,                           -- Applicant's first name
    Lastname VARCHAR(100) NOT NULL,                            -- Applicant's last name
    Email VARCHAR(150) NOT NULL,                               -- Applicant's email
    PhoneNumber VARCHAR(15) NOT NULL,                          -- Applicant's phone number
    AppliedPosition VARCHAR(255) NOT NULL,                     -- Job position applied for
    EarliestStartDate DATE NOT NULL,                           -- Earliest possible start date
    CoverLetter TEXT NOT NULL,                                 -- Cover letter content
    ResumePath VARCHAR(255) NOT NULL,                          -- Path to the uploaded resume file
    DateApplied TIMESTAMP DEFAULT CURRENT_TIMESTAMP,           -- Automatically set the application submission date
    JobID INT NOT NULL,                                        -- Job ID (foreign key from Jobs table)
    CreatedByApplicant INT NOT NULL,                           -- Applicant's UserID (foreign key from Users table)
    CreatedByHRJob INT NOT NULL,                               -- HR's UserID (foreign key from Users table)

    -- Foreign key constraints
    FOREIGN KEY (JobID) REFERENCES Jobs(JobID),
    FOREIGN KEY (CreatedByApplicant) REFERENCES Users(UserID),
    FOREIGN KEY (CreatedByHRJob) REFERENCES Users(UserID)
);

CREATE TABLE Messages (
    MessageID INT AUTO_INCREMENT PRIMARY KEY,               -- Unique identifier for each message
    SenderUserID INT NOT NULL,                               -- The UserID of the sender (applicant or HR)
    ReceiverUserID INT NOT NULL,                             -- The UserID of the receiver (applicant or HR)
    MessageContent TEXT NOT NULL,                            -- The content of the message
    DateSent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,            -- Timestamp when the message was sent
    ApplicationID INT,                                       -- Optional: ApplicationID to link message to a job application
    FOREIGN KEY (SenderUserID) REFERENCES Users(UserID),    -- Foreign key linking to the Users table (sender)
    FOREIGN KEY (ReceiverUserID) REFERENCES Users(UserID),  -- Foreign key linking to the Users table (receiver)
    FOREIGN KEY (ApplicationID) REFERENCES JobApplicationForm(ApplicationID) -- Link to JobApplicationForm table
);



