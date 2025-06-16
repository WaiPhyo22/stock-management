CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@example.com', 
'$2y$10$QdW4v4q8TJ0lr6zfhSOZBeJzymr6lqiMuh8eH4H/xzgzmROE57CQi', 'admin');

INSERT INTO users (name, email, password, role) VALUES
('Regular User', 'user@example.com', 
'$2y$10$AscxMIYpR82aELXmts4FY.saTNs28QCMYPd/RIr3D88N6sWkSRI.m', 'user');