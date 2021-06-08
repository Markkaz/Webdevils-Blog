create table blogposts
(
    id serial not null,
    slug varchar(70) unique not null,
    category varchar(30) not null,
    title varchar(70) not null,
    introduction text not null,
    content text not null,
    publish_date timestamp,
    status varchar(30) not null,
    parser varchar(30) not null,

    primary key(id),
    foreign key(category) references categories(slug)
)