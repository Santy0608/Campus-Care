package com.campus_care.Campus_Care_Backend.domain;

import org.springframework.data.mongodb.core.mapping.Field;

import java.time.LocalDateTime;

public class LogroObtenido {

    @Field("id_logro")
    private String idLogro;

    @Field("fecha_desbloqueo")
    private LocalDateTime fechaDesbloqueo;

    public String getIdLogro(){
        return idLogro;
    }

    public void setIdLogro(String idLogro){
        this.idLogro = idLogro;
    }

    public LocalDateTime getFechaDesbloqueo(){
        return fechaDesbloqueo;
    }

    public void setFechaDesbloqueo(LocalDateTime fechaDesbloqueo){
        this.fechaDesbloqueo = fechaDesbloqueo;
    }

}
