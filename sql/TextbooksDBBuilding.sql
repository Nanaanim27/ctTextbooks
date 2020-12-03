CREATE DATABASE TextbookDB;

USE TextbookDB; 

CREATE TABLE Countries (
	NumericCode char(3) PRIMARY KEY,
	ShortCode char(2) NOT NULL,
	LongCode char(3) NOT NULL,
	Name varchar(255),
	CommonName varchar(255)
);

ALTER TABLE Countries ADD CommonName varchar(255);
ALTER TABLE Countries ALTER COLUMN Name varchar(255);

INSERT INTO Countries VALUES ('840', 'US', 'USA', 'The United States of America', 'United States of America');

SELECT * FROM Countries;

CREATE TABLE Universities (
	ID int AUTO_INCREMENT PRIMARY KEY,
	Name varchar(255) NOT NULL,
	Address varchar(255),
	City varchar(255),
	State varchar(255),
	Province varchar(255),
	PostalCode char(12)
);

INSERT INTO Universities (Name, Address, City, State, PostalCode) VALUES ('University of Akron', '302 Buchtel Common', 'Akron', 'OH', '44304');

SELECT * FROM Universities;

CREATE TABLE UniversityCountry (
	CountryID char(3),
	UniversityID int,
	PRIMARY KEY (CountryID, UniversityID),
	FOREIGN KEY (CountryID) REFERENCES Countries(NumericCode) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (UniversityID) REFERENCES Universities(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO UniversityCountry VALUES ('840', 1);

SELECT * FROM UniversityCountry;

CREATE TABLE Textbooks (
	ID int AUTO_INCREMENT PRIMARY KEY,
	Title varchar(255) NOT NULL,
	ISBN10 varchar(10),
	ISBN13 varchar(13),
	Edition int,
	PublishYear char(4)
);

INSERT INTO Textbooks (Title, ISBN13, Edition) VALUES ('Principles of Data Integration', '9780124160446', 1); 

SELECT * FROM Textbooks;

CREATE TABLE Authors (
	ID int AUTO_INCREMENT PRIMARY KEY,
	FirstName varchar(255),
	MiddleName varchar(255),
	LastName varchar(255) NOT NULL
);

INSERT INTO Authors (FirstName, LastName) VALUES ('AnHai', 'Doan');
INSERT INTO Authors (FirstName, LastName) VALUES ('Alon', 'Halevy');
INSERT INTO Authors (FirstName, LastName) VALUES ('Zachary', 'Ives');

SELECT * FROM Authors;

CREATE TABLE TextbookAuthors (
	TextbookID int,
	AuthorID int,
	PRIMARY KEY (TextbookID, AuthorID),
	FOREIGN KEY (TextbookID) REFERENCES Textbooks(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (AuthorID) REFERENCES Authors(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO TextbookAuthors VALUES (1, 1);
INSERT INTO TextbookAuthors VALUES (1, 2);
INSERT INTO TextbookAuthors VALUES (1, 3);

SELECT * FROM TextbookAuthors;

CREATE TABLE Users (
	ID int AUTO_INCREMENT PRIMARY KEY,
	Username varchar(255) UNIQUE NOT NULL,
	FirstName varchar(255),
	LastName varchar(255)
);

INSERT INTO Users (Username, FirstName, LastName) VALUES ('naa70', 'Nana', 'Anim');

CREATE TABLE UserUniversities (
	UserID int,
	UniversityID int,
	Role char(7),
	PRIMARY KEY (UserID, UniversityID, Role),
	FOREIGN KEY (UserID) REFERENCES Users(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (UniversityID) REFERENCES Universities(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO UserUniversities VALUES (1, 1, 'Student');
INSERT INTO UserUniversities VALUES (1, 1, 'Teacher');

SELECT * FROM UserUniversities;

CREATE TABLE UserTextbooks (
	UserID int,
	TextbookID int,
	PRIMARY KEY (UserID, TextbookID),
	FOREIGN KEY (UserID) REFERENCES Users(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (TextbookID) REFERENCES Textbooks(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO UserTextbooks VALUES (1, 1);

SELECT * FROM UserTextbooks;

CREATE TABLE Classes (
	ID int AUTO_INCREMENT PRIMARY KEY,
	Name varchar(255) NOT NULL,
	Department varchar(255),
	CourseNumber varchar(255)
);

INSERT INTO Classes (Name, Department, CourseNumber) VALUES
	('Applied Data Mining', 'Computer Information Systems', '2440:450'),
	('Data Integration', 'Computer Science', '3460:678');

SELECT * FROM Classes;

CREATE TABLE UniversityClasses (
	UniversityID int,
	ClassID int,
	PRIMARY KEY (UniversityID, ClassID),
	FOREIGN KEY (UniversityID) REFERENCES Universities(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (ClassID) REFERENCES Classes(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO UniversityClasses VALUES (1, 1), (1, 2);

SELECT * FROM UniversityClasses;

CREATE TABLE Instructors (
	ID int AUTO_INCREMENT PRIMARY KEY,
	FirstName varchar(255),
	LastName varchar(255)
);

INSERT INTO Instructors (FirstName, LastName) VALUES ('Zarreen', 'Farooqi'), ('En', 'Cheng'), ('John', 'Nicholas'), ('Zhong-Hui', 'Duan');

SELECT * FROM Instructors;

CREATE TABLE UniversityInstructors (
	UniversityID int,
	InstructorID int,
	PRIMARY KEY (UniversityID, InstructorID),
	FOREIGN KEY (UniversityID) REFERENCES Universities(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (InstructorID) REFERENCES Instructors(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO UniversityInstructors VALUES (1, 1), (1, 2), (1, 3), (1, 4);

SELECT * FROM UniversityInstructors;

CREATE TABLE ClassInstructors (
	ID int AUTO_INCREMENT PRIMARY KEY,
	ClassID int,
	InstructorID int,
	FOREIGN KEY (ClassID) REFERENCES Classes(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (InstructorID) REFERENCES Instructors(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO ClassInstructors (ClassID, InstructorID) VALUES (2, 2);

SELECT * FROM ClassInstructors;

CREATE TABLE UniversityClassTextbookInstructor (
	ID int AUTO_INCREMENT PRIMARY KEY,
	UniversityID int,
	ClassInstructorID int,
	TextbookID int,
	FOREIGN KEY (UniversityID) REFERENCES Universities(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (ClassInstructorID) REFERENCES ClassInstructors(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (TextbookID) REFERENCES Textbooks(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO UniversityClassTextbookInstructor (UniversityID, ClassInstructorID, TextbookID) VALUES (1, 1, 1);

SELECT * FROM UniversityClassTextbookInstructor;

CREATE TABLE Semester (
	ID int AUTO_INCREMENT PRIMARY KEY,
	Term varchar(6) NOT NULL,
	Year int NOT NULL
);

CREATE TABLE TextbookSemesterUse (
	UCTI_ID int,
	SemesterID int,
	PRIMARY KEY (UCTI_ID, SemesterID),
	FOREIGN KEY (UCTI_ID) REFERENCES UniversityClassTextbookInstructor(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (SemesterID) REFERENCES Semester(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO TextbookSemesterUse VALUES (1, 'AUTUMN', 2020);

SELECT * FROM TextbookSemesterUse;

SELECT Name FROM Textbooks;

CREATE TABLE Format (
	ID int AUTO_INCREMENT PRIMARY KEY,
	Name varchar(255) NOT NULL
);

CREATE TABLE TextbookFormats (
	TextbookID int,
	FormatID int,
	PRIMARY KEY (TextbookID, FormatID),
	FOREIGN KEY (TextbookID) REFERENCES Textbooks(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (FormatID) REFERENCES Format(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Topic (
	ID int IDENTITY(1, 1) PRIMARY KEY,
	Name varchar(255) UNIQUE NOT NULL
);

CREATE TABLE TextbookTopics (
	TextbookID int,
	TopicID int,
	PRIMARY KEY (TextbookID, TopicID),
	FOREIGN KEY (TextbookID) REFERENCES Textbooks(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (TopicID) REFERENCES Topic(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE ClassTopics (
	ClassID int,
	TopicID int,
	PRIMARY KEY (TextbookID, TopicID),
	FOREIGN KEY (ClassID) REFERENCES Class(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (TopicID) REFERENCES Topic(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Price (
	ID int AUTO_INCREMENT PRIMARY KEY, 
	Title varchar(255) NOT NULL, 
	Currency varchar(255), 
	Price real NOT NULL
);

CREATE TABLE Store (
	ID int AUTO_INCREMENT PRIMARY KEY, 
	Name varchar(255) NOT NULL UNIQUE, 
	URL varchar(255) NOT NULL UNIQUE
);

CREATE TABLE StorePrice (
	StoreID int,
	ListingID int,
	PriceID int,
	PRIMARY KEY (StoreID, ListingID, PriceID),
	FOREIGN KEY (StoreID) REFERENCES Store(ID) ON DELETE CASCADE ON UPDATE CASCADE, 
	FOREIGN KEY (ListingID) REFERENCES ListingURL(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (PriceID) REFERENCES Price(ID) ON DELETE CASCADE ON UPDATE CASCADE 
);

CREATE TABLE ListingURL (
	ID int AUTO_INCREMENT PRIMARY KEY, 
	URL varchar(255) NOT NULL
);

CREATE TABLE Passwords (
	ID int AUTO_INCREMENT PRIMARY KEY, 
	EncryptedPassword varchar(255) NOT NULL, 
	EncryptionHash varchar(255) NOT NULL, 
	CreationDate date NOT NULL
);

CREATE TABLE UserPasswords (
	UserID int, 
	PasswordID int,
	PRIMARY KEY (UserID, PasswordID),
	FOREIGN KEY (UserID) REFERENCES Users(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (PasswordID) REFERENCES Passwords(ID) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE Reviews (
	ID int AUTO_INCREMENT PRIMARY KEY,
	BookUsed bit,
	Required bit,
	Recommended bit,
	Rating int,
	ReviewDate date
);

CREATE TABLE UserReviews (
	UserID int,
	ReviewID int,
	PRIMARY KEY (UserID, ReviewID),
	FOREIGN KEY (UserID) REFERENCES Users(ID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (ReviewID) REFERENCES Reviews(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

