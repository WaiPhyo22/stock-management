CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity_available INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, price, quantity_available) VALUES
('Lenovo ThinkPad X1 Carbon', 1500.00, 10),
('Dell XPS 13', 1350.00, 8),
('Apple MacBook Pro 14"', 2100.00, 5),
('HP EliteBook 840 G8', 1200.00, 12),
('ASUS ROG Strix G15', 1700.00, 6),
('Acer Aspire 7', 850.00, 14),
('MSI Modern 14', 900.00, 10),
('Logitech MX Master 3 Mouse', 100.00, 25),
('Razer BlackWidow Keyboard', 130.00, 18),
('Dell 24" Monitor', 200.00, 9),
('Samsung 27" Curved Monitor', 300.00, 7),
('Kingston 16GB RAM', 75.00, 20),
('WD 1TB External HDD', 65.00, 15),
('TP-Link WiFi Router', 40.00, 30),
('Canon PIXMA Inkjet Printer', 120.00, 6);