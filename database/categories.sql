create table categories
(
    slug varchar(30) unique not null,
    name varchar(30) not null,
    primary key(slug)
);