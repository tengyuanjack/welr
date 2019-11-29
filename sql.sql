create database ocr default charset=utf8;

create table dictionary(
    id int(11) unsigned not null primary key auto_increment,
    chname varchar(200) not null default "",
    enname varchar(200) not null default ""
)engine=myisam default charset=utf8;


create table project(
	id int(11) unsigned not null primary key auto_increment,
    name varchar(200) not null default "",
    progress tinyint(10) not null default 0, # 进展
    unit varchar(200) not null default "", # 开发单位
    secret tinyint(10) not null default 0 # 秘级
)engine=myisam default charset=utf8;

create table artifact(
	id int(11) unsigned not null primary key auto_increment,  
	pid int(11) unsigned not null default 0, # 隶属项目  
    class tinyint(5) not null default 0,  # 分类 lifecycle的外键
    content varchar(2000) not null default "" # 存储时需要htmlspecial先转化一下
)engine=myisam default charset=utf8;

create table lifecycle(
	id int(11) unsigned not null primary key auto_increment,
    name varchar(200) not null default ""
)engine=myisam default charset=utf8;

insert into project(name) values("涡轮发动机项目");

insert into lifecycle(name) values("系统需求");
insert into lifecycle(name) values("高级需求");
insert into lifecycle(name) values("低级需求");

########### 实验用  ###########
create table data_vector(
    id int(11) unsigned not null primary key auto_increment,
    word varchar(200) not null,
    vector varchar(5000) not null,
    index(word)
)engine=myisam default charset=utf8;


create table text_similarity(
    id int(11) unsigned not null primary key auto_increment,
    source varchar(200) not null,
    target varchar(5000) not null,
    similarity varchar(200) not null,
    jaccard varchar(200) not null,
    dataset varchar(100) not null,
    ctime timestamp not null default current_timestamp
)engine=myisam default charset=utf8;

create table result_set(
    id int(11) unsigned not null primary key auto_increment,
    source varchar(200) not null,
    target varchar(5000) not null,
    similarity varchar(200) not null,
    haslink tinyint(1) not null default 0,
    dataset varchar(100) not null,
    ctime timestamp not null default current_timestamp
)engine=myisam default charset=utf8;


create table idf(
    id int(11) unsigned not null primary key auto_increment,
    word varchar(200) not null,
    idf_num varchar(5000) not null,
    dataset varchar(100) not null,
    ctime timestamp not null default current_timestamp
)engine=myisam default charset=utf8;


create table qe(
    id int(11) unsigned not null primary key auto_increment,
    word varchar(200) not null,
    qe_word varchar(200) not null,
    similarity varchar(200) not null,
    dataset varchar(100) not null,
    ctime timestamp not null default current_timestamp
)engine=myisam default charset=utf8;


create table proportion(
    id int(11) unsigned not null primary key auto_increment,
    filename varchar(200) not null,
    keywords varchar(2000) not null,
    dataset varchar(100) not null,
    ctime timestamp not null default current_timestamp
)engine=myisam default charset=utf8;

create table translation(
    id int(11) unsigned not null primary key auto_increment,
    aid varchar(200) not null, # 制品id
    pid varchar(200) not null, # 项目id
    words varchar(2000) not null,
    ctime timestamp not null default current_timestamp
)engine=myisam default charset=utf8;
########### 实验用 表结束  ###########

########### 系统通用表  ###########
create table user(
    id int(11) unsigned not null primary key auto_increment,
    nickname varchar(200) not null default "", 
    password varchar(200) not null default "",  # 密码 md5加密
    name varchar(200) not null default "",
    unit varchar(200) not null default "", # 单位
    position varchar(200) not null default "", # 职位
    remark varchar(2000) not null default "",
    ctime timestamp not null default current_timestamp
)engine=myisam default charset=utf8;

