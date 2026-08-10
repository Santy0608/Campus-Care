package com.campus_care.Campus_Care_Backend.domain;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;
import org.springframework.data.mongodb.core.mapping.Field;

import java.time.Instant;
import java.time.LocalDate;
import java.util.Date;

@Document(collection = "diarios")
public class Diario {

    @Id
    private String id;
    @Field("id_usuario")
    private String idUsuario;

    @Field("fecha")
    private Instant fecha;

    @Field("entrada_texto")
    private String entradaTexto;

    public Diario() {
    }

    public String getId(){
        return id;
    }

    public void setId(String id){
        this.id = id;
    }

    public String getIdUsuario(){
        return idUsuario;
    }

    public void setIdUsuario(String idUsuario){
        this.idUsuario = idUsuario;
    }

    public Instant getFecha(){
        return fecha;
    }

    public void setFecha(Instant fecha){
        this.fecha = fecha;
    }

    public String getEntradaTexto(){
        return entradaTexto;
    }

    public void  setEntradaTexto(String entradaTexto){
        this.entradaTexto = entradaTexto;
    }



}
