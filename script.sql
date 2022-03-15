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
    datecreated datetime not null
);

create table category(
    id int not null primary key auto_increment,
    name varchar(50) not null,
    description varchar(255)
);

create table product(
    id int not null primary key auto_increment,
    name varchar(255) not null,
    description text,
    category int not null,
    price decimal(18,2),
    inventoryquantity int not null,
    dateadded datetime not null
);

create table productimage(
    id int not null primary key auto_increment,
    product int not null,
    imageurl varchar(255) not null,
    dateadded datetime not null
);

create table shoppingorder(
    id int not null primary key auto_increment,
    customer int not null,
    dateadded datetime not null
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
alter table productimage add foreign key (product) references product(id);
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
('Saša'),
('Lana'),
('Ljupka'),
('Vjera');

insert into lastname(lastname) values
('Knežević'),
('Horvat'),
('Kovačević'),
('Pavlović'),
('Blažević'),
('Božić'),
('Lovrić'),
('Babić'),
('Marković'),
('Bošnjak');

insert into city(city,postalnumber) values
('Osijek','31000'),
('Osijek','31000'),
('Osijek','31000'),
('Valpovo','31550'),
('Beli Manastir','31300'),
('Đakovo','31400'),
('Belišće','31551'),
('Donji Miholjac','31540'),
('Našice','31500');

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
    return concat(left(lower(firstname),1),'.', lower(replace(replace(replace(replace(replace(replace(upper(lastname),' ',''),'Č','C'),'Ć','C'),'Ž','Z'),'Š','S'),'Đ','D')), '@mojatrgovina.com');
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
(null,'Tipkovnica','Membranska,mehanička,žičana,bežična'),
(null,'Miš','Žičani,bežični,ergonomski,standardni'),
(null,'Kućište','sva kućišta'),
(null,'Matična ploča','Maticne ploce svi tipovi'),
(null,'Monitor','LCD,TFT,ravni,zakrivljeni'),
(null,'Ventilatori i hladnjaci','Ventilatori i ostali tipovi hlađenja'),
(null,'HDD i SSD','Tipovi diskova'),
(null,'Napajanje za kompjuter','Vrste napajanja'),
(null,'Memorija za računala','RAM'),
(null,'Procesor','Rzyen i Intel');

-- Insert Product
insert into product(id,name,description,category,price,inventoryquantity,dateadded) values
(null,'REDRAGON K530 PRO RGB',null,1,479.99,5,now()),
(null,'LOGITECH MX Master 3',null,2,750.99,5,now()),
(null,'MS Industrial ARMOR V700 gaming',null,3,429.99,5,now()),
(null,'GIGABYTE B450 Gaming X',null,4,669.99,5,now()),
(null,'GIGABYTE M27F-EK',null,5,1799.99,5,now()),
(null,'Xilence 40×40×10mm',null,6,14.99,5,now()),
(null,'Hikvision C100, 2.5"',null,7,299.99,5,now()),
(null,'Akyga AK-B1-500',null,8,299.99,5,now()),
(null,'Kingston KCP426NS6/4',null,9,179.99,5,now()),
(null,'Intel Core i3-10100F',null,10,749.99,5,now());

-- Insert product images
insert into productimage(id,product,imageurl,dateadded) values
(null,1,'https://www.links.hr/content/images/thumbs/009/0096739_tipkovnica-redragon-draconic-k530-rgb-mehanicka-bezicna-usb-us-layout-crna-101200626.png','2021-11-26 13:05'),
(null,5,'https://www.links.hr/content/images/thumbs/010/0104932_monitor-27-gigabyte-m27f-ek-kvm-gaming-monitor-ips-144hz-1ms-300cd-m2-1000-1-crni-100300841.png','2021-11-26 13:49'),
(null,2,'https://www.links.hr/content/images/thumbs/006/0068425_mis-logitech-mx-master-3-laserski-bezicni-bt-unifying-receiver-usb-graphite.jpg','2021-11-26 13:49'),
(null,3,'https://www.links.hr/content/images/thumbs/011/0112952_kuciste-ms-industrial-armor-v700-gaming-midi-atx-window-crno-bez-napajanja.jpg',now()),
(null,4,'https://www.links.hr/content/images/thumbs/007/0076249_maticna-ploca-gigabyte-b450-gaming-x-amd-b450-ddr4-atx-s-am4-050300477.jpg',now()),
(null,6,'https://www.instar-informatika.hr/slike/velike/ventilator-za-kuciste-xilence-40x40x10mm-35960_1.jpg',now()),
(null,7,'https://www.instar-informatika.hr/slike/velike/ssd-hikvision-c100-240gb-25-sata-3-6gb-s-hks-ssd-c100-240g_1.jpg',now()),
(null,8,'https://www.instar-informatika.hr/slike/velike/akb1500.jpg',now()),
(null,9,'https://www.instar-informatika.hr/slike/velike/memorija-kingston-ddr4-2666mhz-4gb-brand-king-kcp426ns6-4gb_1.jpg',now()),
(null,10,'https://www.instar-informatika.hr/slike/velike/procesor-intel-core-i3-10100f-36ghz-6mb--inp-000151_1.jpg',now());
