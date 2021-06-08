create table blogposts_authors
(
    blogpost_id integer not null,
    author_name varchar(30) not null,

    primary key(blogpost_id, author_name),
    foreign key(blogpost_id) references blogposts(id),
    foreign key(author_name) references authors(name)
);