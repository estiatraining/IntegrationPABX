/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     31/05/2010 16:05:24                          */
/*==============================================================*/


drop table if exists USUARIO;

/*==============================================================*/
/* Table: USUARIO                                               */
/*==============================================================*/
create table USUARIO
(
   USRID                int not null AUTO_INCREMENT,
   USRNOME              varchar(256) not null,
   USRSTS               char(1) not null,
   USRSENHA             varchar(256) not null,
   USRLOGIN             varchar(256) not null,
   primary key (USRID)
);

alter table USUARIO comment 'tabela de dados do usuario';


drop table if exists DADOSFILTRADOSCONVERTIDOS;

/*==============================================================*/
/* Table: DADOSFILTRADOSCONVERTIDOS                             */
/*==============================================================*/
create table DADOSFILTRADOSCONVERTIDOS
(
   DAFCID               integer not null AUTO_INCREMENT,
   DATALIGACAO          date not null,
   HORAINICIOLIGACAO    time,
   HORAINICIOATENDIMENTO time,
   HORAFIMLIGACAO       time,
   ATENDENTE            varchar(256) not null,
   RAMAL                varchar(50) not null,
   TEMPOATENDIMENTO     time,
   TEMPOLIGACAO         time,
   OBSERVACAO           text,
   NUMEROTELEFONE       varchar(256),
   NUMEROTRANSFERIDO    varchar(256),
   NOMETRANSFERIDO      varchar(256),
   NOMECLIENTE          varchar(256),
   IDENTIFICADORCHAMADA varchar(256),
   ORIGINALCAUSA        varchar(10),
   DESTINOCAUSA         varchar(10),
   ARQUIVO         varchar(256),
   primary key (DAFCID)
);

alter table DADOSFILTRADOSCONVERTIDOS comment 'Dados que foram retirados da tabela dadosArquivo mas que for';


CREATE INDEX indexData ON dadosfiltradosconvertidos (
   `DAFCID`             ASC,
   `DATALIGACAO`        ASC,
   `HORAINICIOATENDIMENTO` ASC,
    `HORAFIMLIGACAO`     ASC
);
CREATE INDEX indexRamal ON dadosfiltradosconvertidos (
   `DAFCID`             ASC,
   `RAMAL`        ASC
);
CREATE INDEX indexDestCausa ON dadosfiltradosconvertidos (
   `DAFCID`             ASC,
   `DESTINOCAUSA`        ASC
);

