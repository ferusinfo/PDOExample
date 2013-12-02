CREATE TABLE kat(
  id_kat INTEGER PRIMARY KEY AUTO_INCREMENT,
  nazwa VARCHAR(50) NOT NULL,
  id_nadkat INTEGER references kat(id_kat)
);

INSERT INTO kat VALUES(1,'Matematyka',NULL);
INSERT INTO kat VALUES(2,'Fizyka',NULL);
INSERT INTO kat VALUES(3,'Chemia',NULL);
INSERT INTO kat VALUES(4,'Algebra',1);
INSERT INTO kat VALUES(5,'Dzialania na potęgach',4);
INSERT INTO kat VALUES(6,'Dzialania na pierwiastkach',4);
INSERT INTO kat VALUES(7,'Pierwiastki drugiego stopnia',6);
INSERT INTO kat VALUES(8,'Pierwiastki trzeciego stopnia',6);
