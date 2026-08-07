package com.campus_care.Campus_Care_Backend.domain;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;
import org.springframework.data.mongodb.core.mapping.Field;

import java.time.Instant;
import java.time.LocalDateTime;

@Document(collection = "diarios")
public class Diario {

    @Id
    String id;
    @Field("id_usuario")
    private String idUsuario;

    @Field("fecha")
    private Instant fecha;

    @Field("entrada_texto")
    private String entradaTexto;

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
