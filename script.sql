drop database if exists shop;
create database shop character set utf8mb4;
use shop;

create table customer(
    id int not null primary key auto_increment,
    firstname varchar(50) not null,
    lastname varchar(50) not null,
    email varchar(255) not null,
    user_password char(60) not null,
    phonenumber varchar(15),
    street varchar(255),
    city varchar(50),
    postalnumber char(5),
    datecreated datetime not null,
    lastOnline datetime
);

create table category(
    id int not null primary key auto_increment,
    name varchar(50) not null,
    description varchar(255),
    lastUpdated datetime
);

create table manufacturer(
    id int not null primary key auto_increment,
    name varchar(50) not null,
    description varchar(255),
    lastUpdated datetime
);

create table product(
    id int not null primary key auto_increment,
    name varchar(255) not null,
    manufacturer int not null,
    description text,
    category int not null,
    price decimal(18,2),
    inventoryquantity int not null,
    dateadded datetime not null,
    lastUpdated datetime

);

create table shoppingorder(
    id int not null primary key auto_increment,
    customer int not null,
    dateadded datetime not null,
    isFinished boolean not null,
    dateFinished datetime
);

create table cart(
    id int not null primary key auto_increment,
    shoppingorder int not null,
    product int not null,
    price decimal(18,2),
    quantity int not null,
    dateadded datetime not null
);

create table operator(
    id           int not null primary key auto_increment,
    email           varchar(50) not null,
    user_password         char(60) not null, 
    firstname             varchar(50) not null,
    lastname         varchar(50) not null,
    user_role           varchar(10) not null
);


alter table product add foreign key (category) references category(id);
alter table product add foreign key (manufacturer) references manufacturer(id);
alter table shoppingorder add foreign key (customer) references customer(id);
alter table cart add foreign key (shoppingorder) references shoppingorder(id);
alter table cart add foreign key (product) references product(id);

-- Insert Admin and Operator
insert into operator(email,user_password,firstname,lastname, user_role) values
# lozinka a
('admin@edunova.hr','$2a$12$OZzCotkUOgkVKz9lxuFuGeRouCccsjl7klz9tY6sDryj/mv86XcKO','Administrator','Edunova','admin'),
# lozinka o
('oper@edunova.hr','$2a$12$S6vnHiwtRDdoUW4zgxApvezBlodWj/tmTADdmKxrX28Y2FXHcoHOm','Operater','Edunova','oper');

-- Temporary tables for inserting mock users
create table firstname(
    id int not null primary key auto_increment, 
    firstname varchar(20) not null
);

create table lastname(
    id int not null primary key auto_increment,
    lastname varchar(20) not null
);

create table city(
    id int not null primary key auto_increment,
    city varchar(20),
    postalnumber char(5)
);

-- Mock names, surnames and city
insert into firstname(firstname) values
('Roman'),
('Ivan'),
('Matija'),
('Dario'),
('Dunja'),
('Mirna'),
('Sa??a'),
('Lana'),
('Ljupka'),
('Vjera');

insert into lastname(lastname) values
('Kne??evi??'),
('Horvat'),
('Kova??evi??'),
('Pavlovi??'),
('Bla??evi??'),
('Bo??i??'),
('Lovri??'),
('Babi??'),
('Markovi??'),
('Bo??njak');

insert into city(city,postalnumber) values
('Osijek','31000'),
('Osijek','31000'),
('Osijek','31000'),
('Valpovo','31550'),
('Beli Manastir','31300'),
('??akovo','31400'),
('Beli????e','31551'),
('Donji Miholjac','31540'),
('Na??ice','31500');

-- Procedures to create mock users
-- Random city picker
-- Function is deterministic and only READS data
-- https://stackoverflow.com/questions/26015160/deterministic-no-sql-or-reads-sql-data-in-its-declaration-and-binary-logging-i
-- http://www.titov.net/2005/09/21/do-not-use-order-by-rand-or-how-to-get-random-rows-from-table/
drop function if exists randomcity;
DELIMITER $$
create function randomcity() returns varchar(20)
READS SQL DATA
DETERMINISTIC
begin
    return (select city from city order by rand() limit 1);
end;
$$
DELIMITER ;

-- Email generator with @mojatrgovina.com at the end
-- Function is deterministic and only READS data
-- https://stackoverflow.com/questions/26015160/deterministic-no-sql-or-reads-sql-data-in-its-declaration-and-binary-logging-i
drop function if exists emailfunction;
DELIMITER $$
create function emailfunction(firstname varchar(20), lastname varchar(20)) returns varchar(255)
READS SQL DATA
DETERMINISTIC
begin
    return concat(left(lower(firstname),1),'.', lower(replace(replace(replace(replace(replace(replace(upper(lastname),' ',''),'??','C'),'??','C'),'??','Z'),'??','S'),'??','D')), '@mojatrgovina.com');
end;
$$
DELIMITER ;

-- Customer creation procedure
-- PW is 'a'
drop procedure if exists customercreation;
DELIMITER $$
create procedure customercreation()
begin

    declare _firstname varchar(20);
    declare firstname_kraj int default 0;
    declare firstname_cursor cursor for select firstname from firstname order by id;    
    declare continue handler for not found set firstname_kraj = 1;
    
    open firstname_cursor;

    firstloop: loop

        fetch firstname_cursor into _firstname;

        if firstname_kraj=1 then leave firstloop;
        end if;

        BLOCK1: begin
        declare _lastname varchar(20);
        declare lastname_kraj int default 0;
        declare lastname_cursor cursor for select lastname from lastname order by id;
        declare continue handler for not found set lastname_kraj = 1;

        open lastname_cursor;

        secondloop: loop

            fetch lastname_cursor into _lastname;

            if lastname_kraj=1 then leave secondloop;
            end if;

            insert into customer(id,firstname,lastname,email, user_password, city,datecreated) values
            (null,_firstname,_lastname,emailfunction(_firstname,_lastname),'$2a$12$gcFbIND0389tUVhTMGkZYem.9rsMa733t9J9e9bZcVvZiG3PEvSla',randomcity(),now());

        end loop secondloop;
        close lastname_cursor;
        set lastname_kraj=0;
        end BLOCK1;        
    
    end loop firstloop;

    close firstname_cursor;

end;
$$
DELIMITER ;

call customercreation();

-- Procedure to fill postal number in customer table
drop procedure if exists postalnumber;
DELIMITER $$
create procedure postalnumber()
begin
    declare _postalnumber char(5);
    declare _city varchar(20);
    declare _id int;
    declare kraj int default 0;
    declare customer_cursor cursor for select city, postalnumber from customer order by id;
    declare continue handler for not found set kraj=1;

    open customer_cursor;
    petlja: loop
        fetch customer_cursor into _city, _postalnumber;

        if kraj=1 then leave petlja;
        end if;

        if _postalnumber is not null then leave petlja;
        end if;

        set _postalnumber = (select distinct postalnumber from city where city=_city);

        update customer set postalnumber=_postalnumber where city=_city;

    end loop petlja;
    close customer_cursor;

end;
$$
DELIMITER ;

call postalnumber();

-- Clearing all temporary tables, functions and procedures
drop table firstname;
drop table lastname;
drop table city;
drop function emailfunction;
drop function randomcity;
drop procedure customercreation;
drop procedure postalnumber;

-- Insert Category
insert into category(id,name,description) values
(null,'Tipkovnica','Membranska,mehani??ka,??i??ana,be??i??na'),
(null,'Mi??','??i??ani,be??i??ni,ergonomski,standardni'),
(null,'Ku??i??te','sva ku??i??ta'),
(null,'Mati??na plo??a','Maticne ploce svi tipovi'),
(null,'Monitor','LCD,TFT,ravni,zakrivljeni'),
(null,'Ventilatori i hladnjaci','Ventilatori i ostali tipovi hla??enja'),
(null,'HDD i SSD','Tipovi diskova'),
(null,'Napajanje za kompjuter','Vrste napajanja'),
(null,'Memorija za ra??unala','RAM'),
(null,'Procesor','Rzyen i Intel');

-- Insert Manufacturer
insert into manufacturer(id,name,description) values
(null,'Redragon',''),
(null,'Logitech',''),
(null,'MS',''),
(null,'Gigabyte',''),
(null,'Xilence',''),
(null,'Hikvision',''),
(null,'Akyga',''),
(null,'Kingston',''),
(null,'Intel',''),
(null,'AMD','');

-- Insert Product
insert into product(id,name,manufacturer,description,category,price,inventoryquantity,dateadded) values
(null,'REDRAGON K530 PRO RGB',1,null,1,479.99,5,now()),
(null,'LOGITECH MX Master 3',2,null,2,750.99,5,now()),
(null,'MS Industrial ARMOR V700 gaming',3,null,3,429.99,5,now()),
(null,'GIGABYTE B450 Gaming X',4,null,4,669.99,5,now()),
(null,'GIGABYTE M27F-EK',4,null,5,1799.99,5,now()),
(null,'Xilence 40??40??10mm',5,null,6,14.99,5,now()),
(null,'Hikvision C100, 2.5"',6,null,7,299.99,5,now()),
(null,'Akyga AK-B1-500',7,null,8,299.99,5,now()),
(null,'Kingston KCP426NS6/4',8,null,9,179.99,5,now()),
(null,'Intel Core i3-10100F',9,null,10,749.99,5,now()),
(null,'AMD procesor',10,null,10,749.99,5,now());

-- To this point, we have 100 customers with ID 1-100
-- Creating 5 orders for each
-- 100 customers, 5 orders each, total 500 orders
drop procedure if exists create_shoppingorder;
delimiter $$
create procedure create_shoppingorder()
begin
	
	DECLARE kraj INT default 0;
	
petlja: loop
	IF kraj=500 then leave petlja;
	end if;	
	insert into shoppingorder(id,customer,dateadded,isFinished, dateFinished) 
	values (null,floor(rand()*100+1), now(),true, now());

	set kraj=kraj+1;
end loop petlja;	
end;
$$
delimiter ;

call create_shoppingorder();

-- For each order, 5 products in cart with max quantity of 3
-- Create 500 cart rows 
-- 500 order with 5 carts = 2500
drop procedure if exists create_cart;
delimiter $$
create procedure create_cart()
begin
	
	DECLARE kraj INT default 0;
	
    petlja: loop
        IF kraj=2500 then leave petlja;
        end if;	

        insert into cart(id,shoppingorder,product,quantity,dateadded) 
        values (null,floor(rand()*500+1),floor(rand()*10+1), floor(rand()*3+1), now());

        set kraj=kraj+1;
    end loop petlja;

end;
$$
delimiter ;

call create_cart();

-- Adding product.price and cart.quantity for cart.price total
drop procedure if exists cart_price;
delimiter $$
create procedure cart_price()
begin
    DECLARE kraj INT default 0;
    DECLARE _id INT;
    DECLARE cart_kursor cursor for select id from cart order by id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET kraj = 1;
        
    open cart_kursor;

    petlja: loop
    fetch cart_kursor into _id;

        IF kraj=1 
        then leave petlja;
        end if;

        update cart a 
        inner join product b on a.product = b.id
        set a.price = a.quantity * b.price
        where a.id = _id;
    
    end loop petlja;

    close cart_kursor;

END;
$$
delimiter ;

call cart_price();

-- Clearing procedures for orders, carts and cart.price
drop procedure create_shoppingorder;
drop procedure create_cart;
drop procedure cart_price;