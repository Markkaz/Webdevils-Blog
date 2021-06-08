create table old_slugs
(
    slug varchar(70) unique not null,
    blogpost_id integer not null,

    primary key(slug),
    foreign key(blogpost_id) references blogposts(id)
);